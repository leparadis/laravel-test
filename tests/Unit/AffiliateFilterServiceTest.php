<?php

namespace Tests\Unit;

use App\Services\AffiliateFilterService;
use App\DTOs\AffiliateDTO;
use PHPUnit\Framework\TestCase;

class AffiliateFilterServiceTest extends TestCase
{
    private AffiliateFilterService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AffiliateFilterService();
    }

    public function testFilterAffiliatesWithinRange()
    {
        $affiliates = [
            new AffiliateDTO(53.3340285, -6.2535495, 1, 'John'), // Dublin office (0 km)
            new AffiliateDTO(53.2451022, -6.2386537, 2, 'Alice'), // ~10 km from Dublin
            new AffiliateDTO(54.0466251, -6.1862909, 3, 'Bob'), // ~78 km from Dublin
        ];

        $result = $this->service->filterAffiliates($affiliates);

        $this->assertCount(3, $result);
        $this->assertEquals([
            ['affiliateId' => 1, 'name' => 'John'],
            ['affiliateId' => 2, 'name' => 'Alice'],
            ['affiliateId' => 3, 'name' => 'Bob'],
        ], $result);
    }

    public function testFilterAffiliatesOutsideRange()
    {
        $affiliates = [
            new AffiliateDTO(53.3340285,-6.2535495, 1, 'John' ), // Dublin office (0 km)
            new AffiliateDTO(51.5074,-0.1278, 2, 'Alice'), // London (~464 km from Dublin)
            new AffiliateDTO(48.8566, 2.3522, 3, 'Bob'), // Paris (~780 km from Dublin)
        ];

        $result = $this->service->filterAffiliates($affiliates);

        $this->assertCount(1, $result);
        $this->assertEquals([
            ['affiliateId' => 1, 'name' => 'John'],
        ], $result);
    }

    public function testFilterAffiliatesEmptyInput()
    {
        $result = $this->service->filterAffiliates([]);

        $this->assertEmpty($result);
    }

    public function testFilterAffiliatesSorting()
    {
        $affiliates = [
            new AffiliateDTO(53.3340285, -6.2535495, 3, 'Bob',),
            new AffiliateDTO(53.3340285, -6.2535495, 1, 'John'),
            new AffiliateDTO(53.3340285,-6.2535495, 2, 'Alice'),
        ];

        $result = $this->service->filterAffiliates($affiliates);

        $this->assertCount(3, $result);
        $this->assertEquals([
            ['affiliateId' => 1, 'name' => 'John'],
            ['affiliateId' => 2, 'name' => 'Alice'],
            ['affiliateId' => 3, 'name' => 'Bob'],
        ], $result);
    }

    public function testFilterAffiliatesExactlyAtMaxDistance()
    {
        $affiliates = [
            new AffiliateDTO(53.3340285, -6.2535495, 1, 'John'), // Dublin office (0 km)
            new AffiliateDTO(52.4796992, -6.5651216, 2, 'Alice'), // Exactly 100 km from Dublin
        ];

        $result = $this->service->filterAffiliates($affiliates);

        $this->assertCount(2, $result);
        $this->assertEquals([
            ['affiliateId' => 1, 'name' => 'John'],
            ['affiliateId' => 2, 'name' => 'Alice'],
        ], $result);
    }

    public function testFilterAffiliatesSlightlyOverMaxDistance()
    {
        $affiliates = [
            new AffiliateDTO(53.3340285, -6.2535495, 1, 'John'), // Dublin office (0 km)
            new AffiliateDTO(52.4746992, -6.5651216, 2, 'Alice'), // ~100.5 km from Dublin
        ];

        $result = $this->service->filterAffiliates($affiliates);

        $this->assertCount(2, $result);
        $this->assertEquals([
            ['affiliateId' => 1, 'name' => 'John'],
            ['affiliateId' => 2, 'name' => 'Alice'],
        ], $result);
    }

    public function testFilterAffiliatesWithInvalidCoordinates()
    {
        $affiliates = [
            new AffiliateDTO(53.3340285, -6.2535495, 1, 'John'), // Dublin office (0 km)
            new AffiliateDTO(91, 180, 2, 'Alice'), // Invalid coordinates
            new AffiliateDTO(-91, -180, 3, 'Bob'), // Invalid coordinates
        ];

        $result = $this->service->filterAffiliates($affiliates);

        $this->assertCount(1, $result);
        $this->assertEquals([
            ['affiliateId' => 1, 'name' => 'John'],
        ], $result);
    }

    public function testFilterAffiliatesWithMixedValidAndInvalidData()
    {
        $affiliates = [
            new AffiliateDTO(53.3340285, -6.2535495, 1, 'John'), // Dublin office (0 km)
            new AffiliateDTO(53.2451022, -6.2386537, 2, 'Alice'), // ~10 km from Dublin
            new AffiliateDTO(91, 180, 3, 'Bob'), // Invalid coordinates
            new AffiliateDTO(51.5074, -0.1278, 4, 'Charlie'), // London (~464 km from Dublin)
        ];

        $result = $this->service->filterAffiliates($affiliates);

        $this->assertCount(2, $result);
        $this->assertEquals([
            ['affiliateId' => 1, 'name' => 'John'],
            ['affiliateId' => 2, 'name' => 'Alice'],
        ], $result);
    }
}
