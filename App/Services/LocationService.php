<?php

namespace App\Services;

use App\Models\Location;
use App\Repositories\LocationRepository;

class LocationService
{
    private $locationRepository;
    public function __construct()
    {
        $this->locationRepository = new LocationRepository();
    }
    public function readLocationByFacilityId($facilityId): Location
    {
        return $this->locationRepository->readLocationByFacilityId($facilityId);
    }
    /**
     * @return Location[]
     */
    public function readLocationsByFacilityIds($facility_ids): array
    {
        return $this->locationRepository->readLocationsByFacilityIds($facility_ids);
    }
}