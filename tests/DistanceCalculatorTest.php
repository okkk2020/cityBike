<?php

namespace citybike\task\Tests;

use citybike\task\Services\DistanceCalculator;
use PHPUnit\Framework\TestCase;

class DistanceCalculatorTest extends TestCase
{
    public function testCalculateDistance()
    {
        $distance = DistanceCalculator::calculateDistance(
            lat1: 45.69233,
            lon1: 9.65931,
            lat2: 45.69654,
            lon2: 9.65897
        );
        $expectedDistance = 0.46887485811627466;

        self::assertEqualsWithDelta($expectedDistance, $distance, 0.001, ''); // Adjust delta as needed
    }
}