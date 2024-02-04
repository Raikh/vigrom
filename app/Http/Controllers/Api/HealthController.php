<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class HealthController extends Controller
{
    public function test(): JsonResponse
    {
        return Response::json(['health' => 'OK']);
    }
}
