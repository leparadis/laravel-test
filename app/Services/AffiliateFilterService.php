<?php

namespace App\Services;


class AffiliateFilterService
{
    private const EARTH_RADIUS = 6371; // Earth's radius in kilometers
    private const DUBLIN_OFFICE_LAT = 53.3340285;
    private const DUBLIN_OFFICE_LON = -6.2535495;
    private const MAX_DISTANCE = 100; // kilometers

    public function filterAffiliates(iterable $affiliates): array
    {
        $matchingAffiliates = [];

        foreach ($affiliates as $affiliate) {
            $distance = $this->calculateDistance(
                self::DUBLIN_OFFICE_LAT,
                self::DUBLIN_OFFICE_LON,
                $affiliate->latitude,
                $affiliate->longitude
            );

            if ($distance <= self::MAX_DISTANCE) {
                $matchingAffiliates[] = [
                    'affiliateId' => $affiliate->affiliateId,
                    'name' => $affiliate->name,
                ];
            }
        }

        // Sort by Affiliate ID (ascending)
        usort($matchingAffiliates, fn($a, $b) => $a['affiliateId'] <=> $b['affiliateId']);

        return $matchingAffiliates;
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $deltaLat = $lat2 - $lat1;
        $deltaLon = $lon2 - $lon1;

        $a = sin($deltaLat/2) * sin($deltaLat/2) +
            cos($lat1) * cos($lat2) *
            sin($deltaLon/2) * sin($deltaLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return self::EARTH_RADIUS * $c;
    }
}
