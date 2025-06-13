<?php

namespace App\Services;

use App\Models\Location;
use App\Repositories\LocationRepository;

class LocationService
{
    private LocationRepository $locationRepository;
    public function __construct()
    {
        $this->locationRepository = new LocationRepository();
    }
    public function findLocationByFacilityId(int $facilityId): Location
    {
        return $this->locationRepository->findLocationByFacilityId($facilityId);
    }
    /**
     * @return Location[]
     */
    public function findLocationsByFacilityIds(array $facilityIds): array
    {
        return $this->locationRepository->findLocationsByFacilityIds($facilityIds);
    }
}