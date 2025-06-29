<?php

namespace App\Repositories;

use App\Models\Location;
use PDO;
use PDOException;

class LocationRepository extends Repository
{
    /**
     * Finds a location by a single facility ID.
     *
     * @param int $facilityId The ID of the facility.
     * @return Location|null
     */
    public function findLocationByFacilityId(int $facilityId): ?Location
    {
        try {
            $statement = $this->connection->prepare("SELECT l.id, l.city, l.address, l.zip_code, l.country_code, l.phone_number FROM Location l JOIN Facility f ON f.location_id = l.id WHERE f.id = :facility_id");
            $statement->bindParam(':facility_id', $facilityId);
            $statement->execute();

            return $statement->fetchObject(Location::class);
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Finds multiple locations by a list of facility IDs.
     *
     * @param array $facilityIds
     * @return Location[]
     */
    public function findLocationsByFacilityIds(array $facilityIds): array
    {
        try {
            $placeholders = implode(',', array_fill(0, count($facilityIds), '?'));
            $statement = $this->connection->prepare("SELECT l.id, l.city, l.address, l.zip_code, l.country_code, l.phone_number, f.id AS facility_id 
            FROM Location l JOIN Facility f ON f.location_id = l.id 
            WHERE f.id IN ($placeholders)
        ");
            $statement->execute($facilityIds);
            $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

            $locations = [];
            foreach ($rows as $row) {
                $location = $this->createNewLocation($row);
                $locations[$row['facility_id']] = $location;
            }
            return $locations;
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Creates a new Location object from a database row.
     *
     * @param array $row Associative array with location data.
     * @return Location
     */
    private function createNewLocation(array $row): Location
    {
        $location = new Location();
        $location->setId($row['id']);
        $location->setAddress($row['address']);
        $location->setCity($row['city']);
        $location->setZipCode($row['zip_code']);
        $location->setCountryCode($row['country_code']);
        $location->setPhoneNumber($row['phone_number']);
        return $location;
    }
}