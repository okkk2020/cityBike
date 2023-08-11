<?php

namespace citybike\task\Parsers;

use citybike\task\Exceptions\InvalidXMLDataException;
use InvalidArgumentException;

class XMLParser implements ParserInterface
{

    /**
     * @throws InvalidXMLDataException
     */
    public function parseBikersData($filePath): array
    {
        $parsedData = [];
        $xml = simplexml_load_file($filePath);
        if (!Validator::validateXMLData($xml)) {
            throw new InvalidArgumentException('Invalid XML data');
        }
        foreach ($xml->biker as $biker) {
            $attributes = $biker->attributes();
            $parsedData[] = [
                'count' => (string)$attributes['count'],
                'latitude' => (string)$attributes['latitude'],
                'longitude' => (string)$attributes['longitude'],
            ];
        }

        return $parsedData;
    }
}