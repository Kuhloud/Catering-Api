<?php

namespace App\Repositories;

use App\Models\Facility;
use PDO;

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
    public function readFacility($facility_id)
    {
        $statement = $this->connection->prepare("SELECT facility_id, name, creation_date FROM Facility WHERE facility_id = :facility_id");
        $statement->bindParam(':facility_id', $facility_id);
        $statement->execute();

        return $statement->fetchObject(Facility::class);
    }
    public function readFacilities()
    {
        $statement = $this->connection->prepare("SELECT facility_id, name, creation_date FROM Facility");
        $statement->execute();

        //return $statement->fetchAll(PDO::FETCH_ASSOC);
        return $statement->fetchAll(PDO::FETCH_CLASS, Facility::class);
    }
    public function updateFacility($facility_id, $facility_name, $location_id)
    {
        $statement = $this->connection->prepare("UPDATE Facility SET name = :name, location_id = :location_id WHERE facility_id = :facility_id");
        $statement->bindParam(':facility_id', $facility_id);
        $statement->bindParam(':location_id', $location_id);
        $statement->bindParam(':name', $facility_name);
        $statement->execute();
    }
    public function deleteFacility($facility_id)
    {
        $statement = $this->connection->prepare("DELETE FROM Facility WHERE facility_id = :facility_id");
        $statement->bindParam(':facility_id', $facility_id);
        $statement->execute();
    }
}