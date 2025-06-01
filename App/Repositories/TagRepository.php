<?php

namespace App\Repositories;

class TagRepository extends Repository
{
    public function createTags($tag_name)
    {
        $statement = $this->connection->prepare("INSERT INTO Tag (name) VALUES (:name)");
        $statement->bindParam(':name', $tag_name);
        $statement->execute();

        return $this->connection->lastInsertId();
    }
    public function createFacilityTags($tag_id, $facility_id)
    {
        $statement = $this->connection->prepare("INSERT INTO Facility_Tag (tag_id, facility_id) VALUES (:tag_id, :facility_id)");
        $statement->bindParam(':tag_id', $tag_id);
        $statement->bindParam(':facility_id', $facility_id);
        $statement->execute();
    }
    public function getTagIdByName($tag_name)
    {
        $statement = $this->connection->prepare("SELECT tag_id FROM Tag WHERE name = :name");
        $statement->bindParam(':name', $tag_name);
        $statement->execute();
        $result = $statement->fetch();
        return $result ? (int) $result['tag_id'] : null;
    }
}