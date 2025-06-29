<?php

namespace App\Models;

use JsonSerializable;

class Tag implements JsonSerializable
{
    private int $id;
    private string $name;

    public function jsonSerialize(): array
    {
        return [
            'tag_id' => $this->id,
            'name' => ucfirst($this->name),
        ];
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }
}