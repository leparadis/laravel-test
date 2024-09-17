<?php

namespace Tests\Unit;

use App\Services\FileReaderService;
use App\DTOs\AffiliateDTO;
use PHPUnit\Framework\TestCase;

class FileReaderServiceTest extends TestCase
{
    private FileReaderService $service;
    private string $testDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FileReaderService();
        $this->testDir = sys_get_temp_dir() . '/file_reader_test_' . uniqid();
        mkdir($this->testDir);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->testDir);
        parent::tearDown();
    }

    private function removeDirectory($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object)) {
                        $this->removeDirectory($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }

    public function testReadFileSuccessfully()
    {
        $content = '{"affiliate_id": 1, "name": "John Doe", "latitude": 52.986375, "longitude": -6.043701}' . PHP_EOL .
            '{"affiliate_id": 2, "name": "Jane Smith", "latitude": 51.92893, "longitude": -10.27699}';
        $filePath = $this->testDir . '/affiliates.txt';
        file_put_contents($filePath, $content);

        $result = iterator_to_array($this->service->readFile($filePath));

        $this->assertCount(2, $result);
        $this->assertInstanceOf(AffiliateDTO::class, $result[0]);
        $this->assertEquals(1, $result[0]->affiliateId);
        $this->assertEquals("John Doe", $result[0]->name);
        $this->assertEquals(52.986375, $result[0]->latitude);
        $this->assertEquals(-6.043701, $result[0]->longitude);
    }

    public function testReadFileWithInvalidJson()
    {
        $content = '{"affiliate_id": 1, "name": "John Doe", "latitude": 52.986375, "longitude": -6.043701}' . PHP_EOL .
            'invalid json' . PHP_EOL .
            '{"affiliate_id": 2, "name": "Jane Smith", "latitude": 51.92893, "longitude": -10.27699}';
        $filePath = $this->testDir . '/affiliates_invalid.txt';
        file_put_contents($filePath, $content);

        $result = iterator_to_array($this->service->readFile($filePath));

        $this->assertCount(2, $result);
        $this->assertEquals(1, $result[0]->affiliateId);
        $this->assertEquals(2, $result[1]->affiliateId);
    }

    public function testReadEmptyFile()
    {
        $filePath = $this->testDir . '/empty.txt';
        file_put_contents($filePath, '');

        $result = iterator_to_array($this->service->readFile($filePath));

        $this->assertEmpty($result);
    }

    public function testReadFileWithMaxLineLengthExceeded()
    {
        $longLine = str_repeat('a', 11) . PHP_EOL;
        $filePath = $this->testDir . '/long.txt';
        file_put_contents($filePath, $longLine);

        $result = iterator_to_array($this->service->readFile($filePath, 10));

        $this->assertEmpty($result);
    }

    public function testFileNotFound()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unable to open file: nonexistent.txt');

        iterator_to_array($this->service->readFile('nonexistent.txt'));
    }
}
