<?php

declare(strict_types=1);

namespace App\Controllers;

class BaseController
{
    protected function getUriSegments(): array
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode( '/', $uri );

        return $uri;
    }

    protected function getQueryStringParams(): array
    {
        parse_str($_SERVER['QUERY_STRING'], $query);

        return $query;
    }

    protected function getPostParams(): array
    {
        $requestBody = file_get_contents('php://input');

        return $requestBody ? json_decode($requestBody, true) : [];
    }

    protected function sendOutput(mixed $data): void
    {
        if (is_array($data)) {
            header('Content-Type: application/json');

            echo json_encode($data);
        } else {
            echo $data;
        }

        exit;
    }
}