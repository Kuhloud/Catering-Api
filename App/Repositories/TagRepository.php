<?php

namespace App\Repositories;

use App\Models\Tag;
use PDO;

class TagRepository extends Repository
{
    public function readTagIdByName($tagNames)
    {
        $placeholders = implode(',', array_fill(0, count($tagNames), '?'));
        $statement = $this->connection->prepare("SELECT tag_id, name FROM Tag WHERE name IN ($placeholders)");
        $statement->execute($tagNames);
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $result = [];
        foreach ($rows as $row) {
            $result[$row['name']] = $row['tag_id'];
        }
        return $result;
    }
    public function readTagsByFacilityId($facilityId)
    {
        $statement = $this->connection->prepare("SELECT t.tag_id, t.name FROM Tag t JOIN Facility_Tag ft ON ft.tag_id = t.tag_id WHERE ft.facility_id = :facility_id");
        $statement->bindParam(':facility_id', $facilityId);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    public function readTagsByFacilityIds($facilityIds)
    {
        $placeholder = implode(',', array_fill(0, count($facilityIds), '?'));
        $statement = $this->connection->prepare("SELECT ft.facility_id, t.tag_id, t.name FROM Tag t JOIN Facility_Tag ft ON ft.tag_id = t.tag_id WHERE ft.facility_id IN ($placeholder)");
        $statement->execute($facilityIds);
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $result = [];
        foreach ($rows as $row) {
            $tag = new Tag();
            $tag->setTagId($row['tag_id']);
            $tag->setName($row['name']);
            $result[$row['facility_id']][] = $tag;
        }
        return $result;
    }
    public function createTag($tagName)
    {
        $statement = $this->connection->prepare("INSERT INTO Tag (name) VALUES (:name)");
        $statement->bindParam(':name', $tagName);
        $statement->execute();

        return $this->connection->lastInsertId();
    }
    public function updateTag($tagId, $tagName)
    {
        $statement = $this->connection->prepare("UPDATE Tag SET name = :name WHERE tag_id = :tag_id");
        $statement->bindParam(':tag_id', $tagId);
        $statement->bindParam(':name', $tagName);
    }
    public function createFacilityTags($tagId, $facilityId)
    {
        $statement = $this->connection->prepare("INSERT INTO Facility_Tag (tag_id, facility_id) VALUES (:tag_id, :facility_id)");
        $statement->bindParam(':tag_id', $tagId);
        $statement->bindParam(':facility_id', $facilityId);
        $statement->execute();
    }
    public function deleteFacilityTagsByFacility($facilityId)
    {
        $statement = $this->connection->prepare("DELETE FROM Facility_Tag WHERE facility_id = :facility_id");
        $statement->bindParam(':facility_id', $facilityId);
        $statement->execute();
    }
}