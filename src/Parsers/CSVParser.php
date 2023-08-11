<?php

namespace citybike\task\Parsers;

use citybike\task\Exceptions\InvalidCSVDataException;

class CSVParser implements ParserInterface
{
    public function parseBikersData($filePath): array
    {
        $bikers_data = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        try {
            if (!Validator::validateCSVData($bikers_data)) {
                echo "CSV data validation failed.\n";
            }
        } catch (InvalidCSVDataException $e) {
            echo "CSV data validation error: " . $e->getMessage() . "\n";
        }
        // Remove the first line
        array_shift($bikers_data);
        $bikers = [];

        foreach ($bikers_data as $line) {
            $biker_info = explode(',', $line);
            $bikers[] = [
                "count" => $biker_info[0],
                "latitude" => $biker_info[1],
                "longitude" => $biker_info[2],
            ];
        }

        return $bikers;
    }

}