<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Psr\SimpleCache\CacheInterface;

class FeatureTestCase extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        /** @var CacheInterface $cache */
        $cache = $this->app->make(CacheInterface::class);

        $cache->clear();
    }
}
