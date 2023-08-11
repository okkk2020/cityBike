<?php

namespace citybike\task;

use citybike\task\Parsers\CSVParser;
use citybike\task\Parsers\ParserInterface;
use citybike\task\Parsers\XMLParser;
use InvalidArgumentException;

class ParserFactory
{
    public static function createParser($filename): ParserInterface
    {
        $fileFormat = pathinfo($filename, PATHINFO_EXTENSION);
        return match ($fileFormat) {
            'csv' => new CSVParser(),
            'xml' => new XMLParser(),
            default => throw new InvalidArgumentException("Unsupported parser format: $fileFormat"),
        };
    }
}