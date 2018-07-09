<?php

namespace App\Core\Contracts;

interface APIInterface
{
    public function get($request, $response, ...$params);
    public function post($request, $response, ...$params);
    public function put($request, $response, ...$params);
    public function patch($request, $response, ...$params);
    public function delete($request, $response, ...$params);
}
