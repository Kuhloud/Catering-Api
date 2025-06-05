<?php

namespace App\Services;

use App\Repositories\FacilityRepository;

class FacilityService
{
    private FacilityRepository $facilityRepository;

    function __construct()
    {
        $this->facilityRepository = new FacilityRepository();
    }
    public function createFacility($facility_name, $facility_location)
    {
        return $this->facilityRepository->createFacility($facility_name, $facility_location);
    }
    public function readFacility($facility_id)
    {
        return $this->facilityRepository->readFacility($facility_id);
    }
    public function updateFacility($facility_id, $facility_name, $location_id = null)
    {
        $this->facilityRepository->updateFacility($facility_id, $facility_name, $location_id);
    }
    public function deleteFacility($facility_id)
    {
        $this->facilityRepository->deleteFacility($facility_id);
    }
}