<?php

namespace App\Models;

use JsonSerializable;

class Facility implements JsonSerializable
{
    private int $id;
    private string $name;
    private string $creation_date;
    private Location $location;
    /**
     * @var Tag[]
     */
    private array $tags = [];

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setCreationDate(string $creation_date): void
    {
        $this->creation_date = $creation_date;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCreationDate(): string
    {
        return $this->creation_date;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setLocation(Location $location): void
    {
        $this->location = $location;
    }
    /**
     * @param Tag[] $tags
     */
    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    public function jsonSerialize(): array
    {
        return [
            'facility_id' => $this->id,
            'name' => $this->name,
            'creation_date' => $this->creation_date,
            'location' => $this->location,
            'tags' => $this->tags,
        ];
    }
}