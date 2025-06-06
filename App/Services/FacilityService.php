<?php

namespace App\Services;

use App\Repositories\FacilityRepository;

class FacilityService
{
    private FacilityRepository $facilityRepository;
    private LocationService $locationService;
    private TagService $tagService;

    function __construct()
    {
        $this->facilityRepository = new FacilityRepository();
        $this->locationService = new LocationService();
        $this->tagService = new TagService();
    }
    public function createFacility($facility_name, $facility_location)
    {
        return $this->facilityRepository->createFacility($facility_name, $facility_location);
    }
    public function readFacility($facility_id)
    {
        $facility = $this->facilityRepository->readFacility($facility_id);
        $facility->setLocation($this->locationService->readLocationByFacilityId($facility_id));
        $facility->setTags($this->tagService->readTagsByFacilityId($facility_id));
        return $facility;
    }
    public function readFacilities()
    {
        $facilities = $this->facilityRepository->readFacilities();
        $ids = array_map(fn($facility) => $facility->getFacilityId(), $facilities);
        $locations = $this->locationService->readLocationsByFacilityIds($ids);
        $tags = $this->tagService->readTagsByFacilityIds($ids);

        foreach ($facilities as $facility) {
            $id = $facility->getFacilityId();
            $facility->setLocation($locations[$id] ?? null);
            $facility->setTags($tags[$id] ?? []);
        }
        return $facilities;
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