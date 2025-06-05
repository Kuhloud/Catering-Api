<?php

namespace App\Services;

use App\Repositories\LocationRepository;

class LocationService
{
    private $locationRepository;
    public function __construct()
    {
        $this->locationRepository = new LocationRepository();
    }
    public function readLocationByFacilityId($facility_id)
    {
        return $this->locationRepository->readLocationByFacilityId($facility_id);
    }
}