<?php

namespace Tests\Unit;

use App\Services\Portal\PortalIdNormalizer;
use Tests\TestCase;

class PortalIdNormalizerTest extends TestCase
{
    public function test_sahibinden_normalization(): void
    {
        $n = new PortalIdNormalizer();
        $this->assertSame('163868-6', $n->normalizeProviderId('sahibinden', '163 868 - 6'));
        $this->assertSame('123456', $n->normalizeProviderId('sahibinden', ' 123 456 '));
    }
}