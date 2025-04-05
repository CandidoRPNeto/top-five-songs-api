<?php

namespace Tests\Unit;

use App\Actions\GetParametersFromLink;
use PHPUnit\Framework\TestCase;

class GetParametersFromLinkTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_get_all_song_information(): void
    {
        $params = GetParametersFromLink::execute('https://www.youtube.com/watch?v=s9kVG2ZaTS4');
        $this->assertTrue(true);

        $this->assertArrayHasKey('title', $params);
        $this->assertArrayHasKey('views', $params);
        $this->assertArrayHasKey('youtube_id', $params);
        $this->assertArrayHasKey('thumb', $params);

        $this->assertEquals('O mineiro e o italiano', $params['title']);
        $this->assertEquals('s9kVG2ZaTS4', $params['youtube_id']);
        $this->assertEquals('https://img.youtube.com/vi/s9kVG2ZaTS4/hqdefault.jpg', $params['thumb']);
    }
}
