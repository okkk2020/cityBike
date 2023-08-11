<?php

namespace citybike\task\Parsers;

use citybike\task\Exceptions\InvalidCSVDataException;
use citybike\task\Exceptions\InvalidXMLDataException;

class Validator
{
    /**
     * @throws InvalidXMLDataException
     */
    public static function validateXMLData($data): bool
    {
        try {
            foreach ($data->biker as $biker) {
                if (!isset($biker['count'], $biker['latitude'], $biker['longitude'])) {
                    throw new InvalidXMLDataException('Each line should have exactly 3 values');
                }

                // Validate 'count' as a valid integer
                if (!filter_var((string)$biker['count'], FILTER_VALIDATE_INT)) {
                    throw new InvalidXMLDataException("'count' is not a valid integer");
                }

                // Validate 'latitude' and 'longitude' as valid float values
                if (!filter_var((string)$biker['latitude'], FILTER_VALIDATE_FLOAT) ||
                    !filter_var((string)$biker['longitude'], FILTER_VALIDATE_FLOAT)) {
                    throw new InvalidXMLDataException("'latitude' or 'longitude' is not a valid float");
                }
            }
            return true;
        } catch (\Exception $e) {
            throw new InvalidXMLDataException("XML data validation failed: " . $e->getMessage());
        }
    }

    /**
     * @throws InvalidCSVDataException
     */
    public static function validateCSVData($data): bool
    {
        // Skip the header line
        if (count($data) > 0) {
            unset($data[0]);
        }
        try {
            foreach ($data as $line) {
                $biker_info = explode(',', $line);

                if (count($biker_info) !== 3) {
                    throw new InvalidCSVDataException('Each line should have exactly 3 values');
                }

                $count = trim($biker_info[0]);
                // Validate 'count' as a valid integer using regex
                if (!preg_match('/^\d+$/', $count)) {
                    throw new InvalidCSVDataException("'count' is not a valid integer");
                }

                // Validate 'latitude' and 'longitude' as valid float values
                if (!filter_var($biker_info[1], FILTER_VALIDATE_FLOAT) ||
                    !filter_var($biker_info[2], FILTER_VALIDATE_FLOAT)) {
                    throw new InvalidCSVDataException("'latitude' or 'longitude' is not a valid float");
                }
            }
            return true;
        } catch (\Exception $e) {
            throw new InvalidCSVDataException("CSV data validation failed: " . $e->getMessage());
        }
    }
}
