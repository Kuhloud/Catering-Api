<?php

namespace App\Models;

use DateTime;
use JsonSerializable;

class Facility implements JsonSerializable
{
    private int $facility_id;
    private string $name;
    private string $creation_date;
    private Location $location;
    /**
     * @var Tag[]
     */
    private array $tags = [];

    public function getFacilityId(): int
    {
        return $this->facility_id;
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
            'facility_id' => $this->facility_id,
            'name' => $this->name,
            'creation_date' => $this->creation_date,
            'location' => $this->location,
            'tags' => $this->tags,
        ];
    }
}