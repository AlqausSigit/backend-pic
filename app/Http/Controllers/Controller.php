<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Backend PIC API Documentation",
 *      description="L5 Swagger OpenApi description"
 * )
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      type="apiKey",
 *      name="Authorization",
 *      in="header",
 *      description="Bearer Token"
 * )
 */
abstract class Controller extends BaseController
{
    //
}