<?php

namespace citybike\task\Parsers;

interface ParserInterface
{
    public function parseBikersData($filePath): array;
}