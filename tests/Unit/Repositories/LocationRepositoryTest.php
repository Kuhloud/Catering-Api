<?php

namespace Unit\Repositories;

use PHPUnit\Framework\TestCase;
use App\Repositories\LocationRepository;
use App\Models\Location;
use PDO;
use PDOException;
use PDOStatement;
use ReflectionClass;

class LocationRepositoryTest extends TestCase
{
    private $pdoMock;
    private $statementMock;
    private LocationRepository $repository;

    protected function setUp(): void
    {
        $this->statementMock = $this->createMock(PDOStatement::class);
        $this->pdoMock = $this->createMock(PDO::class);

        $this->pdoMock->method('prepare')->willReturn($this->statementMock);

        $this->repository = new LocationRepository();

        // Inject mocked PDO into private $connection property
        $ref = new ReflectionClass($this->repository);
        $prop = $ref->getProperty('connection');
        $prop->setAccessible(true);
        $prop->setValue($this->repository, $this->pdoMock);
    }

    public function testFindLocationByFacilityIdReturnsLocation()
    {
        $location = new Location();

        $this->statementMock->expects($this->once())
            ->method('bindParam')
            ->with(':facility_id', 1);

        $this->statementMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $this->statementMock->expects($this->once())
            ->method('fetchObject')
            ->with(Location::class)
            ->willReturn($location);

        $result = $this->repository->findLocationByFacilityId(1);

        $this->assertSame($location, $result);
    }

    public function testFindLocationByFacilityIdReturnsNullOnException()
    {
        $this->pdoMock->method('prepare')->will($this->throwException(new PDOException()));

        $result = $this->repository->findLocationByFacilityId(1);

        $this->assertNull($result);
    }

    public function testFindLocationsByFacilityIdsReturnsLocationsArray()
    {
        $rows = [
            [
                'id' => 1,
                'city' => 'City1',
                'address' => 'Address1',
                'zip_code' => '12345',
                'country_code' => 'US',
                'phone_number' => '555-1234',
                'facility_id' => 101,
            ],
            [
                'id' => 2,
                'city' => 'City2',
                'address' => 'Address2',
                'zip_code' => '67890',
                'country_code' => 'US',
                'phone_number' => '555-5678',
                'facility_id' => 102,
            ],
        ];

        $this->statementMock->expects($this->once())
            ->method('execute')
            ->with([101, 102])
            ->willReturn(true);

        $this->statementMock->expects($this->once())
            ->method('fetchAll')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn($rows);

        $result = $this->repository->findLocationsByFacilityIds([101, 102]);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertArrayHasKey(101, $result);
        $this->assertArrayHasKey(102, $result);
        $this->assertInstanceOf(Location::class, $result[101]);
        $this->assertInstanceOf(Location::class, $result[102]);

        // Additional property checks
        $this->assertEquals('City1', $result[101]->getCity());
        $this->assertEquals('City2', $result[102]->getCity());
    }

    public function testFindLocationsByFacilityIdsReturnsEmptyArrayOnException()
    {
        $this->pdoMock->method('prepare')->will($this->throwException(new PDOException()));

        $result = $this->repository->findLocationsByFacilityIds([1, 2]);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}