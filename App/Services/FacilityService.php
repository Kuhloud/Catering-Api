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

    /**
     * @throws Exception
     */
    public function createFacility(string $facilityName, int $facilityLocation, ?array $tags = null): Facility
    {
        $facilityId = $this->facilityRepository->createFacility($facilityName, $facilityLocation);
        if (isset($tags))
        {
            $tags = $this->convertTagsToLowerCase($tags);
            $this->tagService->createFacilityTags($tags, $facilityId);
        }
        return $this->readFacility($facilityId);
    }

    /**
     * Gets Location and Tags from their respective Services
     * @throws Exception
     */
    public function readFacility(int $facilityId): Facility
    {
        if (!$this->facilityRepository->facilityExists($facilityId)) {
            throw new Exception("Facility not found");
        }
        $facility = $this->facilityRepository->findFacilityByFacilityId($facilityId);
        $facility->setLocation($this->locationService->findLocationByFacilityId($facilityId));
        $facility->setTags($this->tagService->findTagsByFacilityId($facilityId));
        return $facility;
    }
    /**
     *
     * Gets Location and Tags from their respective Services
     * @return Facility[]
     */
    public function readFacilities(?string $facilityName = null, ?string $tagName = null, ?string $locationCity = null): array
    {
        $facilities = $this->facilityRepository->findFacilitiesByFilter($facilityName, $tagName, $locationCity);
        $ids = array_map(fn($facility) => $facility->getFacilityId(), $facilities);
        $locations = $this->locationService->findLocationsByFacilityIds($ids);
        $tags = $this->tagService->findTagsByFacilityIds($ids);

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
    public function updateFacility(int $facilityId, string $facilityName, ?int $locationId = null, ?array $tags = null): ?Facility
    {
        if (!$this->facilityRepository->facilityExists($facilityId)) {
            return null;
        }
        $this->facilityRepository->updateFacility($facilityId, $facilityName, $locationId);
        if (isset($tags))
        {
            $tags = $this->convertTagsToLowerCase($tags);
            $this->tagService->updateFacilityTags($tags, $facilityId);
        }
        return $this->readFacility($facilityId);
    }

    /**
     * @throws Exception
     */
    public function deleteFacility(int $facilityId): bool
    {
        return $this->facilityRepository->deleteFacility($facilityId);
    }
    private function convertTagsToLowerCase(array $tags): array
    {
        return $tags ? array_map('strtolower', $tags) : [];
    }
}