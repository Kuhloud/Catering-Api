<?php

namespace App\Controllers;

use App\Services\FacilityService;
use App\Services\LocationService;
use App\Services\TagService;

use App\Plugins\Http\Response as Status;

class FacilityController extends BaseController
{
    private FacilityService $facilityService;
    private LocationService $locationService;
    private TagService $tagService;

    public function __construct()
    {
        $this->facilityService = new FacilityService();
        $this->locationService = new LocationService();
        $this->tagService = new TagService();
    }

    public function createFacility()
    {
        if (!$this->isPost())
        {
            http_response_code(405); // Method Not Allowed
            return;
        }
        $data = $this->getJsonDataAsObject();
        $new_facility_id = $this->facilityService->createFacility($data->name, $data->location_id);
        // Extract tags from json and turn them lowercase
        if (isset($data->tags))
        {
            $tags = array_map('strtolower', $data->tags);
            $this->tagService->createFacilityTags($tags, $new_facility_id);
        }
    }
    public function readFacility($facility_id)
    {
        if (!$this->isGet())
        {
            http_response_code(405); // Method Not Allowed
            return;
        }
        $facility = $this->facilityService->readFacility($facility_id);
        $facility->setLocation($this->locationService->readLocationByFacilityId($facility_id));
        $facility->setTags($this->tagService->getTagsByFacilityId($facility_id));
        echo json_encode(['facility' => $facility]);
    }
    public function updateFacility($facility_id)
    {
        if (!$this->isPut())
        {
            http_response_code(405); // Method Not Allowed
            return;
        }
        $data = $this->getJsonDataAsObject();
        $this->facilityService->updateFacility($facility_id, $data->name ?? null, $data->location_id ?? null);
        if (isset($data->tags))
        {
            $tags = array_map('strtolower', $data->tags);
            $this->tagService->updateFacilityTags($tags, $facility_id);
        }
    }
    public function deleteFacility($facility_id)
    {
        if (!$this->isDelete())
        {
            http_response_code(405); // Method Not Allowed
            return;
        }
        $this->facilityService->deleteFacility($facility_id);
    }
}