<?php

namespace App\Services;

use App\Repositories\TagRepository;

class TagService
{
    private TagRepository $tagRepository;
    function __construct()
    {
        $this->tagRepository = new TagRepository();
    }
    private function createTags($tags)
    {
        $existingTags = $this->tagRepository->readTagIdByName($tags);
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
    public function readTagsByFacilityId($facilityId)
    {
        return $this->tagRepository->readTagsByFacilityId($facilityId);
    }
    public function readTagsByFacilityIds($facilityIds)
    {
        return $this->tagRepository->readTagsByFacilityIds($facilityIds);
    }
    public function updateFacilityTags($tags, $facilityId)
    {
        $this->tagRepository->deleteFacilityTagsByFacility($facilityId);
        $this->createFacilityTags($tags, $facilityId);
    }
    public function createFacilityTags($tags, $facilityId)
    {
        $tagIds = $this->createTags($tags);
        foreach ($tagIds as $tagId) {
            $this->tagRepository->createFacilityTags($tagId, $facilityId);
        }
    }
}