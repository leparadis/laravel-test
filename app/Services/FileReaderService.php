<?php

namespace App\Services;

use App\DTOs\AffiliateDTO;
use Generator;

class FileReaderService
{
    public function readFile(string $filePath, int $maxLineLength = 1024 * 1024): Generator
    {
        $handle = fopen($filePath, 'r');

        if ($handle === false) {
            throw new \RuntimeException("Unable to open file: $filePath");
        }

        while (!feof($handle)) {
            $line = stream_get_line($handle, $maxLineLength, "\n");
            if ($line === false) {
                break;
            }
            $data = json_decode($line, true);
            if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
                continue;
            }
            if (is_array($data)) {
                yield AffiliateDTO::fromArray($data);
            }
        }

        fclose($handle);
    }
}
