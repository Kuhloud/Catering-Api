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
            'name' => ucfirst($this->name),
        ];
    }

    public function setTagId(int $tag_id): void
    {
        $this->tag_id = $tag_id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}