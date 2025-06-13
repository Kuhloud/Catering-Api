<?php

namespace App\Repositories;

use App\Models\Facility;
use Exception;
use PDO;
use PDOException;

class FacilityRepository extends Repository
{
    public function createFacility($facilityName, $FacilityLocation): ?int
    {
        try {
            $statement = $this->connection->prepare("INSERT INTO Facility (name, location_id) VALUES (:name, :location_id)");
            $statement->bindParam(':name', $facilityName);
            $statement->bindParam(':location_id', $FacilityLocation);
            $statement->execute();

            return $this->connection->lastInsertId();
        }
        catch (PDOException $e) {
            return null;
        }
    }
    public function findFacilityByFacilityId($facilityId): ?Facility
    {
        try
        {
            $statement = $this->connection->prepare("SELECT facility_id, name, creation_date FROM Facility WHERE facility_id = :facility_id");
            $statement->bindParam(':facility_id', $facilityId);
            $statement->execute();

            return $statement->fetchObject(Facility::class);
        }
        catch (PDOException $e)
        {
            return null;
        }
    }
    /**
     * Retrieves facilities from the database that match the provided optional filters.
     *
     * @param string|null $facilityName   Filter facilities by name
     * @param string|null $tagName        Filter facilities by tag name (note: typo in var name)
     * @param string|null $locationCity   Filter facilities by location city
     *
     * @return Facility[] List of facilities that match the filters, or an empty array if an error occurs
     */
    public function findFacilitiesByFilter(?string $facilityName = null, ?string $tagName = null, ?string $locationCity = null): array
    {
        $filters = $this->buildFilters($facilityName, $tagName, $locationCity);
        $sql = $this->buildFilterQuery($filters);
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($filters['params']);

            return $stmt->fetchAll(PDO::FETCH_CLASS, Facility::class);
        }
        catch (PDOException $e) {
            return [];
        }
    }
    public function updateFacility($facilityId, $FacilityName, $locationId): void
    {
        try {
            $statement = $this->connection->prepare("UPDATE Facility SET name = :name, location_id = :location_id WHERE facility_id = :facility_id");
            $statement->bindParam(':facility_id', $facilityId);
            $statement->bindParam(':location_id', $locationId);
            $statement->bindParam(':name', $FacilityName);
            $statement->execute();
        } catch (PDOException $e) {
            throw new PDOException("Could not update facility");
        }
    }

    /**
     * @throws Exception
     */
    public function deleteFacility($facilityId)
    {
        try {
            $statement = $this->connection->prepare("DELETE FROM Facility WHERE facility_id = :facility_id");
            $statement->bindParam(':facility_id', $facilityId);
            $statement->execute();
            return $statement->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception("Something went wrong");
        }
    }
    /**
     * Builds the SQL query string used to fetch facilities based on dynamic filters.
     *
     * @param array $filters Associative array with keys:
     * - 'sql': string containing SQL WHERE clause additions
     * - 'params': array of bound parameters for PDO
     *
     * @return string Full SQL query including dynamic filters and ordering
     */
    private function buildFilterQuery(array $filters): string
    {
        $sql = "
        SELECT DISTINCT f.facility_id, f.name, f.creation_date
        FROM Facility f
        JOIN Location l ON f.location_id = l.location_id
        LEFT JOIN Facility_Tag ft ON f.facility_id = ft.facility_id
        LEFT JOIN Tag t ON ft.tag_id = t.tag_id
        WHERE 1 = 1
    ";
        return $sql . $filters['sql'] . " ORDER BY f.facility_id";
    }
    /**
     * Constructs SQL filter conditions and parameter bindings for optional filters.
     *
     * @param string|null $facilityName   Filter by facility name (partial match)
     * @param string|null $tagName        Filter by tag name (partial match)
     * @param string|null $locationCity   Filter by location city (partial match)
     *
     * @return array An array containing:
     * - 'sql': dynamically built WHERE clause additions
     * - 'params': key-value pairs for PDO parameter binding
     */
    private function buildFilters(?string $facilityName, ?string $tagName, ?string $locationCity): array
    {
        $filters = ['sql' => '', 'params' => []];
        $columns = ['facility_name' => ['f.name', $facilityName], 'tag_name' => ['t.name', $tagName], 'location_city' => ['l.city', $locationCity]];

        foreach ($columns as $key => [$column, $value]) {
            if ($value !== null) {
                $value = trim($value);
                $filters['sql'] .= " AND $column LIKE :$key";
                $filters['params'][":$key"] = "%$value%";
            }
        }
        return $filters;
    }
    public function facilityExists($facilityId): bool
    {
        try {
            $statement = $this->connection->prepare("SELECT COUNT(*) FROM Facility WHERE facility_id = :facility_id");
            $statement->bindParam(':facility_id', $facilityId);
            $statement->execute();
            return $statement->fetchColumn();
        } catch (PDOException $e) {
            return false;
        }
    }
}