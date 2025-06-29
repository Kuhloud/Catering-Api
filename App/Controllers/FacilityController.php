<?php

namespace App\Controllers;

use App\Services\FacilityService;
use App\Services\LocationService;
use App\Services\TagService;

use App\Plugins\Http\Response as Status;
use Exception;
use InvalidArgumentException;

class FacilityController extends BaseController
{
    private FacilityService $facilityService;

    public function __construct()
    {
        $this->facilityService = new FacilityService();
    }

    /**
     * Creates a new facility
     * @return void
     */
    public function createFacility()
    {
        if (!$this->isPost())
        {
            $this->sendMethodNotAllowedResponse("Method is not allowed"); // Method Not Allowed
            return;
        }
        $data = $this->getJsonDataAsObject();
        if (!$data || !isset($data->location_id)) {
            $this->sendBadRequestResponse('Fill in all required fields: name, location_id'); // Bad Request
            return;
        }
        try {
            {
                $newFacility = $this->facilityService->createFacility($data->name, $data->location_id, $data->tags ?? null);
                $this->sendSuccessResponse(); // OK
                echo json_encode(['New Facility' => $newFacility]);
            }
        } catch (Exception $e) {
            $this->sendErrorResponse('Could not create facility'); // Internal Server Error
        }
    }

    /**
     * Finds a facility with facilityId
     *
     * @param $facilityId
     * @return void
     */
    public function readFacility($facilityId)
    {
        if (!$this->isGet())
        {
            $this->sendMethodNotAllowedResponse("Method is not allowed"); // Method Not Allowed
            return;
        }
        try {
            $facility = $this->facilityService->readFacility($facilityId);
            $this->sendSuccessResponse(); // OK
            echo json_encode(['Facility' => $facility]);
        } catch (Exception $e) {
            $this->sendErrorResponse('Could not read facility'); // Internal Server Error
        }
    }

    /**
     * Finds multiple facilities. Can be used with filters
     * @return void
     */
    public function readFacilities()
    {
        if (!$this->isGet())
        {
            $this->sendMethodNotAllowedResponse("Method is not allowed"); // Method Not Allowed
            return;
        }
        try {
            $facilities = $this->facilityService->readFacilities($_GET['name'] ?? null, $_GET['tag'] ?? null, $_GET['city'] ?? null);
            $this->sendSuccessResponse(); // OK
            echo json_encode($facilities);
        } catch (Exception $e) {
            $this->sendErrorResponse('Could not read facilities'); // Internal Server Error
        }
    }

    /**
     * Updates an existing facility. Name, location, and tags are optional.
     *
     * @param $facilityId
     * @return void
     */
    public function updateFacility($facilityId)
    {
        if (!$this->isPut())
        {
            $this->sendMethodNotAllowedResponse("Method is not allowed"); // Method Not Allowed
            return;
        }
        $data = $this->getJsonDataAsObject();
        if (!$data) {
            $this->sendBadRequestResponse("Nothing to update"); // Internal Server Error
            return;
        }
        try {
            $updatedFacility = $this->facilityService->updateFacility($facilityId, $data->name ?? null, $data->location_id ?? null, $data->tags ?? null);
            if (!$updatedFacility)
            {
                $this->sendNotFoundResponse('Facility not found'); // Not Found
                return;
            }
            $this->sendSuccessResponse(); // OK
            echo json_encode(['Updated Facility' => $updatedFacility]);
        } catch (Exception $e) {
            $this->sendErrorResponse('Could not update facility'); // Bad Request
        }
    }

    /**
     * Deletes an existing facility with facilityId
     *
     * @param int $facilityId
     * @return void
     */
    public function deleteFacility(int $facilityId): void
    {
        if (!$this->isDelete()) {
            $this->sendMethodNotAllowedResponse('Method is not allowed'); // Method Not Allowed
            return;
        }
        try {
            $wasDeleted = $this->facilityService->deleteFacility($facilityId);

            if (!$wasDeleted) {
                $this->sendNotFoundResponse('Facility not found'); // Not Found
                return;
            }
            $this->sendNoContentResponse();
        } catch (Exception $e) {
            $this->sendErrorResponse('Could not delete facility'); // Internal Server Error
        }
    }

    /**
     * Sets FacilityService for tests
     *
     */
    public function setFacilityServiceForTests(FacilityService $service): void
    {
        $this->facilityService = $service;
    }

}