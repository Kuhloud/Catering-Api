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
    public function deleteFacility($facility_id)
    {
        $this->facilityRepository->deleteFacility($facility_id);
    }
}