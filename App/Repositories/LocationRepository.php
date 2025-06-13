<?php

namespace App\Repositories;

use App\Models\Location;
use PDO;
use PDOException;

class LocationRepository extends Repository
{
    public function findLocationByFacilityId(int $facilityId): ?Location
    {
        try {
            $statement = $this->connection->prepare("SELECT l.* FROM Location l JOIN Facility f ON f.location_id = l.location_id WHERE facility_id = :facility_id");
            $statement->bindParam(':facility_id', $facilityId);
            $statement->execute();

            return $statement->fetchObject(Location::class);
        } catch (PDOException $e) {
            return null;
        }
    }
    public function findLocationsByFacilityIds($facilityIds): array
    {
        try {
            $placeholders = implode(',', array_fill(0, count($facilityIds), '?'));
            $statement = $this->connection->prepare("SELECT l.*, f.facility_id FROM Location l 
        JOIN Facility f ON f.location_id = l.location_id 
        WHERE f.facility_id IN ($placeholders)
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
    private function createNewLocation($row): Location
    {
        $location = new Location();
        $location->setLocationId($row['location_id']);
        $location->setAddress($row['address']);
        $location->setCity($row['city']);
        $location->setZipCode($row['zip_code']);
        $location->setCountryCode($row['country_code']);
        $location->setPhoneNumber($row['phone_number']);
        return $location;
    }
}