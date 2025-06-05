<?php

namespace App\Models;

use JsonSerializable;

class Location implements JsonSerializable
{
    private int $location_id;
    private string $city;
    private string $address;
    private string $zip_code;
    private string $country_code;
    private string $phone_number;

    public function jsonSerialize(): array
    {
        return [
            'location_id' => $this->location_id,
            'city' => $this->city,
            'zip_code' => $this->zip_code,
            'country_code' => $this->country_code,
            'phone_number' => $this->phone_number,
        ];
    }
}