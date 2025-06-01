<?php


namespace App\Controllers;

use App\Plugins\Di\Injectable;

class BaseController extends Injectable {
    protected function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }

    protected function isGet()
    {
        return $_SERVER['REQUEST_METHOD'] == 'GET';
    }
    protected function isPut()
    {
        return $_SERVER['REQUEST_METHOD'] == 'PUT';
    }
    protected function isDelete()
    {
        return $_SERVER['REQUEST_METHOD'] == 'DELETE';
    }
    protected function getJsonDataAsObject()
    {
        $json = file_get_contents('php://input');
        return json_decode($json);
    }
    // Ik heb geleerd dat een associative array een array is waarbij de keys iets anders zijn dan objecten zijn.
    protected function getJsonDataAsAssociativeArray()
    {
        $json = file_get_contents('php://input');
        return json_decode($json, true);
    }
    protected function returnResponseData($response)
    {
        return json_encode($response);
    }
    protected function returnErrorData()
    {
        return json_encode(['error' => 'Data is not set']);
    }
    protected function filterString($string)
    {
        return $string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    protected function userCheckForImportantData()
    {
        if (!$this->isPost() ||!isset($_SESSION['account']) || !$_SESSION['account']->role == 'admin') {
            return false;
        }
        return true;
    }
    protected function filterInt($int)
    {
        $int = filter_var($int, FILTER_VALIDATE_INT);
        return filter_var($int, FILTER_SANITIZE_NUMBER_INT);
    }

}
