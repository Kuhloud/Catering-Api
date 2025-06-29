<?php

namespace Unit\Models;

use PHPUnit\Framework\TestCase;
use App\Models\Tag;

class TagTest extends TestCase
{
    private Tag $tag;

    protected function setUp(): void
    {
        $this->tag = new Tag();
    }
    public function testSetAndGetId(): void
    {
        $id = 123;
        $this->tag->setId($id);
        $this->assertSame($id, $this->tag->getId());
    }

    public function testSetAndGetName(): void
    {
        $name = 'example';
        $this->tag->setName($name);
        $this->assertSame($name, $this->tag->getName());
    }

    public function testJsonSerializeReturnsExpectedArray(): void
    {
        $id = 1;
        $name = 'test';

        $this->tag->setId($id);
        $this->tag->setName($name);

        $expected = [
            'tag_id' => $id,
            'name' => ucfirst($name),
        ];

        $this->assertSame($expected, $this->tag->jsonSerialize());
    }
}