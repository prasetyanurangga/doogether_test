<?php

declare(strict_types=1);

namespace app\Factory;

use Yii;
use app\Dto\ApiResponseData;

final class ApiResponseDataFactory
{

    public function createSuccessResponse(?array $data, string $message): ApiResponseData
    {
        $this->setHeader(200);
        return $this->createResponse()
            ->setStatus(200)
            ->setMessage($message)
            ->setData($data);
    }

    public function createErrorResponse(string $message): ApiResponseData
    {
        $this->setHeader(500);
        return $this->createResponse()
            ->setStatus(500)
            ->setMessage($message);
    }

    public function createResponse(): ApiResponseData
    {
        return new ApiResponseData();
    }

    private function getErrorMessage(DataResponse $response): string
    {
        $data = $response->getData();
        if (is_string($data) && !empty($data)) {
            return $data;
        }

        return 'Unknown error';
    }

    protected function setHeader($status)
    {

        $text = $this->_getStatusCodeMessage($status);

        Yii::$app->response->setStatusCode($status, $text);

        $status_header = 'HTTP/1.1 ' . $status . ' ' . $text;
        $content_type = "application/json; charset=utf-8";


        header($status_header);
        header('Content-type: ' . $content_type);
        header('Access-Control-Allow-Origin:*');


    }

    protected function _getStatusCodeMessage($status)
    {
        // these could be stored in a .ini file and loaded
        // via parse_ini_file()... however, this will suffice
        // for an example
        $codes = Array(
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
        );
        return (isset($codes[$status])) ? $codes[$status] : '';
    }
}
