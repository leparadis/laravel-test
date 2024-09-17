<?php

namespace Tests\Integration;
use App\Services\FileReaderService;
use App\DTOs\AffiliateDTO;
use PHPUnit\Framework\TestCase;

class FileReaderServiceIntegrationTest extends \Tests\TestCase
{
    private FileReaderService $service;
    private string $testFilePath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FileReaderService();
        $this->testFilePath = storage_path('test_affiliates.txt');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testFilePath)) {
            unlink($this->testFilePath);
        }
        parent::tearDown();
    }

    public function testReadFileIntegration()
    {
        $content = '{"affiliate_id": 1, "name": "John Doe", "latitude": 52.986375, "longitude": -6.043701}' . PHP_EOL .
            '{"affiliate_id": 2, "name": "Jane Smith", "latitude": 51.92893, "longitude": -10.27699}';
        file_put_contents($this->testFilePath, $content);

        $result = iterator_to_array($this->service->readFile($this->testFilePath));

        $this->assertCount(2, $result);
        $this->assertInstanceOf(AffiliateDTO::class, $result[0]);
        $this->assertEquals(1, $result[0]->affiliateId);
        $this->assertEquals("John Doe", $result[0]->name);
        $this->assertEquals(52.986375, $result[0]->latitude);
        $this->assertEquals(-6.043701, $result[0]->longitude);
    }

    public function testReadLargeFileIntegration()
    {
        $content = '';
        for ($i = 0; $i < 1000; $i++) {
            $content .= json_encode([
                    'affiliate_id' => $i,
                    'name' => "Affiliate $i",
                    'latitude' => rand(50, 54),
                    'longitude' => rand(-10, -5)
                ]) . PHP_EOL;
        }
        file_put_contents($this->testFilePath, $content);

        $result = iterator_to_array($this->service->readFile($this->testFilePath));

        $this->assertCount(1000, $result);
        $this->assertInstanceOf(AffiliateDTO::class, $result[0]);
        $this->assertEquals(0, $result[0]->affiliateId);
        $this->assertEquals(999, $result[999]->affiliateId);
    }
}
