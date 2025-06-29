<?php


namespace App\Controllers;

use App\Plugins\Di\Injectable;

class BaseController extends Injectable {
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    protected function isGet(): bool
    {
        return $_SERVER['REQUEST_METHOD'] == 'GET';
    }

    protected function isPut(): bool
    {
        return $_SERVER['REQUEST_METHOD'] == 'PUT';
    }

    protected function isDelete(): bool
    {
        return $_SERVER['REQUEST_METHOD'] == 'DELETE';
    }

    protected function getJsonDataAsObject()
    {
        header('Content-Type: application/json');
        $json = file_get_contents('php://input');
        return json_decode($json);
    }

    protected function sendSuccessResponse(): void
    {
        header('Content-Type: application/json');
        http_response_code(200);
    }

    protected function sendErrorResponse(string $message): void
    {
        http_response_code(500);
        $this->encodeErrorJson($message);
    }

    protected function sendBadRequestResponse(string $message): void
    {
        http_response_code(400);
        $this->encodeErrorJson($message);
    }

    protected function sendMethodNotAllowedResponse(string $message): void
    {
        http_response_code(405);
        $this->encodeErrorJson($message);
    }

    protected function sendNotFoundResponse(string $message): void
    {
        http_response_code(404);
        $this->encodeErrorJson($message);
    }

    protected function sendNoContentResponse(): void
    {
        http_response_code(204);
    }

    private function encodeErrorJson(string $message): void
    {
        header('Content-Type: application/json');
        echo json_encode(['Error' => $message]);
    }

}
