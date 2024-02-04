<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Http\JsonResponse;
use Tests\TestCase;

class HealthControllerTest extends TestCase
{
    public function testHealth(): void
    {
        $this->get('/api/health')
            ->assertStatus(JsonResponse::HTTP_OK)
            ->assertJson(['health' => 'OK']);
    }
}
