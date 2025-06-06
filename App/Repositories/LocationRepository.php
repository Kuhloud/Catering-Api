<?php

namespace App\Repositories;

use App\Models\Location;
use PDO;

class LocationRepository extends Repository
{
    public function readLocationByFacilityId($facility_id)
    {
        $statement = $this->connection->prepare("SELECT l.* FROM Location l JOIN Facility f ON f.location_id = l.location_id WHERE facility_id = :facility_id");
        $statement->bindParam(':facility_id', $facility_id);
        $statement->execute();

        return $statement->fetchObject(Location::class);
    }
    public function readLocationsByFacilityIds($facility_ids)
    {
        $placeholders = implode(',', array_fill(0, count($facility_ids), '?'));
        $statement = $this->connection->prepare("SELECT l.*, f.facility_id FROM Location l 
        JOIN Facility f ON f.location_id = l.location_id 
        WHERE f.facility_id IN ($placeholders)
    ");
        $statement->execute($facility_ids);
        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        $locations = [];
        foreach ($rows as $row) {
            // Associate by facility_id, not name/tag_id
            $location = new Location();
            $location->setLocationId($row['location_id']);
            $location->setAddress($row['address']);
            $location->setCity($row['city']);
            $location->setZipCode($row['zip_code']);
            $location->setCountryCode($row['country_code']);
            $location->setPhoneNumber($row['phone_number']);
            $locations[$row['facility_id']] = $location;
        }
        return $locations; // e.g. [1 => LocationObject, 2 => LocationObject]
    }
}