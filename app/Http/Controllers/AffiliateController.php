<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;

use App\Services\FileReaderService;
use App\Services\AffiliateFilterService;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AffiliateController extends Controller
{
    public function __construct(
        private FileReaderService $fileReaderService,
        private AffiliateFilterService $affiliateFilterService
    ) {}

    public function index(): StreamedResponse
    {
        $filePath = Storage::path('affiliates.txt');

        $affiliatesGenerator = $this->fileReaderService->readFile($filePath);

        return response()->stream(
            function () use ($affiliatesGenerator) {
                echo '[';
                $first = true;

                foreach ($affiliatesGenerator as $affiliate) {
                    if (!$first) {
                        echo ',';
                    }
                    $first = false;

                    $jsonAffiliate = json_encode($affiliate);
                    if ($jsonAffiliate === false) {
                        continue;
                    }
                    echo $jsonAffiliate;

                    flush();
                }

                echo ']';
            },
            200,
            [
                'Content-Type' => 'application/json',
                'X-Accel-Buffering' => 'no', // Disable nginx buffering
                'Cache-Control' => 'no-cache',
            ]
        );
    }

    public function filtered(): StreamedResponse
    {
        $filePath = Storage::path('affiliates.txt');
        $affiliatesGenerator = $this->fileReaderService->readFile($filePath);

        $matchingAffiliates = $this->affiliateFilterService->filterAffiliates($affiliatesGenerator);

        return response()->stream(
            function () use ($matchingAffiliates) {
                ob_start();
                echo '[';
                foreach ($matchingAffiliates as $index => $affiliate) {
                    if ($index > 0) {
                        echo ',';
                    }
                    $jsonAffiliate = json_encode($affiliate);
                    if ($jsonAffiliate === false) {
                        continue;
                    }
                    echo $jsonAffiliate;
                    if (ob_get_length() > 65536) {  // 64KB buffer
                        ob_end_flush();
                        flush();
                        ob_start();
                    }
                }
                echo ']';
                ob_end_flush();
            },
            200,
            [
                'Content-Type' => 'application/json',
                'X-Accel-Buffering' => 'no',
                'Cache-Control' => 'no-cache',
            ]
        );
    }
}
