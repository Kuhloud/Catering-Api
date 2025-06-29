<?php

namespace Unit\Repositories;

use PHPUnit\Framework\TestCase;
use App\Repositories\FacilityRepository;
use App\Models\Facility;
use PDO;
use PDOException;
use PDOStatement;
use Exception;
use ReflectionClass;

class FacilityRepositoryTest extends TestCase
{
    private $pdoMock;
    private $statementMock;
    private FacilityRepository $repository;

    protected function setUp(): void
    {
        $this->statementMock = $this->createMock(PDOStatement::class);
        $this->pdoMock = $this->createMock(PDO::class);

        $this->pdoMock->method('prepare')->willReturn($this->statementMock);

        $this->repository = new FacilityRepository();

        // Inject mocked PDO into private $connection property
        $ref = new ReflectionClass($this->repository);
        $prop = $ref->getProperty('connection');
        $prop->setAccessible(true);
        $prop->setValue($this->repository, $this->pdoMock);
    }

    public function testCreateFacilityReturnsLastInsertId()
    {
        $callCount = 0;

        $this->statementMock->expects($this->exactly(2))
            ->method('bindParam')
            ->willReturnCallback(function ($param, &$value) use (&$callCount) {
                if ($callCount === 0) {
                    $this->assertEquals(':name', $param);
                    // Optionally assert $value here if needed
                } elseif ($callCount === 1) {
                    $this->assertEquals(':location_id', $param);
                    // Optionally assert $value here if needed
                }
                $callCount++;
                return true;
            });

        $this->statementMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $this->pdoMock->method('lastInsertId')->willReturn('42');

        $result = $this->repository->createFacility('Test Facility', 10);

        $this->assertEquals('42', $result);
    }

    public function testCreateFacilityReturnsNullOnException()
    {
        $this->pdoMock->method('prepare')->will($this->throwException(new PDOException()));

        $result = $this->repository->createFacility('Test Facility', 10);
        $this->assertNull($result);
    }

    public function testFindFacilityByFacilityIdReturnsFacilityObject()
    {
        $facility = new Facility();
        $this->statementMock->expects($this->once())->method('bindParam')->with(':facility_id', 5);
        $this->statementMock->expects($this->once())->method('execute')->willReturn(true);
        $this->statementMock->method('fetchObject')->with(Facility::class)->willReturn($facility);

        $result = $this->repository->findFacilityByFacilityId(5);
        $this->assertSame($facility, $result);
    }

    public function testFindFacilityByFacilityIdReturnsNullOnException()
    {
        $this->pdoMock->method('prepare')->will($this->throwException(new PDOException()));

        $result = $this->repository->findFacilityByFacilityId(5);
        $this->assertNull($result);
    }

    public function testFindFacilitiesByFilterReturnsArrayOfFacilities()
    {
        $facility1 = new Facility();
        $facility2 = new Facility();

        // We'll override buildFilters to simplify test (optional, but let's mock statement execution)
        $this->statementMock->method('execute')->willReturn(true);
        $this->statementMock->method('fetchAll')->with(PDO::FETCH_CLASS, Facility::class)
            ->willReturn([$facility1, $facility2]);

        $result = $this->repository->findFacilitiesByFilter('name', 'tag', 'city');
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf(Facility::class, $result);
    }

    public function testFindFacilitiesByFilterReturnsEmptyArrayOnException()
    {
        $this->pdoMock->method('prepare')->will($this->throwException(new PDOException()));

        $result = $this->repository->findFacilitiesByFilter('name', 'tag', 'city');
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testUpdateFacilityExecutesSuccessfully()
    {
        $expectedParams = [
            ':facility_id',
            ':location_id',
            ':name',
        ];

        $callCount = 0;

        $this->statementMock->expects($this->exactly(3))
            ->method('bindParam')
            ->willReturnCallback(function ($param, &$var) use (&$callCount, $expectedParams) {
                $expectedParam = $expectedParams[$callCount++];
                $this->assertEquals($expectedParam, $param);
                // You can optionally assert $var here or do nothing
                return true; // bindParam returns boolean
            });

        $this->statementMock->expects($this->once())
            ->method('execute')
            ->willReturn(true);

        $this->repository->updateFacility(1, 'Updated Facility', 2);

        $this->assertTrue(true); // Marks test passed if no exceptions
    }

    public function testUpdateFacilityThrowsExceptionOnFailure()
    {
        $this->statementMock->method('execute')->will($this->throwException(new PDOException()));

        $this->expectException(PDOException::class);
        $this->expectExceptionMessage('Could not update facility');

        $this->repository->updateFacility(1, 'Updated Facility', 2);
    }

    public function testDeleteFacilityReturnsTrueWhenDeleted()
    {
        $this->statementMock->expects($this->once())->method('bindParam')->with(':facility_id', 1);
        $this->statementMock->method('execute')->willReturn(true);
        $this->statementMock->method('rowCount')->willReturn(1);

        $result = $this->repository->deleteFacility(1);
        $this->assertTrue($result);
    }

    public function testDeleteFacilityReturnsFalseWhenNothingDeleted()
    {
        $this->statementMock->method('execute')->willReturn(true);
        $this->statementMock->method('rowCount')->willReturn(0);

        $result = $this->repository->deleteFacility(1);
        $this->assertFalse($result);
    }

    public function testDeleteFacilityThrowsExceptionOnError()
    {
        $this->statementMock->method('execute')->will($this->throwException(new PDOException()));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Something went wrong');

        $this->repository->deleteFacility(1);
    }

    public function testFacilityExistsReturnsTrueWhenCountGreaterThanZero()
    {
        $this->statementMock->expects($this->once())->method('bindParam')->with(':facility_id', 5);
        $this->statementMock->method('execute')->willReturn(true);
        $this->statementMock->method('fetchColumn')->willReturn(1);

        $result = $this->repository->facilityExists(5);
        $this->assertTrue($result);
    }

    public function testFacilityExistsReturnsFalseWhenCountZero()
    {
        $this->statementMock->method('execute')->willReturn(true);
        $this->statementMock->method('fetchColumn')->willReturn(0);

        $result = $this->repository->facilityExists(5);
        $this->assertFalse($result);
    }

    public function testFacilityExistsReturnsFalseOnException()
    {
        $this->pdoMock->method('prepare')->will($this->throwException(new PDOException()));

        $result = $this->repository->facilityExists(5);
        $this->assertFalse($result);
    }
}