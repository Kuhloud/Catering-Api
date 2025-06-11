<?php

namespace App\Controllers;

use App\Services\FacilityService;
use App\Services\LocationService;
use App\Services\TagService;

use App\Plugins\Http\Response as Status;
use Exception;

class FacilityController extends BaseController
{
    private FacilityService $facilityService;

    public function __construct()
    {
        $this->facilityService = new FacilityService();
    }

    public function createFacility()
    {
        if (!$this->isPost())
        {
            http_response_code(405); // Method Not Allowed
            return;
        }
        $data = $this->getJsonDataAsObject();
        if (!$data || !isset($data->location_id)) {
            http_response_code(400); // Bad Request
            echo json_encode(['Error' => 'Fill in all required fields']);
            return;
        }
        try {
            {
                $newFacility = $this->facilityService->createFacility($data->name, $data->location_id, $data->tags ?? null);
                http_response_code(200); // OK
                echo json_encode(['New Facility' => $newFacility]);
            }
        } catch (Exception $e) {
            http_response_code(400); // Bad Request
            echo json_encode(['Error' => "Could not create facility"]);
        }
    }
    public function readFacility($facilityId)
    {
        if (!$this->isGet())
        {
            http_response_code(405); // Method Not Allowed
            return;
        }
        try {
            $facility = $this->facilityService->readFacility($facilityId);
            http_response_code(200); // OK
            echo json_encode(['Facility' => $facility]);
        } catch (Exception $e) {
            http_response_code(400); // Bad Request
            echo json_encode(['Error' => $e->getMessage()]);
        }
    }
    public function readFacilities()
    {
        if (!$this->isGet())
        {
            http_response_code(405); // Method Not Allowed
            return;
        }
        try {
            $facilities = $this->facilityService->readFacilities($_GET['name'] ?? null, $_GET['tag'] ?? null, $_GET['city'] ?? null);
            http_response_code(200); // OK
            echo json_encode(['Facilities' => $facilities]);
        } catch (Exception $e) {
            http_response_code(400); // Bad Request
            echo json_encode(['Error' => "Could not read facilities"]);
        }
    }
    public function updateFacility($facilityId)
    {
        if (!$this->isPut())
        {
            http_response_code(405); // Method Not Allowed
            return;
        }
        $data = $this->getJsonDataAsObject();
        if (!$data) {
            http_response_code(400); // Bad Request
            echo json_encode(['Error' => 'Nothing to update']);
            return;
        }
        try {
            $updatedFacility = $this->facilityService->updateFacility($facilityId, $data->name ?? null, $data->location_id ?? null, $data->tags ?? null);
            http_response_code(200); // OK
            echo json_encode(['Updated Facility' => $updatedFacility]);
        } catch (Exception $e) {
            http_response_code(400); // Bad Request
            echo json_encode(['Error' => $e->getMessage()]);
        }
    }
    public function deleteFacility($facilityId)
    {
        if (!$this->isDelete())
        {
            http_response_code(405); // Method Not Allowed
            return;
        }
        try {
            $deletedFacility = $this->facilityService->deleteFacility($facilityId);
            http_response_code(204); // No Content
            echo json_encode(['Facility Deleted']);
        } catch (Exception $e) {
            http_response_code(400); // Bad Request
            echo json_encode(['Error' => "Could not delete facility"]);
        }
    }
}