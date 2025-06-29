<?php

namespace Unit\Repositories;

use PHPUnit\Framework\TestCase;
use App\Repositories\TagRepository;
use App\Models\Tag;
use PDO;
use PDOStatement;
use PDOException;
use ReflectionClass;

class TagRepositoryTest extends TestCase
{
    private $pdoMock;
    private $statementMock;
    private TagRepository $repository;

    protected function setUp(): void
    {
        $this->statementMock = $this->createMock(PDOStatement::class);
        $this->pdoMock = $this->createMock(PDO::class);

        $this->pdoMock->method('prepare')->willReturn($this->statementMock);

        $this->repository = new TagRepository();

        // Inject the mocked PDO into the private connection property
        $ref = new ReflectionClass($this->repository);
        $prop = $ref->getProperty('connection');
        $prop->setAccessible(true);
        $prop->setValue($this->repository, $this->pdoMock);
    }

    public function testFindTagIdsByNameReturnsCorrectMapping()
    {
        $tagNames = ['foo', 'bar'];
        $rows = [
            ['id' => 1, 'name' => 'foo'],
            ['id' => 2, 'name' => 'bar'],
        ];

        $this->statementMock->expects($this->once())->method('execute')->with($tagNames)->willReturn(true);
        $this->statementMock->expects($this->once())->method('fetchAll')->with(PDO::FETCH_ASSOC)->willReturn($rows);

        $result = $this->repository->findTagIdsByName($tagNames);

        $this->assertEquals([
            'foo' => 1,
            'bar' => 2,
        ], $result);
    }

    public function testFindTagsByFacilityIdReturnsTagsArray()
    {
        $facilityId = 10;
        $rows = [
            ['id' => 1, 'name' => 'tag1'],
            ['id' => 2, 'name' => 'tag2'],
        ];

        $this->statementMock->expects($this->once())->method('bindParam')->with(':facility_id', $facilityId);
        $this->statementMock->expects($this->once())->method('execute')->willReturn(true);
        $this->statementMock->expects($this->once())->method('fetchAll')->with(PDO::FETCH_ASSOC)->willReturn($rows);

        $result = $this->repository->findTagsByFacilityId($facilityId);

        $this->assertEquals($rows, $result);
    }

    public function testFindTagsByFacilityIdsReturnsMappedTags()
    {
        $facilityIds = [10, 20];
        $rows = [
            ['facility_id' => 10, 'id' => 1, 'name' => 'tag1'],
            ['facility_id' => 10, 'id' => 2, 'name' => 'tag2'],
            ['facility_id' => 20, 'id' => 3, 'name' => 'tag3'],
        ];

        $this->statementMock->expects($this->once())->method('execute')->with($facilityIds)->willReturn(true);
        $this->statementMock->expects($this->once())->method('fetchAll')->with(PDO::FETCH_ASSOC)->willReturn($rows);

        $result = $this->repository->findTagsByFacilityIds($facilityIds);

        $this->assertArrayHasKey(10, $result);
        $this->assertArrayHasKey(20, $result);

        $this->assertCount(2, $result[10]);
        $this->assertCount(1, $result[20]);

        foreach ($result[10] as $tag) {
            $this->assertInstanceOf(Tag::class, $tag);
        }
        foreach ($result[20] as $tag) {
            $this->assertInstanceOf(Tag::class, $tag);
        }
    }

    public function testCreateTagReturnsLastInsertId()
    {
        $tagName = 'newtag';

        $this->statementMock->expects($this->once())->method('bindParam')->with(':name', $tagName);
        $this->statementMock->expects($this->once())->method('execute')->willReturn(true);

        $this->pdoMock->expects($this->once())->method('lastInsertId')->willReturn('123');

        $result = $this->repository->createTag($tagName);

        $this->assertEquals('123', $result);
    }

    public function testCreateFacilityTagsExecutesSuccessfully()
    {
        $tagId = 1;
        $facilityId = 2;

        $calls = 0;

        $this->statementMock->expects($this->exactly(2))
            ->method('bindParam')
            ->willReturnCallback(function ($param, &$var) use (&$calls, $tagId, $facilityId) {
                if ($calls === 0) {
                    $this->assertEquals(':tag_id', $param);
                    $this->assertEquals($tagId, $var);
                } elseif ($calls === 1) {
                    $this->assertEquals(':facility_id', $param);
                    $this->assertEquals($facilityId, $var);
                }
                $calls++;
                return true;
            });

        $this->statementMock->expects($this->once())->method('execute')->willReturn(true);

        $this->repository->createFacilityTags($tagId, $facilityId);

        $this->assertTrue(true); // If no exception, test passes
    }

    public function testDeleteFacilityTagsByFacilityExecutesSuccessfully()
    {
        $facilityId = 5;

        $this->statementMock->expects($this->once())->method('bindParam')->with(':facility_id', $facilityId);
        $this->statementMock->expects($this->once())->method('execute')->willReturn(true);

        $this->repository->deleteFacilityTagsByFacility($facilityId);

        $this->assertTrue(true);
    }
}