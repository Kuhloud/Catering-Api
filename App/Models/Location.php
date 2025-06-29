<?php

namespace App\Models;

use JsonSerializable;

class Location implements JsonSerializable
{
    private int $id;
    private string $city;
    private string $address;
    private string $zip_code;
    private string $country_code;
    private string $phone_number;

    public function jsonSerialize(): array
    {
        return [
            'location_id' => $this->id,
            'city' => $this->city,
            'zip_code' => $this->zip_code,
            'country_code' => $this->country_code,
            'phone_number' => $this->phone_number,
        ];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getZipCode(): string
    {
        return $this->zip_code;
    }

    public function getCountryCode(): string
    {
        return $this->country_code;
    }

    public function getPhoneNumber(): string
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(string $phone_number): void
    {
        $this->phone_number = $phone_number;
    }

    public function setCountryCode(string $country_code): void
    {
        $this->country_code = $country_code;
    }

    public function setZipCode(string $zip_code): void
    {
        $this->zip_code = $zip_code;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}