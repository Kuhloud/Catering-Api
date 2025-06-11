<?php

namespace App\Services;

use App\Models\Facility;
use App\Repositories\FacilityRepository;
use Exception;

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
    public function createFacility($facilityName, $facilityLocation, $tags = null): Facility
    {
        $facilityId = $this->facilityRepository->createFacility($facilityName, $facilityLocation);
        if (isset($tags))
        {
            $tags = array_map('strtolower', $tags);
            $this->tagService->updateFacilityTags($tags, $facilityId);
        }
        return $this->readFacility($facilityId);
    }

    /**
     * @throws Exception
     */
    public function readFacility($facilityId): Facility
    {
        if (!$this->facilityRepository->facilityExists($facilityId)) {
            throw new Exception("Facility not found");
        }
        $facility = $this->facilityRepository->readFacility($facilityId);
        $facility->setLocation($this->locationService->readLocationByFacilityId($facilityId));
        $facility->setTags($this->tagService->readTagsByFacilityId($facilityId));
        return $facility;
    }
    /**
     * @return Facility[]
     */
    public function readFacilities(?string $facilityName = null, ?string $tagName = null, ?string $locationCity = null): array
    {
        $facilities = $this->facilityRepository->readFacilities($facilityName, $tagName, $locationCity);
        $ids = array_map(fn($facility) => $facility->getFacilityId(), $facilities);
        $locations = $this->locationService->readLocationsByFacilityIds($ids);
        $tags = $this->tagService->readTagsByFacilityIds($ids);

        foreach ($facilities as $facility) {
            $id = $facility->getFacilityId();
            $facility->setLocation($locations[$id]);
            $facility->setTags($tags[$id] ?? []);
        }
        return $facilities;
    }

    /**
     * @throws Exception
     */
    public function updateFacility($facilityId, $facilityName, $locationId = null, $tags = null)
    {
        if (!$this->facilityRepository->facilityExists($facilityId)) {
            throw new Exception("Facility not found");
        }
        $this->facilityRepository->updateFacility($facilityId, $facilityName, $locationId);
        if (isset($tags))
        {
            $tags = array_map('strtolower', $tags);
            $this->tagService->updateFacilityTags($tags, $facilityId);
        }
        return $this->readFacility($facilityId);
    }

    /**
     * @throws Exception
     */
    public function deleteFacility($facilityId): bool
    {
        return $this->facilityRepository->deleteFacility($facilityId);
    }
}