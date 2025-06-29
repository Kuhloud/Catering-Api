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

    /**
     * Finds location with the facilityId
     */
    public function findLocationByFacilityId(int $facilityId): Location
    {
        return $this->locationRepository->findLocationByFacilityId($facilityId);
    }

    /**
     * Finds location for facilities with multiple facilityIds
     * @return Location[]
     */
    public function findLocationsByFacilityIds(array $facilityIds): array
    {
        return $this->locationRepository->findLocationsByFacilityIds($facilityIds);
    }
}