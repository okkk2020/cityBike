<?php

namespace citybike\task\Services;

use citybike\task\Parsers\ParserInterface;

class BikeStationFinder
{
    private CityBikeApi $cityBikeApi;
    private ParserInterface $parser;

    public function __construct(CityBikeApi $cityBikeApi, ParserInterface $parser)
    {
        $this->cityBikeApi = $cityBikeApi;
        $this->parser = $parser;
    }

    public function findClosestStations($city, $filePath): void
    {
        $stationsData = $this->cityBikeApi->fetchStationsData($city);
        $bikersData = $this->parser->parseBikersData($filePath);
        foreach ($bikersData as $biker) {
            $closestStation = null;
            $minDistance = PHP_INT_MAX;

            foreach ($stationsData as $station) {
                $distance = DistanceCalculator::calculateDistance(
                    $biker['latitude'],
                    $biker['longitude'],
                    $station['latitude'],
                    $station['longitude']
                );

                if ($distance < $minDistance) {
                    $closestStation = $station;
                    $minDistance = $distance;
                }
            }
            // Print the closest stations and relevant data
            if ($closestStation) {
                $jsonOutput = json_encode($closestStation, JSON_PRETTY_PRINT);
                echo $jsonOutput . "\n";
            } else {
                echo "No closest station found.\n";
            }
        }
    }
}
