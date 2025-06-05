<?php

namespace App\Repositories;

use PDO;

class TagRepository extends Repository
{
    public function getTagByName($tag_names)
    {
        $placeholders = implode(',', array_fill(0, count($tag_names), '?'));
        $statement = $this->connection->prepare("SELECT tag_id, name FROM Tag WHERE name IN ($placeholders)");
        $statement->execute($tag_names);
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);
        $result = [];
        foreach ($rows as $row) {
            $result[$row['name']] = $row['tag_id'];
        }
        return $result; // e.g. ['mexican' => 1, 'italian' => 2]
    }
    public function getTagsByFacilityId($facility_id)
    {
        $statement = $this->connection->prepare("SELECT t.tag_id, t.name FROM Tag t JOIN Facility_Tag ft ON ft.tag_id = t.tag_id WHERE ft.facility_id = :facility_id");
        $statement->bindParam(':facility_id', $facility_id);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    public function createTag($tag_name)
    {
        $statement = $this->connection->prepare("INSERT INTO Tag (name) VALUES (:name)");
        $statement->bindParam(':name', $tag_name);
        $statement->execute();

        return $this->connection->lastInsertId();
    }
    public function updateTag($tag_id, $tag_name)
    {
        $statement = $this->connection->prepare("UPDATE Tag SET name = :name WHERE tag_id = :tag_id");
        $statement->bindParam(':tag_id', $tag_id);
        $statement->bindParam(':name', $tag_name);
    }
    public function createFacilityTags($tag_id, $facility_id)
    {
        $statement = $this->connection->prepare("INSERT INTO Facility_Tag (tag_id, facility_id) VALUES (:tag_id, :facility_id)");
        $statement->bindParam(':tag_id', $tag_id);
        $statement->bindParam(':facility_id', $facility_id);
        $statement->execute();
    }
    public function deleteFacilityTagsByFacility($facility_id)
    {
        $statement = $this->connection->prepare("DELETE FROM Facility_Tag WHERE facility_id = :facility_id");
        $statement->bindParam(':facility_id', $facility_id);
        $statement->execute();
    }
}