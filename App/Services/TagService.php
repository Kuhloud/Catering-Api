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
        $existing_tags = $this->tagRepository->getTagByName($tags);
        $tag_ids = [];
        foreach ($tags as $tag) {
            if (!isset($existing_tags[$tag])) {
                $tag_ids[] = $this->tagRepository->createTag($tag);
            } else {
                $tag_ids[] = $existing_tags[$tag];
            }
        }
        return $tag_ids;
    }
    public function getTagsByFacilityId($facility_id)
    {
        return $this->tagRepository->getTagsByFacilityId($facility_id);
    }
    public function updateFacilityTags($tags, $facility_id)
    {
        $this->tagRepository->deleteFacilityTagsByFacility($facility_id);
        $this->createFacilityTags($tags, $facility_id);
    }
    public function createFacilityTags($tags, $facility_id)
    {
        $tag_ids = $this->createTags($tags);
        foreach ($tag_ids as $tag_id) {
            $this->tagRepository->createFacilityTags($tag_id, $facility_id);
        }
    }
}