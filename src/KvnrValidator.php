<?php

/**
 * @author    Davide Danna
 * @copyright 2020 GAIA AG, Hamburg
 * @package   KVNR-Validator
 *
 * Created using PhpStorm at 07.09.20 10:36
 */

namespace GaiaGroup;

/**
 * Class for KVNR code validation
 */
class KvnrValidator
{
    /**
     * Checks if given KVNR code is correct by validating its format and by verifying
     * its checksum (ref: https://de.wikipedia.org/wiki/Krankenversichertennummer)
     *
     * @param string $kvnr KVNR string to be validated
     *
     * @return bool true / false
     */
    public static function validate(string $kvnr): bool
    {
        $isValid = false;

        // checks KVNR format using regex
        if (preg_match("/^[A-Z][0-9]{9}$/", $kvnr)) {
            // computes CRC digit using KVNR string trimmed out of the last digit
            $kvnrCrc = self::computesKvnrCrcDigit(substr($kvnr, 0, strlen($kvnr) - 1));

            // compares computed CRC values with last digit in provided KVNR
            $isValid = $kvnr[9] == $kvnrCrc;
        }

        return $isValid;
    }

    /**
     * computes the KVNR CRC digit of a given string, if the format of provided string is not valid
     * the function returns -1
     *
     * @param string $kvnr KVNR string composed by capital letter followed by 8 digits
     *
     * @return int CRC digit, if code is invalid returns -1
     */
    private static function computesKvnrCrcDigit(string $kvnr)
    {
        $crc = -1;

        // converts first character of KVNR to integer using ASCII
        $digitChar = ord($kvnr[0]) - 64;

        // checks if conversion gave expected values (A->1 ... Z->26)
        if ($digitChar <= 26 && $digitChar >= 1) {
            $kvnrDigits = [];

            // adds 0 left-padding for values less than 10
            $digitChar = str_pad(strval($digitChar), 2, "0", STR_PAD_LEFT);

            // sets first 2 element of digits array
            $kvnrDigits[] = intval($digitChar[0]);
            $kvnrDigits[] = intval($digitChar[1]);

            // sets last 8 element of digits array
            for ($i = 2; $i < 10; $i++) {
                $kvnrDigits[$i] = intval($kvnr[$i - 1]);
            }

            $kvnrDigitsWeighted = [];
            for ($i = 0; $i < 10; $i++) {
                // sets weight array: (1, 2, 1, 2, ...)
                $weight = ($i % 2 == 0) ? 1 : 2;

                // multiplies digit array with weight array
                $kvnrDigitWeighted = $kvnrDigits[$i] * $weight;

                // if resulting number is >= 10 then it's digit are summed
                if ($kvnrDigitWeighted < 10) {
                    $kvnrDigitsWeighted[$i] = $kvnrDigitWeighted;
                } else {
                    $kvnrDigitsWeighted[$i] = array_sum(str_split($kvnrDigitWeighted));
                }
            }

            // computes CRC by summing each item in array and applying module-10
            $crc = array_sum($kvnrDigitsWeighted) % 10;
        }

        return $crc;
    }
}