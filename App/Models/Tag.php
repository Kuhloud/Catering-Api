<?php

namespace App\Models;

use JsonSerializable;

class Tag implements JsonSerializable
{
    private int $tag_id;
    private string $name;

    public function jsonSerialize(): array
    {
        return [
            'tag_id' => $this->tag_id,
            'name' => $this->name,
        ];
    }
}