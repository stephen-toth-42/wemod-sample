<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class LinkTest extends TestCase
{
    /**
     * Tests the incrementHits model method using syncOriginal to simulate
     * saving since the method uses getOriginal
     */
    public function test_hits_incrementing(): void
    {
        $link = new \App\Models\Link([
            'long_url' => 'http://www.something.com',
            'hits' => 41,
        ]);
        $link->syncOriginal();
        // var_dump($link);
        $this->assertEquals(41, $link->hits);
        $link->incrementHits();
        $link->syncOriginal();
        $this->assertEquals(42, $link->hits);
    }
}
