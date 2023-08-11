<?php

namespace citybike\task\Tests;

use citybike\task\Exceptions\InvalidXMLDataException;
use citybike\task\Parsers\CSVParser;
use citybike\task\Parsers\XMLParser;
use PHPUnit\Framework\TestCase;

class BikeStationParserTest extends TestCase
{
    public function testCSVParser()
    {
        $csvParser = new CSVParser();
        $csvFilePath = __DIR__ . "/../data/bikers.csv";
        $parsedBikersData = $csvParser->parseBikersData($csvFilePath);

        $expectedBikersData = [
            ['count' => 2, 'latitude' => 45.69233, 'longitude' => 9.65931],
            ['count' => 1, 'latitude' => 45.69654, 'longitude' => 9.65897],
            ['count' => 0, 'latitude' => 45.67831, 'longitude' => 9.67516],
            ['count' => 4, 'latitude' => 45.716909, 'longitude' => 9.716649],
        ];

        self::assertEquals($expectedBikersData, $parsedBikersData);
    }

    /**
     * @throws InvalidXMLDataException
     */
    public function testXMLParser()
    {
        $parser = new XMLParser();
        $xmlFilePath = __DIR__ . "/../data/bikers.xml";
        $parsedData = $parser->parseBikersData($xmlFilePath);

        $this->assertEquals([
            ['count' => '2', 'latitude' => '45.69233', 'longitude' => '9.65931'],
            ['count' => '1', 'latitude' => '45.69654', 'longitude' => '9.65897'],
        ], $parsedData);
    }
}