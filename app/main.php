<?php

require_once __DIR__ . '/../vendor/autoload.php'; // Autoload dependencies

use citybike\task\ParserFactory;
use citybike\task\Services\BikeStationFinder;
use citybike\task\Services\CityBikeApi;

$city = $argv[1];
$filePath = __DIR__ . "/../data/bikers.csv";

$cityBikeApi = new CityBikeApi();
$parser = ParserFactory::createParser($filePath);
$bikeStationFinder = new BikeStationFinder($cityBikeApi, $parser);
$bikeStationFinder->findClosestStations($city, $filePath);
