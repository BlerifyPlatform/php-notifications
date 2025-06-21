<?php

namespace Blerify\Authentication;

use Exception;
use Firebase\JWT\JWT;
use Ramsey\Uuid\Uuid;

class JwtHandler
{
    private $clientId;
    private $privateKey;
    private $organizationId;
    private $tokenUri;

    private $cachedJwt;
    private $cachedExpiration;

    private $audience;

    public function __construct($clientId, $organizationId, $privateKey, $tokenUri, $audience)
    {
        $this->clientId = $clientId;
        $this->organizationId = $organizationId;
        $this->privateKey = $privateKey;
        $this->tokenUri = $tokenUri;
        $this->cachedJwt = null;
        $this->cachedExpiration = null;
        $this->audience = $audience;
    }

    public static function new($path): JwtHandler
    {
        if (!file_exists($path)) {
            throw new Exception("Config file not found: $path");
        }
        $credentials = json_decode(file_get_contents($path), true);
        $organizationId = $credentials['organization_id'];
        $jwtHandler = new JwtHandler(
            $credentials['client_id'],
            $organizationId,
            $credentials['private_key'],
            $credentials['token_uri'],
            $credentials['iam_audience']
        );
        return $jwtHandler;
    }

    public function getOrganizationId(): string
    {
        return $this->organizationId;
    }

    public function createJwt($audience)
    {
        $now = time();
        $payload = [
            'iss' => $this->clientId,
            'sub' => $this->clientId,
            'aud' => $audience,
            'iat' => $now,
            'exp' => $now + 3600,
            'jti' => Uuid::uuid4()->toString(),
        ];

        return JWT::encode($payload, $this->privateKey, 'RS256');
    }

    public function getAccessToken()
    {
        if ($this->cachedJwt && $this->cachedExpiration > (time() + 60)) {
            return $this->cachedJwt;
        }
        $jwt = $this->createJwt($this->audience);

        $ch = curl_init($this->tokenUri);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'client_id' => $this->clientId,
            'organization_id' => $this->organizationId,
            'client_assertion' => $jwt,
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
        ]);
        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($statusCode !== 200) {
            throw new Exception("Failed to get access token. Status code: $statusCode");
        }
        $tokenResponse = json_decode($response, true);
        if (!isset($tokenResponse['access_token'])) {
            throw new Exception("Access token not found in response.");
        }
        // Cache the JWT and its expiration time
        $this->cachedJwt = $tokenResponse['access_token'];
        $this->cachedExpiration = $this->getJwtExp($jwt);

        return $this->cachedJwt;
    }

    public function getJwtExp($jwt)
    {
        $parts = explode(".", $jwt);

        if (count($parts) !== 3) {
            throw new Exception("Invalid JWT format");
        }

        $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);

        $exp = (int) $payload['exp'];
        if (!isset($exp)) {
            throw new Exception("Invalid JWT: 'exp' not valid");
        }

        return $exp;
    }
}
