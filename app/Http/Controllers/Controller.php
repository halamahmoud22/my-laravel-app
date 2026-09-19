<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Attributes as OA;

#[OA\Info(
    title: "My Laravel Product API",
    version: "1.0.0",
    description: "API for managing products, with token-based authentication via Laravel Sanctum."
)]
#[OA\Server(
    url: L5_SWAGGER_CONST_HOST,
    description: "API server"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT",
    description: "Enter the token you received from /api/login, without the word 'Bearer'."
)]
#[OA\Server(url: 'http://127.0.0.1:8000', description: 'Local dev server')]
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
