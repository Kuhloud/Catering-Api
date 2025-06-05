<?php

namespace App\Repositories;

use App\Models\Location;

class LocationRepository extends Repository
{
    public function readLocationByFacilityId($facility_id)
    {
        $statement = $this->connection->prepare("SELECT l.* FROM Location l JOIN Facility f ON f.location_id = l.location_id WHERE facility_id = :facility_id");
        $statement->bindParam(':facility_id', $facility_id);
        $statement->execute();

        return $statement->fetchObject(Location::class);
    }
}