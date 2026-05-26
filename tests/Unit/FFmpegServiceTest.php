<?php

namespace Tests\Unit;

use App\Services\FFmpegService;
use Tests\TestCase;

class FFmpegServiceTest extends TestCase
{
    /**
     * Test that the atempo filter chain is built correctly for normal speed.
     */
    public function test_atempo_normal_speed(): void
    {
        $service = new FFmpegService();
        $filters = $this->invokeMethod($service, 'buildAtempoFilters', [1.5]);

        $this->assertCount(1, $filters);
        $this->assertEquals('atempo=1.5', $filters[0]);
    }

    /**
     * Test that the atempo filter chain is correctly chained for speed > 2.0.
     */
    public function test_atempo_chains_above_two(): void
    {
        $service = new FFmpegService();
        $filters = $this->invokeMethod($service, 'buildAtempoFilters', [4.0]);

        // Should chain: atempo=2.0, atempo=2.0
        $this->assertCount(2, $filters);
        $this->assertEquals('atempo=2', $filters[0]);
        $this->assertEquals('atempo=2', $filters[1]);
    }

    /**
     * Test that atempo is correctly chained for speed < 0.5.
     */
    public function test_atempo_chains_below_half(): void
    {
        $service = new FFmpegService();
        $filters = $this->invokeMethod($service, 'buildAtempoFilters', [0.25]);

        // Should chain: atempo=0.5, atempo=0.5
        $this->assertCount(2, $filters);
        $this->assertEquals('atempo=0.5', $filters[0]);
        $this->assertEquals('atempo=0.5', $filters[1]);
    }

    /**
     * Helper to call protected/private methods via reflection.
     */
    protected function invokeMethod(object $object, string $method, array $params = []): mixed
    {
        $reflection = new \ReflectionClass($object);
        $m = $reflection->getMethod($method);
        $m->setAccessible(true);
        return $m->invokeArgs($object, $params);
    }
}
