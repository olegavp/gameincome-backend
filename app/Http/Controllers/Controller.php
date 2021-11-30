<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="GameInCome",
 *      description="Description of API accessor methods for GameInCome",
 *      @OA\Contact(
 *          email="karlossan@me.com",
 *          name="Support",
 *      ),
 *      @OA\License(
 *          name="License 1.0 (Demo)",
 *          url="L5_SWAGGER_CONST_HOST",
 *      ),
 * ),
 *
 * @OA\SecurityScheme(
 *   securityScheme="bearer_token",
 *   type="http",
 *   scheme="bearer",
 * ),
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Production API Server",
 * ),
 * @OA\Server(
 *      url="https://game-back.karlossan.com/api/v1",
 *      description="Stage API Server",
 * ),
 *
 * @OA\Tag(name = "Personal Area: Purchases",
 *     description = "API Endpoints of personal area"
 * ),
 * @OA\Tag(name = "Personal Area: Sales",
 *     description = "API Endpoints of personal area"
 * ),
 * @OA\Tag(name = "Personal Area: Finance",
 *     description = "API Endpoints of personal area",
 * )
 * @OA\Tag(name = "Personal Area: Appeals",
 *     description = "API Endpoints of personal area",
 * )
 * @OA\Tag(name="Socialite",
 *     description="API Endpoints of socialite"
 * ),
 *
 * @OA\Tag(
 *     name="OLD_Authentication & Authorization",
 *     description="API Endpoints of authentication and authorization"
 * ),
 * @OA\Tag(
 *     name="OLD_Registration",
 *     description="API Endpoints of registration"
 * ),
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
