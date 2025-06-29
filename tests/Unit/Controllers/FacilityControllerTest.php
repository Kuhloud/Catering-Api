<?php

namespace Unit\Controllers;

use PHPUnit\Framework\TestCase;
use App\Controllers\FacilityController;
use App\Services\FacilityService;
use App\Models\Facility;
use Exception;
use ReflectionClass;

class FacilityControllerTest extends TestCase
{
    private FacilityController $controller;
    private $facilityServiceMock;

    protected function setUp(): void
    {
        $this->facilityServiceMock = $this->createMock(FacilityService::class);

        $this->controller = $this->getMockBuilder(FacilityController::class)
            ->onlyMethods([
                'isPost', 'isGet', 'isPut', 'isDelete',
                'sendMethodNotAllowedResponse',
                'sendBadRequestResponse',
                'sendSuccessResponse',
                'sendErrorResponse',
                'sendNotFoundResponse',
                'sendNoContentResponse',
                'getJsonDataAsObject'
            ])
            ->getMock();

        $this->controller->setFacilityServiceForTests($this->facilityServiceMock);
    }

    // ----- createFacility tests -----

    public function testCreateFacilityMethodNotAllowed()
    {
        $this->controller->method('isPost')->willReturn(false);
        $this->controller->expects($this->once())->method('sendMethodNotAllowedResponse');

        $this->controller->createFacility();
    }

    public function testCreateFacilityBadRequest()
    {
        $this->controller->method('isPost')->willReturn(true);
        $this->controller->method('getJsonDataAsObject')->willReturn(null);
        $this->controller->expects($this->once())->method('sendBadRequestResponse');

        $this->controller->createFacility();
    }

    public function testCreateFacilityThrowsException()
    {
        $data = (object) ['name' => 'Test', 'location_id' => 1];

        $this->controller->method('isPost')->willReturn(true);
        $this->controller->method('getJsonDataAsObject')->willReturn($data);

        $this->facilityServiceMock->method('createFacility')->willThrowException(new Exception());

        $this->controller->expects($this->once())->method('sendErrorResponse');

        ob_start();
        $this->controller->createFacility();
        ob_end_clean();
    }

    public function testCreateFacilitySuccess()
    {
        $data = (object) ['name' => 'Test Facility', 'location_id' => 1, 'tags' => ['tag1']];

        $this->controller->method('isPost')->willReturn(true);
        $this->controller->method('getJsonDataAsObject')->willReturn($data);

        $facilityMock = $this->createMock(Facility::class);

        $this->facilityServiceMock->expects($this->once())
            ->method('createFacility')
            ->with('Test Facility', 1, ['tag1'])
            ->willReturn($facilityMock);

        $this->controller->expects($this->once())->method('sendSuccessResponse');

        ob_start();
        $this->controller->createFacility();
        $output = ob_get_clean();

        $this->assertStringContainsString('New Facility', $output);
    }

    // ----- readFacility tests -----

    public function testReadFacilityMethodNotAllowed()
    {
        $this->controller->method('isGet')->willReturn(false);
        $this->controller->expects($this->once())->method('sendMethodNotAllowedResponse');

        $this->controller->readFacility(1);
    }

    public function testReadFacilityThrowsException()
    {
        $this->controller->method('isGet')->willReturn(true);

        $this->facilityServiceMock->method('readFacility')->willThrowException(new Exception());

        $this->controller->expects($this->once())->method('sendErrorResponse');

        ob_start();
        $this->controller->readFacility(1);
        ob_end_clean();
    }

    public function testReadFacilitySuccess()
    {
        $facilityMock = $this->createMock(Facility::class);

        $this->controller->method('isGet')->willReturn(true);

        $this->facilityServiceMock->expects($this->once())
            ->method('readFacility')
            ->with(123)
            ->willReturn($facilityMock);

        $this->controller->expects($this->once())->method('sendSuccessResponse');

        ob_start();
        $this->controller->readFacility(123);
        $output = ob_get_clean();

        $this->assertStringContainsString('Facility', $output);
    }

    // ----- readFacilities tests -----

    public function testReadFacilitiesMethodNotAllowed()
    {
        $this->controller->method('isGet')->willReturn(false);
        $this->controller->expects($this->once())->method('sendMethodNotAllowedResponse');

        $this->controller->readFacilities();
    }

    public function testReadFacilitiesThrowsException()
    {
        $this->controller->method('isGet')->willReturn(true);

        $this->facilityServiceMock->method('readFacilities')->willThrowException(new Exception());

        $this->controller->expects($this->once())->method('sendErrorResponse');

        ob_start();
        $this->controller->readFacilities();
        ob_end_clean();
    }

