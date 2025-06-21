<?php

namespace Blerify\Model\Response;

use JsonSerializable;

class GetNotificationItemResponse
{
    private $id;
    private $notificationGroupId;
    private $receiverData;
    private $status;
    private $createdAt;
    private $sentAt;
    private $receivedAt;
    private $readAt;
    public static function fromArray(array $data): self
    {
        $response = new self();
        $response->id = $data['id'] ?? null; //Credential::fromArray($data['credential']) ?? null;
        $response->notificationGroupId = $data['notificationGroupId'] ?? null;
        $response->receiverData = $data['receiverData'] ?? null;
        $response->status = $data['status'] ?? null;
        $response->createdAt = $data['createdAt'] ?? null;
        $response->sentAt = $data['sentAt'] ?? null;
        $response->receivedAt = $data['receivedAt'] ?? null;
        $response->readAt = $data['readAt'] ?? null;
        return $response;
    }

    public function getId(): self
    {
        return $this->id;
    }
    public function getnotificationGroupId(): ?string
    {
        return $this->notificationGroupId;
    }

    public function getReceiverData(): object
    {
        return $this->receiverData;
    }
    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getSentAt(): string
    {
        return $this->sentAt;
    }

    public function getReceivedAt(): string
    {
        return $this->receivedAt;
    }

    public function getReadAt(): string
    {
        return $this->readAt;
    }

    public function jsonSerialize(): array
    {
        if ($this->id !== null) {
            $data['id'] = $this->id;
        }

        if ($this->notificationGroupId !== null) {
            $data['notificationGroupId'] = $this->notificationGroupId;
        }
        if ($this->receiverData !== null) {
            $data['receiverData'] = $this->receiverData;
        }
        if ($this->status !== null) {
            $data['status'] = $this->status;
        }
        if ($this->createdAt !== null) {
            $data['createdAt'] = $this->createdAt;
        }
        if ($this->sentAt !== null) {
            $data['sentAt'] = $this->sentAt;
        }
        if ($this->receivedAt !== null) {
            $data['receivedAt'] = $this->receivedAt;
        }
        if ($this->readAt !== null) {
            $data['readAt'] = $this->readAt;
        }
        return $data;
    }
}
