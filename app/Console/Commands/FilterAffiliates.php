<?php

namespace App\Console\Commands;

use App\Services\FileReaderService;
use App\Services\AffiliateFilterService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FilterAffiliates extends Command
{
    protected $signature = 'affiliates:filter';
    protected $description = 'Filter affiliates within 100km of Dublin office';

    public function __construct(
        private FileReaderService $fileReaderService,
        private AffiliateFilterService $affiliateFilterService
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $filePath = Storage::path('affiliates.txt');
        $affiliatesGenerator = $this->fileReaderService->readFile($filePath);

        $matchingAffiliates = $this->affiliateFilterService->filterAffiliates($affiliatesGenerator);

        $this->info("Affiliates within 100km of Dublin office:");
        foreach ($matchingAffiliates as $affiliate) {
            $this->line("ID: {$affiliate['affiliateId']}, Name: {$affiliate['name']}");
        }
    }
}
