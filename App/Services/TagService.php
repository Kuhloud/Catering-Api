<?php

namespace App\Services;

use App\Models\Tag;
use App\Repositories\TagRepository;

class TagService
{
    private TagRepository $tagRepository;
    function __construct()
    {
        $this->tagRepository = new TagRepository();
    }

    /**
     * Processes tag names and returns their IDs, creating new tags as needed.
     */
    public function createTags(array $tags): array
    {
        $existingTags = $this->tagRepository->findTagIdsByName($tags);
        $tagIds = [];
        foreach ($tags as $tag) {
            if (!isset($existingTags[$tag])) {
                $tagIds[] = $this->tagRepository->createTag($tag);
            } else {
                $tagIds[] = $existingTags[$tag];
            }
        }
        return $tagIds;
    }

    /**
     * Gets all tags for a specific facility.
     */
    public function findTagsByFacilityId(int $facilityId): array
    {
        return $this->tagRepository->findTagsByFacilityId($facilityId);
    }

    /**
     * Gets tags for multiple facilities in a single query.
     *
     * @return array<int, Tag[]> Map of facility IDs to their tags
     */
    public function findTagsByFacilityIds(array $facilityIds): array
    {
        return $this->tagRepository->findTagsByFacilityIds($facilityIds);
    }

    /**
     * Deletes old Facility_Tag data in the database, and replaces them.
     */
    public function updateFacilityTags(array $tags, int $facilityId): void
    {
        $this->tagRepository->deleteFacilityTagsByFacility($facilityId);
        if (empty($tags)) {
            return;
        }
        $this->createFacilityTags($tags, $facilityId);
    }

    /**
     * Creates new tags first, then adds it to a junction table with the facilityId.
     */
    public function createFacilityTags(array $tags, int $facilityId): void
    {
        $tagIds = $this->createTags($tags);
        foreach ($tagIds as $tagId) {
            $this->tagRepository->createFacilityTags($tagId, $facilityId);
        }
    }

}