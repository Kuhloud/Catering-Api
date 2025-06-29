<?php

namespace Unit\Services;

use App\Models\Location;
use App\Repositories\LocationRepository;
use App\Services\LocationService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class LocationServiceTest extends TestCase
{
    private LocationService $locationService;
    private LocationRepository $locationRepositoryMock;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->locationRepositoryMock = $this->createMock(LocationRepository::class);
        $this->locationService = new LocationService();

        // Use reflection to inject the mock repository
        $reflection = new \ReflectionClass($this->locationService);
        $property = $reflection->getProperty('locationRepository');
        $property->setAccessible(true);
        $property->setValue($this->locationService, $this->locationRepositoryMock);
    }

    public function testFindLocationByFacilityId(): void
    {
        $facilityId = 123;
        $expectedLocation = new Location();

        $this->locationRepositoryMock->expects($this->once())
            ->method('findLocationByFacilityId')
            ->with($facilityId)
            ->willReturn($expectedLocation);

        $result = $this->locationService->findLocationByFacilityId($facilityId);

        $this->assertInstanceOf(Location::class, $result);
        $this->assertSame($expectedLocation, $result);
    }

    public function testFindLocationsByFacilityIds(): void
    {
        $facilityIds = [123, 456];
        $expectedLocations = [
            123 => new Location(),
            456 => new Location()
        ];

        $this->locationRepositoryMock->expects($this->once())
            ->method('findLocationsByFacilityIds')
            ->with($facilityIds)
            ->willReturn($expectedLocations);

        $result = $this->locationService->findLocationsByFacilityIds($facilityIds);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertArrayHasKey(123, $result);
        $this->assertArrayHasKey(456, $result);
        $this->assertInstanceOf(Location::class, $result[123]);
        $this->assertInstanceOf(Location::class, $result[456]);
    }

    public function testFindLocationsByFacilityIdsWithEmptyInput(): void
    {
        $this->locationRepositoryMock->expects($this->once())
            ->method('findLocationsByFacilityIds')
            ->with([])
            ->willReturn([]);

        $result = $this->locationService->findLocationsByFacilityIds([]);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}