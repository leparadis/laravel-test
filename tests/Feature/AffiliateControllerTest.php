<?php

namespace Tests\Feature;

use App\Services\FileReaderService;
use App\Services\AffiliateFilterService;
use App\DTOs\AffiliateDTO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Mockery;

class AffiliateControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    public function testIndex()
    {
        $testData = [
            '{"affiliate_id": 1, "name": "John Doe", "latitude": 52.986375, "longitude": -6.043701}',
            '{"affiliate_id": 2, "name": "Jane Smith", "latitude": 51.92893, "longitude": -10.27699}'
        ];

        Storage::put('affiliates.txt', implode("\n", $testData));

        $response = $this->get('/api/affiliates');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');

        // Capture the streamed content
        $content = '';
        $response->sendContent();

        // Get the output from the output buffer
        $content = ob_get_clean();

        // Decode the JSON content
        $decodedContent = json_decode($content, true);

        // Assert that the content matches the expected data
        $expectedData = [
            ['affiliateId' => 1, 'name' => 'John Doe', 'latitude' => 52.986375, 'longitude' => -6.043701],
            ['affiliateId' => 2, 'name' => 'Jane Smith', 'latitude' => 51.92893, 'longitude' => -10.27699]
        ];
        $this->assertEquals($expectedData, $decodedContent);
    }

    public function testFiltered()
    {
        $mockFileReaderService = Mockery::mock(FileReaderService::class);
        $mockAffiliateFilterService = Mockery::mock(AffiliateFilterService::class);

        $this->app->instance(FileReaderService::class, $mockFileReaderService);
        $this->app->instance(AffiliateFilterService::class, $mockAffiliateFilterService);

        $testData = [
            new AffiliateDTO(52.986375, -6.043701, 1, 'John Doe'),
            new AffiliateDTO(51.92893, -10.27699, 2, 'Jane Smith'),
            new AffiliateDTO(53.3340285, -6.2535495, 3, 'Dublin Office')
        ];

        // Use a generator for FileReaderService
        $mockFileReaderService->shouldReceive('readFile')->once()->andReturn((function () use ($testData) {
            yield from $testData;
        })());

        $filteredData = [
            ['affiliateId' => 1, 'name' => 'John Doe'],
            ['affiliateId' => 3, 'name' => 'Dublin Office']
        ];

        $mockAffiliateFilterService->shouldReceive('filterAffiliates')->once()->andReturn($filteredData);

        $response = $this->get('/api/affiliates/filtered');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');

        // Capture the streamed content
        $content = '';
        $response->sendContent();

        // Get the output from the output buffer
        $content = ob_get_clean();

        // Decode the JSON content
        $decodedContent = json_decode($content, true);

        // Assert that the content matches the filtered data
        $this->assertEquals($filteredData, $decodedContent);
    }

    public function testIndexWithEmptyFile()
    {
        Storage::put('affiliates.txt', '');

        $response = $this->get('/api/affiliates');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');

        // Capture the streamed content
        $content = '';
        $response->sendContent();

        // Get the output from the output buffer
        $content = ob_get_clean();

        // Decode the JSON content
        $decodedContent = json_decode($content, true);

        // Assert that the content is an empty array
        $this->assertEquals([], $decodedContent);
    }

    public function testFilteredWithNoMatches()
    {
        $mockFileReaderService = Mockery::mock(FileReaderService::class);
        $mockAffiliateFilterService = Mockery::mock(AffiliateFilterService::class);

        $this->app->instance(FileReaderService::class, $mockFileReaderService);
        $this->app->instance(AffiliateFilterService::class, $mockAffiliateFilterService);

        $testData = [
            new AffiliateDTO(40.7128, -74.0060, 1, 'New York Office'),
            new AffiliateDTO(34.0522, -118.2437, 2, 'Los Angeles Office')
        ];

        $mockFileReaderService->shouldReceive('readFile')->once()->andReturn((function () use ($testData) {
            yield from $testData;
        })());

        $mockAffiliateFilterService->shouldReceive('filterAffiliates')->once()->andReturn([]);

        $response = $this->get('/api/affiliates/filtered');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');

        // Capture the streamed content
        $content = '';
        $response->sendContent();

        // Get the output from the output buffer
        $content = ob_get_clean();

        // Decode the JSON content
        $decodedContent = json_decode($content, true);

        // Assert that the content is an empty array
        $this->assertEquals([], $decodedContent);
    }

    public function testIndexWithInvalidJson()
    {
        $testData = [
            '{"affiliate_id": 1, "name": "John Doe", "latitude": 52.986375, "longitude": -6.043701}',
            'invalid json',
            '{"affiliate_id": 2, "name": "Jane Smith", "latitude": 51.92893, "longitude": -10.27699}'
        ];

        Storage::put('affiliates.txt', implode("\n", $testData));

        $response = $this->get('/api/affiliates');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/json');

        // Capture the streamed content
        $content = '';
        $response->sendContent();

        // Get the output from the output buffer
        $content = ob_get_clean();

        // Decode the JSON content
        $decodedContent = json_decode($content, true);

        // Assert the content matches what we expect
        $this->assertEquals([
            ['affiliateId' => 1, 'name' => 'John Doe', 'latitude' => 52.986375, 'longitude' => -6.043701],
            ['affiliateId' => 2, 'name' => 'Jane Smith', 'latitude' => 51.92893, 'longitude' => -10.27699]
        ], $decodedContent);
    }
}
