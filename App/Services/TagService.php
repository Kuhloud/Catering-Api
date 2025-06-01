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
    public function createTag($tags)
    {
        $tag_ids = [];
        foreach ($tags as $tag) {
            $existing_id = $this->tagRepository->getTagIdByName($tag);
            if (!$existing_id) {
                $tag_ids[] = $this->tagRepository->createTags($tag);
            }
            else {
                $tag_ids[] = $existing_id;
            }
        }
        return $tag_ids;
    }
    public function createFacilityTags($tag_ids, $new_facility_id)
    {
        foreach ($tag_ids as $tag_id) {
            $this->tagRepository->createFacilityTags($tag_id, $new_facility_id);
        }
    }
}