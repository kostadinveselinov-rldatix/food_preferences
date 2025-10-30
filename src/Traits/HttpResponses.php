<?php
namespace App\Traits;

trait HttpResponses
{
    public function jsonResponse(array $data = [], string $message = "", int $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');

        if(empty($data)) {
            return json_encode([
            'status' => $statusCode,
            'message' => $message,
        ]);
        }

        return json_encode([
            'status' => $statusCode,
            'message' => $message,
            'data' => $data
        ]);
    }

    public function redirect(string $url, int $statusCode = 302)
    {
        header("Location: $url", true, $statusCode);
        // exit();
    }

    public function errorResponse(string $message, int $statusCode = 400)
    {
        return $this->jsonResponse([],$message, $statusCode);
    }
}