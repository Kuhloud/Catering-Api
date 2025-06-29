<?php

namespace Unit\Models;

use JsonSerializable;
use PHPUnit\Framework\TestCase;
use App\Models\Facility;
use App\Models\Location;
use App\Models\Tag;

class FacilityTest extends TestCase
{
    private Facility $facility;

    protected function setUp(): void
    {
        $this->facility = new Facility();
    }

    public function testSetAndGetId(): void
    {
        $id = 10;
        $this->facility->setId($id);
        $this->assertSame($id, $this->facility->getId());
    }

    public function testSetAndGetName(): void
    {
        $name = 'Test Facility';
        $this->facility->setName($name);
        $this->assertSame($name, $this->facility->getName());
    }

    public function testSetAndGetCreationDate(): void
    {
        $date = '2025-06-29';
        $this->facility->setCreationDate($date);
        $this->assertSame($date, $this->facility->getCreationDate());
    }

    public function testSetAndGetLocation(): void
    {
        $location = $this->createMock(Location::class);
        $this->facility->setLocation($location);
        $this->assertSame($location, $this->facility->getLocation());
    }

    public function testSetAndGetTags(): void
    {
        $tag1 = $this->createMock(Tag::class);
        $tag2 = $this->createMock(Tag::class);

        $tags = [$tag1, $tag2];
        $this->facility->setTags($tags);
        $this->assertSame($tags, $this->facility->getTags());
    }

    public function testJsonSerialize(): void
    {
        $id = 123;
        $name = 'Facility Name';
        $date = '2025-06-29';

        $locationMock = $this->createMock(Location::class);
        $locationMock->expects($this->once())
            ->method('jsonSerialize')
            ->willReturn(['location_id' => 1, 'city' => 'CityName']);

        $tag1 = $this->createMock(Tag::class);
        $tag1->expects($this->once())
            ->method('jsonSerialize')
            ->willReturn(['tag_id' => 1, 'name' => 'TagOne']);

        $tag2 = $this->createMock(Tag::class);
        $tag2->expects($this->once())
            ->method('jsonSerialize')
            ->willReturn(['tag_id' => 2, 'name' => 'TagTwo']);

        $tags = [$tag1, $tag2];

        $this->facility->setId($id);
        $this->facility->setName($name);
        $this->facility->setCreationDate($date);
        $this->facility->setLocation($locationMock);
        $this->facility->setTags($tags);

        $expected = [
            'facility_id' => $id,
            'name' => $name,
            'creation_date' => $date,
            'location' => ['location_id' => 1, 'city' => 'CityName'],
            'tags' => [
                ['tag_id' => 1, 'name' => 'TagOne'],
                ['tag_id' => 2, 'name' => 'TagTwo'],
            ],
        ];

        $actual = $this->toArray($this->facility->jsonSerialize());

        $this->assertSame($expected, $actual);
    }
    private function toArray($value)
    {
        if (is_array($value)) {
            return array_map([$this, 'toArray'], $value);
        }
        if ($value instanceof JsonSerializable) {
            return $this->toArray($value->jsonSerialize());
        }
        return $value;
    }
}