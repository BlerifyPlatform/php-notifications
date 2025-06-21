<?php

namespace Blerify\Model\Response;

use JsonSerializable;

class CreateNotificationBulkResponse implements JsonSerializable
{
    private int $total;
    private int $limit;
    private int $offset;

    /** @var NotificationItemResponse[] */
    private array $items = [];

    public static function fromArray(array $data): self
    {
        $response = new self();
        $response->total = $data['total'] ?? null; //Credential::fromArray($data['credential']) ?? null;
        $response->limit = $data['limit'] ?? null;
        $response->offset = $data['offset'] ?? null;
        $response->items = [];
        foreach ($data['items'] ?? [] as $item) {
            $response->items[] = NotificationItemResponse::fromArray($item);
        }
        echo "encoded is::: " . json_encode($response->items) .  "\n";
        return $response;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function getItems(): ?array
    {
        return $this->items;
    }

    public function jsonSerialize(): array
    {
        if ($this->total !== null) {
            $data['total'] = $this->total;
        }
        if ($this->limit !== null) {
            $data['limit'] = $this->limit;
        }
        if ($this->offset !== null) {
            $data['offset'] = $this->offset;
        }
        if ($this->items !== null) {
            $data['items'] = json_encode($this->items);
        }
        return $data;
    }
}
