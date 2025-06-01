<?php

namespace App\Repositories;

use App\Models\Facility;

class FacilityRepository extends Repository
{
    public function createFacility($facility_name, $facility_location)
    {
        $statement = $this->connection->prepare("INSERT INTO Facility (name, location_id) VALUES (:name, :location_id)");
        $statement->bindParam(':name', $facility_name);
        $statement->bindParam(':location_id', $facility_location);
        $statement->execute();

        return $this->connection->lastInsertId();
    }
    public function deleteFacility($facility_id)
    {
        $statement = $this->connection->prepare("DELETE FROM Facility WHERE facility_id = :facility_id");
        $statement->bindParam(':facility_id', $facility_id);
        $statement->execute();
    }
}