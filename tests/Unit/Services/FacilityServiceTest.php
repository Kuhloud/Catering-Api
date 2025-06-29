<?php

namespace Unit\Services;


use App\Models\Facility;
use App\Repositories\FacilityRepository;
use App\Services\FacilityService;
use App\Services\LocationService;
use App\Services\TagService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class FacilityServiceTest extends TestCase
{
    private FacilityService $facilityService;
    private FacilityRepository $facilityRepositoryMock;
    private LocationService $locationServiceMock;
    private TagService $tagServiceMock;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->facilityRepositoryMock = $this->createMock(FacilityRepository::class);
        $this->locationServiceMock = $this->createMock(LocationService::class);
        $this->tagServiceMock = $this->createMock(TagService::class);

        $this->facilityService = new FacilityService();

        // Use reflection to inject our mocks
        $reflection = new \ReflectionClass($this->facilityService);

        $facilityRepoProperty = $reflection->getProperty('facilityRepository');
        $facilityRepoProperty->setAccessible(true);
        $facilityRepoProperty->setValue($this->facilityService, $this->facilityRepositoryMock);

        $locationServiceProperty = $reflection->getProperty('locationService');
        $locationServiceProperty->setAccessible(true);
        $locationServiceProperty->setValue($this->facilityService, $this->locationServiceMock);

        $tagServiceProperty = $reflection->getProperty('tagService');
        $tagServiceProperty->setAccessible(true);
        $tagServiceProperty->setValue($this->facilityService, $this->tagServiceMock);
    }

    public function testCreateFacilitySuccessWithoutTags(): void
    {
        $facilityName = 'Test Facility';
        $locationId = 1;
        $facilityId = 123;

        $expectedFacility = new Facility();
        $expectedFacility->setId($facilityId);

        // Mock repository calls
        $this->facilityRepositoryMock->expects($this->once())
            ->method('createFacility')
            ->with($facilityName, $locationId)
            ->willReturn($facilityId);

        $this->facilityRepositoryMock->expects($this->once())
            ->method('facilityExists')
            ->with($facilityId)
            ->willReturn(true);

        $this->facilityRepositoryMock->expects($this->once())
            ->method('findFacilityByFacilityId')
            ->with($facilityId)
            ->willReturn($expectedFacility);

        $this->tagServiceMock->expects($this->never())
            ->method('createFacilityTags');

        $result = $this->facilityService->createFacility($facilityName, $locationId);

        $this->assertInstanceOf(Facility::class, $result);
        $this->assertEquals($facilityId, $result->getId());
    }

    public function testCreateFacilitySuccessWithTags(): void
    {
        $facilityName = 'Test Facility';
        $locationId = 1;
        $facilityId = 123;
        $tags = ['Tag1', 'Tag2'];
        $lowercaseTags = ['tag1', 'tag2'];

        $expectedFacility = new Facility();
        $expectedFacility->setId($facilityId);

        // Mock repository calls
        $this->facilityRepositoryMock->expects($this->once())
            ->method('createFacility')
            ->with($facilityName, $locationId)
            ->willReturn($facilityId);

        $this->facilityRepositoryMock->expects($this->once())
            ->method('facilityExists')
            ->with($facilityId)
            ->willReturn(true);

        $this->facilityRepositoryMock->expects($this->once())
            ->method('findFacilityByFacilityId')
            ->with($facilityId)
            ->willReturn($expectedFacility);

        $this->tagServiceMock->expects($this->once())
            ->method('createFacilityTags')
            ->with($lowercaseTags, $facilityId);

        $result = $this->facilityService->createFacility($facilityName, $locationId, $tags);

        $this->assertInstanceOf(Facility::class, $result);
    }

    public function testReadFacilitySuccess(): void
    {
        $facilityId = 123;
        $facility = new Facility();
        $location = new \App\Models\Location();
        $tags = [new \App\Models\Tag()];

        $this->facilityRepositoryMock->expects($this->once())
            ->method('facilityExists')
            ->with($facilityId)
            ->willReturn(true);

        $this->facilityRepositoryMock->expects($this->once())
            ->method('findFacilityByFacilityId')
            ->with($facilityId)
            ->willReturn($facility);

        $this->locationServiceMock->expects($this->once())
            ->method('findLocationByFacilityId')
            ->with($facilityId)
            ->willReturn($location);

        $this->tagServiceMock->expects($this->once())
            ->method('findTagsByFacilityId')
            ->with($facilityId)
            ->willReturn($tags);

        $result = $this->facilityService->readFacility($facilityId);

        $this->assertInstanceOf(Facility::class, $result);
    }

    public function testReadFacilityNotFound(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Facility not found');

        $facilityId = 999;

        $this->facilityRepositoryMock->expects($this->once())
            ->method('facilityExists')
            ->with($facilityId)
            ->willReturn(false);

        $this->facilityService->readFacility($facilityId);
    }

    public function testReadFacilitiesWithFilters(): void
    {
        $facilityName = 'Test';
        $tagName = 'sports';
        $city = 'Amsterdam';

        $facility1 = new Facility();
        $facility1->setId(1);
        $facility2 = new Facility();
        $facility2->setId(2);

        $locations = [
            1 => new \App\Models\Location(),
            2 => new \App\Models\Location()
        ];

        $tags = [
            1 => [new \App\Models\Tag()],
            2 => [new \App\Models\Tag(), new \App\Models\Tag()]
        ];

        $this->facilityRepositoryMock->expects($this->once())
            ->method('findFacilitiesByFilter')
            ->with($facilityName, $tagName, $city)
            ->willReturn([$facility1, $facility2]);

        $this->locationServiceMock->expects($this->once())
            ->method('findLocationsByFacilityIds')
            ->with([1, 2])
            ->willReturn($locations);

        $this->tagServiceMock->expects($this->once())
            ->method('findTagsByFacilityIds')
            ->with([1, 2])
            ->willReturn($tags);

        $result = $this->facilityService->readFacilities($facilityName, $tagName, $city);

        $this->assertCount(2, $result);
        $this->assertInstanceOf(Facility::class, $result[0]);
        $this->assertInstanceOf(Facility::class, $result[1]);
    }

    public function testUpdateFacilitySuccess(): void
    {
        $facilityId = 123;
        $facilityName = 'Updated Name';
        $locationId = 2;
        $tags = ['New', 'Tags'];
        $lowercaseTags = ['new', 'tags'];

        $expectedFacility = new Facility();

        $this->facilityRepositoryMock->expects($this->any())  // <-- changed here
        ->method('facilityExists')
            ->with($facilityId)
            ->willReturn(true);

        $this->facilityRepositoryMock->expects($this->once())
            ->method('updateFacility')
            ->with($facilityId, $facilityName, $locationId);

        $this->tagServiceMock->expects($this->once())
            ->method('updateFacilityTags')
            ->with($lowercaseTags, $facilityId);

        $this->facilityRepositoryMock->expects($this->once())
            ->method('findFacilityByFacilityId')
            ->with($facilityId)
            ->willReturn($expectedFacility);

        $result = $this->facilityService->updateFacility($facilityId, $facilityName, $locationId, $tags);

        $this->assertInstanceOf(Facility::class, $result);
    }

    public function testUpdateFacilityNotFound(): void
    {
        $facilityId = 999;

        $this->facilityRepositoryMock->expects($this->once())
            ->method('facilityExists')
            ->with($facilityId)
            ->willReturn(false);

        $result = $this->facilityService->updateFacility($facilityId, 'Name');

        $this->assertNull($result);
    }

    public function testDeleteFacilitySuccess(): void
    {
        $facilityId = 123;

        $this->facilityRepositoryMock->expects($this->once())
            ->method('deleteFacility')
            ->with($facilityId)
            ->willReturn(true);

        $result = $this->facilityService->deleteFacility($facilityId);

        $this->assertTrue($result);
    }
}