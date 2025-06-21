<?php

namespace Blerify\Notifications;

use Blerify\Exception\AuthenticationException;
use Blerify\Exception\HttpRequestException;
use Exception;
use Ramsey\Uuid\Uuid;

class ApiClient
{
    private $endpointBase;
    private $jwtHandler;

    public function __construct($endpointBase, $jwtHandler)
    {
        $this->endpointBase = $endpointBase;
        $this->jwtHandler = $jwtHandler;
    }

    public function request($method, $path, $data = [], $correlationId =  null)
    {
        $correlationId = $correlationId ?? Uuid::uuid4()->toString();
        $url = $this->endpointBase . $path;

        // Get access token
        $accessToken = $this->jwtHandler->getAccessToken();

        // Prepare cURL request
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
            'correlation-id: ' . $correlationId
        ]);

        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode === 401) {
            throw new AuthenticationException("Authentication failed", 401, json_decode($response, true));
        }

        if ($httpCode >= 400) {
            throw new HttpRequestException("HTTP request failed", $httpCode, json_decode($response, true));
        }

        curl_close($ch);

        return $response;
    }

    public function call($data = [], $correlationId = null, $path, $method)
    {
        try {
            $response = $this->request(
                $method,
                $path,
                $data,
                $correlationId
            );
            $response = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $msg = 'JSON decode error: ' . json_last_error_msg();
                return ["error" => true, "message" => $msg, "code" => 30000];
            }
            return ["error" => false, "data" => $response];
        } catch (HttpRequestException | AuthenticationException $e) {
            return ["error" => true, "message" => $e->getMessage(), "details" => $e->getDetails(), "code" => $e->getCode()];
        } catch (Exception $e) {
            return ["error" => true, "message" => $e->getMessage(), "details" => [], "code" => $e->getCode()];
        }
    }
}
