<?php

namespace App\Controllers;

use App\Services\FacilityService;
use App\Services\TagService;

class FacilityController extends BaseController
{
    private FacilityService $facilityService;
    private TagService $tagService;
    public function __construct()
    {
        $this->facilityService = new FacilityService();
        $this->tagService = new TagService();
    }

    public function createFacility()
    {
        if (!$this->isPost())
        {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['error' => 'Only POST requests allowed']);
            return;
        }
        $data = $this->getJsonDataAsObject();
        // Extract tags from the input
        $tags = array_map('strtolower', $data->tags ?? []);
        $new_facility_id = $this->facilityService->createFacility($data->name, $data->location_id);
        $tag_ids = $this->tagService->createTag($tags);
        $this->tagService->createFacilityTags($tag_ids, $new_facility_id);
    }
    public function deleteFacility($facility_id)
    {
        if (!$this->isDelete())
        {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['error' => 'Only DELETE requests allowed']);
            return;
        }
        $this->facilityService->deleteFacility($facility_id);
    }
}