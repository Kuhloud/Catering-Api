<?php

namespace Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Location;

class LocationTest extends TestCase
{
    private Location $location;

    protected function setUp(): void
    {
        $this->location = new Location();
    }

    public function testSetAndGetId(): void
    {
        $id = 123;
        $this->location->setId($id);
        $this->assertSame($id, $this->location->getId());
    }

    public function testSetAndGetCity(): void
    {
        $city = 'Urk';
        $this->location->setCity($city);
        $this->assertSame($city, $this->location->getCity());
    }

    public function testSetAndGetAddress(): void
    {
        $address = 'I.dkstraat 33';
        $this->location->setAddress($address);
        $this->assertSame($address, $this->location->getAddress());
    }

    public function testSetAndGetZipCode(): void
    {
        $zipCode = '90210';
        $this->location->setZipCode($zipCode);
        $this->assertSame($zipCode, $this->location->getZipCode());
    }

    public function testSetAndGetCountryCode(): void
    {
        $countryCode = 'NL';
        $this->location->setCountryCode($countryCode);
        $this->assertSame($countryCode, $this->location->getCountryCode());
    }

    public function testSetAndGetPhoneNumber(): void
    {
        $phoneNumber = '+3134567890';
        $this->location->setPhoneNumber($phoneNumber);
        $this->assertSame($phoneNumber, $this->location->getPhoneNumber());
    }

    public function testJsonSerialize(): void
    {
        $this->location->setId(1);
        $this->location->setCity('Urk');
        $this->location->setAddress('I.dkstraat 33');
        $this->location->setZipCode('12345');
        $this->location->setCountryCode('NL');
        $this->location->setPhoneNumber('+3134567890');

        $expected = [
            'location_id' => 1,
            'city' => 'Urk',
            'zip_code' => '12345',
            'country_code' => 'NL',
            'phone_number' => '+3134567890',
        ];

        $this->assertSame($expected, $this->location->jsonSerialize());
    }
}