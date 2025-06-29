<?php

namespace Unit\Services;


use App\Models\Tag;
use App\Repositories\TagRepository;
use App\Services\TagService;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Assert;

class TagServiceTest extends TestCase
{
    private TagService $tagService;
    private TagRepository $tagRepositoryMock;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->tagRepositoryMock = $this->createMock(TagRepository::class);
        $this->tagService = new TagService();

        // Inject mock repository using reflection
        $reflection = new \ReflectionClass($this->tagService);
        $property = $reflection->getProperty('tagRepository');
        $property->setAccessible(true);
        $property->setValue($this->tagService, $this->tagRepositoryMock);
    }

    public function testCreateTagsWithNewAndExistingTags(): void
    {
        $tags = ['newtag', 'existingtag'];
        $existingTags = ['existingtag' => 2];

        $this->tagRepositoryMock->expects($this->once())
            ->method('findTagIdsByName')
            ->with($tags)
            ->willReturn($existingTags);

        $this->tagRepositoryMock->expects($this->once())
            ->method('createTag')
            ->with('newtag')
            ->willReturn(1);

        $result = $this->tagService->createTags($tags);

        $this->assertEquals([1, 2], $result);
    }

    public function testFindTagsByFacilityId(): void
    {
        $facilityId = 123;
        $expectedTags = [new Tag(), new Tag()];

        $this->tagRepositoryMock->expects($this->once())
            ->method('findTagsByFacilityId')
            ->with($facilityId)
            ->willReturn($expectedTags);

        $result = $this->tagService->findTagsByFacilityId($facilityId);

        $this->assertCount(2, $result);
        $this->assertContainsOnlyInstancesOf(Tag::class, $result);
    }

    public function testFindTagsByFacilityIds(): void
    {
        $facilityIds = [123, 456];
        $expectedTags = [
            123 => [new Tag()],
            456 => [new Tag(), new Tag()]
        ];

        $this->tagRepositoryMock->expects($this->once())
            ->method('findTagsByFacilityIds')
            ->with($facilityIds)
            ->willReturn($expectedTags);

        $result = $this->tagService->findTagsByFacilityIds($facilityIds);

        $this->assertCount(2, $result);
        $this->assertArrayHasKey(123, $result);
        $this->assertArrayHasKey(456, $result);
        $this->assertCount(1, $result[123]);
        $this->assertCount(2, $result[456]);
    }

    public function testUpdateFacilityTagsWithEmptyTags(): void
    {
        $facilityId = 123;

        $this->tagRepositoryMock->expects($this->once())
            ->method('deleteFacilityTagsByFacility')
            ->with($facilityId);

        $this->tagRepositoryMock->expects($this->never())
            ->method('createTag');

        $this->tagRepositoryMock->expects($this->never())
            ->method('createFacilityTags');

        $this->tagService->updateFacilityTags([], $facilityId);
    }

    public function testUpdateFacilityTagsWithNewTags(): void
    {
        $facilityId = 123;
        $tags = ['tag1', 'tag2'];

        $this->tagRepositoryMock->expects($this->once())
            ->method('deleteFacilityTagsByFacility')
            ->with($facilityId);

        $this->tagRepositoryMock->expects($this->once())
            ->method('findTagIdsByName')
            ->with($tags)
            ->willReturn([]);

        $this->tagRepositoryMock->expects($this->exactly(2))
            ->method('createTag')
            ->willReturnOnConsecutiveCalls(1, 2);

        $this->tagRepositoryMock->expects($this->exactly(2))
            ->method('createFacilityTags')
            ->willReturnCallback(function ($tagId, $facId) use ($facilityId) {
                $this->assertEquals($facilityId, $facId);
                $this->assertContains($tagId, [1, 2]);
            });

        $this->tagService->updateFacilityTags($tags, $facilityId);
    }

    public function testCreateFacilityTags(): void
    {
        $facilityId = 123;
        $tags = ['tag1', 'tag2'];

        $this->tagRepositoryMock->expects($this->once())
            ->method('findTagIdsByName')
            ->with($tags)
            ->willReturn(['tag1' => 1, 'tag2' => 2]);

        $this->tagRepositoryMock->expects($this->never())
            ->method('createTag');

        $expectedCalls = [
            [1, $facilityId],
            [2, $facilityId],
        ];

        $callIndex = 0;

        $this->tagRepositoryMock->expects($this->exactly(2))
            ->method('createFacilityTags')
            ->willReturnCallback(function ($tagId, $facId) use (&$callIndex, $expectedCalls) {
                [$expectedTagId, $expectedFacilityId] = $expectedCalls[$callIndex++];
                Assert::assertEquals($expectedTagId, $tagId);
                Assert::assertEquals($expectedFacilityId, $facId);
            });

        $this->tagService->createFacilityTags($tags, $facilityId);
    }
}