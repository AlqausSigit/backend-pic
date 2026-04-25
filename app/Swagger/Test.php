<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/test",
 *     operationId="testAPI",
 *     tags={"Test"},
 *     @OA\Response(
 *         response=200,
 *         description="OK"
 *     )
 * )
 */
class Test {}
