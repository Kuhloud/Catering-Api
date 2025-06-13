<?php

namespace App\Repositories;

use App\Models\Tag;
use PDO;

class TagRepository extends Repository
{
    /**
     * Reads tag IDs by their names.
     *
     * Builds a dynamic SQL query using placeholders to safely query multiple tag names.
     *
     * @param string[] $tagNames List of tag names
     * @return array Associative array mapping tag name => tag ID
     */
    public function findTagIdByName(array $tagNames): array
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

    /**
     * Retrieves all tags associated with a single facility.
     *
     * @param int $facilityId
     * @return array List of tag records (as associative arrays)
     */
    public function findTagsByFacilityId(int $facilityId): array
    {
        $statement = $this->connection->prepare("SELECT t.tag_id, t.name FROM Tag t JOIN Facility_Tag ft ON ft.tag_id = t.tag_id WHERE ft.facility_id = :facility_id");
        $statement->bindParam(':facility_id', $facilityId);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Reads tags for multiple facilities in a single query.
     *
     * Returns an associative array indexed by facility ID,
     * where each value is an array of Tag objects.
     *
     * @param int[] $facilityIds
     * @return array facility_id => Tag[]
     */
    public function findTagsByFacilityIds(array $facilityIds): array
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
    public function createTag(string $tagName)
    {
        $statement = $this->connection->prepare("INSERT INTO Tag (name) VALUES (:name)");
        $statement->bindParam(':name', $tagName);
        $statement->execute();

        return $this->connection->lastInsertId();
    }
    public function createFacilityTags(int $tagId, int $facilityId)
    {
        $statement = $this->connection->prepare("INSERT INTO Facility_Tag (tag_id, facility_id) VALUES (:tag_id, :facility_id)");
        $statement->bindParam(':tag_id', $tagId);
        $statement->bindParam(':facility_id', $facilityId);
        $statement->execute();
    }
    public function deleteFacilityTagsByFacility(int $facilityId)
    {
        $statement = $this->connection->prepare("DELETE FROM Facility_Tag WHERE facility_id = :facility_id");
        $statement->bindParam(':facility_id', $facilityId);
        $statement->execute();
    }
}