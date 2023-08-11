<?php

namespace citybike\task\Services;

use Exception;

class CityBikeApi
{
    public function fetchStationsData($city): array
    {
        $base_url = "http://api.citybik.es";
        $networks_url = $base_url . '/v2/networks';

        $networks_response = file_get_contents($networks_url);
        $networks_data = $this->parse_response($networks_response);

        $station_info = [];

        foreach ($networks_data["networks"] as $network) {
            if ($network["location"]["city"] == $city) {
                $network_url = $base_url . $network["href"];
                $network_response = file_get_contents($network_url);
                $network_info = $this->parse_response($network_response);

                foreach ($network_info["network"]["stations"] as $station) {
                    $station_info[] = [
                        "name" => $station["name"],
                        "latitude" => $station["latitude"],
                        "longitude" => $station["longitude"],
                        "free_bikes" => $station["free_bikes"]
                    ];
                }
            }
        }

        return $station_info;
    }

    private function parse_response($response)
    {
        mb_internal_encoding("UTF-8");
        try {
            $i = 0;
            $result = $this->response_decode_value($response, $i);
            $n = strlen($response);
            while ($i < $n && ctype_space($response[$i])) {
                $i++;
            }
            if ($result !== null && $i >= $n) {
                return $result;
            }
        } catch (Exception $e) {
            // Handle the exception if needed
        }
        return null;
    }

    /**
     * @throws Exception
     */
    private function response_decode_value($decode_value, &$i)
    {
        $n = strlen($decode_value);
        while ($i < $n && ctype_space($decode_value[$i])) {
            $i++;
        }

        $char = $decode_value[$i] ?? '';

        switch ($char) {
            case '{':
                return $this->parseObject($decode_value, $i);
            case '[':
                return $this->parseArray($decode_value, $i);
            case '"':
                return $this->response_decode_string($decode_value, $i);
            case '-':
                return $this->response_decode_number($decode_value, $i);
            case 't':
                return $this->parseKeyword($decode_value, $i, 'true', true);
            case 'f':
                return $this->parseKeyword($decode_value, $i, 'false', false);
            case 'n':
                return $this->parseKeyword($decode_value, $i, 'null', null);
            default:
                if (ctype_digit($char)) {
                    return $this->response_decode_number($decode_value, $i);
                } else {
                    throw new Exception("Syntax error");
                }
        }
    }

    /**
     * @throws Exception
     */
    private function parseObject($decode_value, &$i): array
    {
        $i++;
        $result = [];
        $n = strlen($decode_value);

        while ($i < $n) {
            $key = $this->response_decode_string($decode_value, $i);
            while ($i < $n && ctype_space($decode_value[$i])) {
                $i++;
            }

            if ($decode_value[$i++] !== ':') {
                throw new Exception("Expected ':' on " . ($i - 1));
            }

            $value = $this->response_decode_value($decode_value, $i);
            $result[$key] = $value;

            while ($i < $n && ctype_space($decode_value[$i])) {
                $i++;
            }

            if ($decode_value[$i] === '}') {
                $i++;
                return $result;
            }

            if ($decode_value[$i++] !== ',') {
                throw new Exception("Expected ',' on " . ($i - 1));
            }

            while ($i < $n && ctype_space($decode_value[$i])) {
                $i++;
            }
        }

        throw new Exception("Syntax error");
    }

    /**
     * @throws Exception
     */
    private function response_decode_string($string, &$i): string
    {
        $result = '';
        $escape = [
            '"' => '"',
            '\\' => '\\',
            '/' => '/',
            'b' => "\b",
            'f' => "\f",
            'n' => "\n",
            'r' => "\r",
            't' => "\t"
        ];
        $n = strlen($string);

        if ($string[$i] === '"') {
            while (++$i < $n) {
                $char = $string[$i];

                if ($char === '"') {
                    $i++;
                    return $result;
                }

                if ($char === '\\') {
                    $i++;
                    $nextChar = $string[$i] ?? null;

                    if ($nextChar === 'u') {
                        $unicodeHex = substr($string, $i + 1, 4);
                        $unicodeValue = hexdec($unicodeHex);
                        $result .= mb_chr($unicodeValue, 'UTF-8');
                        $i += 4;
                    } elseif (isset($escape[$nextChar])) {
                        $result .= $escape[$nextChar];
                        $i++;
                    } else {
                        throw new Exception("Invalid escape sequence at position $i");
                    }
                } else {
                    $result .= $char;
                }
            }
        }

        throw new Exception("Syntax error at position $i");
    }

    /**
     * @throws Exception
     */
    private function parseArray($decode_value, &$i): array
    {
        $i++;
        $result = [];
        $n = strlen($decode_value);

        while ($i < $n) {
            $value = $this->response_decode_value($decode_value, $i);
            $result[] = $value;

            while ($i < $n && ctype_space($decode_value[$i])) {
                $i++;
            }

            if ($decode_value[$i] === ']') {
                $i++;
                return $result;
            }

            if ($decode_value[$i++] !== ',') {
                throw new Exception("Expected ',' on " . ($i - 1));
            }

            while ($i < $n && ctype_space($decode_value[$i])) {
                $i++;
            }
        }

        throw new Exception("Syntax error");
    }

    /**
     * @throws Exception
     */
    private function response_decode_number($number, &$i): float
    {
        $result = '';
        $n = strlen($number);

        // Handle sign
        if ($number[$i] === '-') {
            $result .= $number[$i++];
        }

        // Handle integral part
        while ($i < $n && ctype_digit($number[$i])) {
            $result .= $number[$i++];
        }

        // Handle decimal part
        if ($i < $n && $number[$i] === '.') {
            $result .= $number[$i++];
            while ($i < $n && ctype_digit($number[$i])) {
                $result .= $number[$i++];
            }
        }

        // Handle exponent part
        if ($i < $n && ($number[$i] === 'e' || $number[$i] === 'E')) {
            $result .= $number[$i++];
            if ($i < $n && ($number[$i] === '-' || $number[$i] === '+')) {
                $result .= $number[$i++];
            }
            while ($i < $n && ctype_digit($number[$i])) {
                $result .= $number[$i++];
            }
        }
        if ($result === '') {
            throw new Exception("Invalid number format at position $i");
        }
        return (float)$result;
    }

    /**
     * @throws Exception
     */
    private function parseKeyword($decode_value, &$i, $keyword, $value)
    {
        $length = strlen($keyword);
        if (substr($decode_value, $i, $length) === $keyword) {
            $i += $length;
            return $value;
        }
        throw new Exception("Syntax error");
    }
}