    public function testReadFacilitiesSuccess()
    {
        $facilities = ['facility1', 'facility2'];

        $this->controller->method('isGet')->willReturn(true);

        // Emulate $_GET parameters:
        $_GET['name'] = 'foo';
        $_GET['tag'] = 'bar';
        $_GET['city'] = 'baz';

        $this->facilityServiceMock->expects($this->once())
            ->method('readFacilities')
            ->with('foo', 'bar', 'baz')
            ->willReturn($facilities);

        $this->controller->expects($this->once())->method('sendSuccessResponse');

        ob_start();
        $this->controller->readFacilities();
        $output = ob_get_clean();

        $this->assertStringContainsString('facility1', $output);

        // Cleanup $_GET to avoid side effects
        unset($_GET['name'], $_GET['tag'], $_GET['city']);
    }

    // ----- updateFacility tests -----

    public function testUpdateFacilityMethodNotAllowed()
    {
        $this->controller->method('isPut')->willReturn(false);
        $this->controller->expects($this->once())->method('sendMethodNotAllowedResponse');

        $this->controller->updateFacility(1);
    }

    public function testUpdateFacilityBadRequest()
    {
        $this->controller->method('isPut')->willReturn(true);
        $this->controller->method('getJsonDataAsObject')->willReturn(null);

        $this->controller->expects($this->once())->method('sendBadRequestResponse');

        $this->controller->updateFacility(1);
    }

    public function testUpdateFacilityNotFound()
    {
        $data = (object) ['name' => 'Updated'];

        $this->controller->method('isPut')->willReturn(true);
        $this->controller->method('getJsonDataAsObject')->willReturn($data);

        $this->facilityServiceMock->expects($this->once())
            ->method('updateFacility')
            ->willReturn(null);

        $this->controller->expects($this->once())->method('sendNotFoundResponse');

        $this->controller->updateFacility(123);
    }

    public function testUpdateFacilityThrowsException()
    {
        $data = (object) ['name' => 'Updated'];

        $this->controller->method('isPut')->willReturn(true);
        $this->controller->method('getJsonDataAsObject')->willReturn($data);

        $this->facilityServiceMock->method('updateFacility')->willThrowException(new Exception());

        $this->controller->expects($this->once())->method('sendErrorResponse');

        ob_start();
        $this->controller->updateFacility(1);
        ob_end_clean();
    }

    public function testUpdateFacilitySuccess()
    {
        $data = (object) ['name' => 'Updated', 'location_id' => 5];

        $this->controller->method('isPut')->willReturn(true);
        $this->controller->method('getJsonDataAsObject')->willReturn($data);

        $facilityMock = $this->createMock(Facility::class);

        $this->facilityServiceMock->expects($this->once())
            ->method('updateFacility')
            ->with(1, 'Updated', 5, null)
            ->willReturn($facilityMock);

        $this->controller->expects($this->once())->method('sendSuccessResponse');

        ob_start();
        $this->controller->updateFacility(1);
        $output = ob_get_clean();

        $this->assertStringContainsString('Updated Facility', $output);
    }

    // ----- deleteFacility tests -----

    public function testDeleteFacilityMethodNotAllowed()
    {
        $this->controller->method('isDelete')->willReturn(false);
        $this->controller->expects($this->once())->method('sendMethodNotAllowedResponse');

        $this->controller->deleteFacility(1);
    }

    public function testDeleteFacilityNotFound()
    {
        $this->controller->method('isDelete')->willReturn(true);

        $this->facilityServiceMock->method('deleteFacility')->willReturn(false);

        $this->controller->expects($this->once())->method('sendNotFoundResponse');

        $this->controller->deleteFacility(123);
    }

    public function testDeleteFacilityThrowsException()
    {
        $this->controller->method('isDelete')->willReturn(true);

        $this->facilityServiceMock->method('deleteFacility')->willThrowException(new Exception());

        $this->controller->expects($this->once())->method('sendErrorResponse');

        ob_start();
        $this->controller->deleteFacility(1);
        ob_end_clean();
    }

    public function testDeleteFacilitySuccess()
    {
        $this->controller->method('isDelete')->willReturn(true);

        $this->facilityServiceMock->method('deleteFacility')->willReturn(true);

        $this->controller->expects($this->once())->method('sendNoContentResponse');

        $this->controller->deleteFacility(1);
    }

    // ----- setFacilityServiceForTests -----

    /**
     * @throws \PHPUnit\Framework\MockObject\Exception
     */
    public function testSetFacilityServiceForTests()
    {
        // Create a real instance (not a mock)
        $realController = new FacilityController();

        $newServiceMock = $this->createMock(FacilityService::class);

        $realController->setFacilityServiceForTests($newServiceMock);

        $reflection = new ReflectionClass($realController);
        $property = $reflection->getProperty('facilityService');
        $property->setAccessible(true);

        $this->assertSame($newServiceMock, $property->getValue($realController));
    }
}