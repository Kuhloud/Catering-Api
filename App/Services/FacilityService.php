<?php

namespace App\Services;

use App\Models\Facility;
use App\Repositories\FacilityRepository;
use Exception;

class FacilityService extends BaseService
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
     * Creates a new facility with a name and locationId. Tags can also be optionally added.
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
        if (empty($facilities)) {
            return [];
        }
        return $this->convertFacilitiesToFacilityObject($facilities);
    }

    /**
     * Coverts the ids of $facilities and uses it to fetch locations and tags for each id.
     * Then the data gets turned into an array of Facility objects.
     * @return Facility[]
     */
    private function convertFacilitiesToFacilityObject(array $facilities): array
    {
        $ids = array_map(fn($facility) => $facility->getId(), $facilities);
        $locations = $this->locationService->findLocationsByFacilityIds($ids);
        $tags = $this->tagService->findTagsByFacilityIds($ids);

        foreach ($facilities as $facility) {
            $id = $facility->getId();
            $facility->setLocation($locations[$id]);
            $facility->setTags($tags[$id] ?? []);
        }
        return $facilities;
    }

    /**
     * Updates a facility with a body with optional name, location, and optional tags via a facilityId
     * @throws Exception
     */
    public function updateFacility(int $facilityId, string $facilityName, ?int $locationId = null, ?array $tags = null): ?Facility
    {
        if (!$this->facilityRepository->facilityExists($facilityId)) {
            return null;
        }
        $this->facilityRepository->updateFacility($facilityId, $facilityName, $locationId);
        $tags = $this->convertTagsToLowerCase($tags);
        $this->tagService->updateFacilityTags($tags, $facilityId);
        return $this->readFacility($facilityId);
    }

    /**
     * Deletes a facility with facilityId
     * @throws Exception
     */
    public function deleteFacility(int $facilityId): bool
    {
        return $this->facilityRepository->deleteFacility($facilityId);
    }

}