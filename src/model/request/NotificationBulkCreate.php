<?php

namespace Blerify\Model\Request;

use JsonSerializable;

class NotificationBulkCreate implements JsonSerializable
{
    private $items = null;

    public static function new(): self
    {
        return new NotificationBulkCreate();
    }

    public function items($items): self
    {
        $this->items = $items;
        return $this;
    }

    public function jsonSerialize(): array
    {
        if ($this->items !== null) {
            $data['items'] = $this->items;
        }
        return $data;
    }
}
