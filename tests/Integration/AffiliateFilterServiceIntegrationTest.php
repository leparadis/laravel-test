<?php
namespace Tests\Integration;

use App\Services\AffiliateFilterService;
use App\DTOs\AffiliateDTO;
use PHPUnit\Framework\TestCase;

class AffiliateFilterServiceIntegrationTest extends \Tests\TestCase
{
    private AffiliateFilterService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AffiliateFilterService::class);
    }

    public function testFilterAffiliatesIntegration()
    {
        $affiliates = [
            new AffiliateDTO(53.3340285, -6.2535495, 1, 'John'), // Dublin office (0 km)
            new AffiliateDTO(53.2451022, -6.2386537, 2, 'Alice'), // ~10 km from Dublin
            new AffiliateDTO(54.0466251, -6.1862909, 3, 'Bob'), // ~78 km from Dublin
            new AffiliateDTO(51.5074, -0.1278, 4, 'Charlie'), // London (~464 km from Dublin)
        ];

        $result = $this->service->filterAffiliates($affiliates);

        $this->assertCount(3, $result);
        $this->assertEquals([
            ['affiliateId' => 1, 'name' => 'John'],
            ['affiliateId' => 2, 'name' => 'Alice'],
            ['affiliateId' => 3, 'name' => 'Bob'],
        ], $result);
    }

    public function testFilterAffiliatesLargeDatasetIntegration()
    {
        $affiliates = array_merge(
            [new AffiliateDTO(53.3340285, -6.2535495, 1, 'John')], // Dublin office
            array_map(function ($i) {
                return new AffiliateDTO($i + 2, "Affiliate $i", rand(52, 54), rand(-7, -5));
            }, range(0, 999))
        );

        $result = $this->service->filterAffiliates($affiliates);

        $this->assertGreaterThan(1, count($result));
        $this->assertLessThan(1001, count($result));
        $this->assertEquals(1, $result[0]['affiliateId']);
    }
}
