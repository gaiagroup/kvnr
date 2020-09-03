<?php

namespace Gaia\KvnrValidator;

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
    public static function validate( string $kvnr ) : bool {
        // checks KVNR format using regex
        if( ! preg_match( "/^[A-Z][0-9]{9}$/", $kvnr) )
            return false;

        // computes CRC digit using KVNR string trimmed out of the last digit
        $kvnrCrc = self::computesKvnrCrcDigit( substr($kvnr,0,strlen($kvnr)-1) );

        // compares computed CRC values with last digit in provided KVNR and returns result
        return $kvnr[9] == $kvnrCrc;
    }

    /**
     * computes the KVNR CRC digit of a given string using a module-10 checksum algorithm
     *
     * @param string $kvnr KVNR string composed by capital letter followed by 8 digits
     *
     * @return int return a positive integer which is the computed CRC value for the given code,
     * if the format of the parameter is not valid function returns -1
     */
    private static function computesKvnrCrcDigit(string $kvnr ) {
        $crc = -1;
        $kvnrDigits = [];

        // converts first character of KVNR to integer using ASCII
        $number = ord($kvnr[0]) - 64;

        // checks if conversion gave expected values (A->1 ... Z->26)
        if( $number <= 26 && $number >= 1 ) {
            // adds 0 left-padding for values less than 10
            $digits = str_pad( strval($number), 2, "0", STR_PAD_LEFT);

            // puts padded digits inside digits array
            $kvnrDigits[] = intval( $digits[0] );
            $kvnrDigits[] = intval( $digits[1] );
        }

        // populates last 8 element of CRC array from digits [1-8] in KVNR
        $digits = 10;
        if( !empty( $kvnrDigits ) ) {
            for( $i=2; $i<$digits; $i++ ) {
                $kvnrDigits[$i] = intval( $kvnr[$i-1] );
            }
        }

        if( count($kvnrDigits) == 10 ) {
            $kvnrDigitsWeighted = [];

            // multiplies KVNR-DIGITS * WEIGHTS
            for( $i=0; $i<$digits; $i++ ) {
                // calculating weight value
                $weight = ($i % 2 == 0) ? 1 : 2;

                // multiplies each digit for thw weight
                $kvnrDigitWeighted = $kvnrDigits[$i] * $weight;

                // if resulting number is >= 10 then it's digit are summed
                if( $kvnrDigitWeighted < 10 )
                    $kvnrDigitsWeighted[$i] = $kvnrDigitWeighted;
                else
                    $kvnrDigitsWeighted[$i] = array_sum(str_split($kvnrDigitWeighted));
            }

            // sums each item in resulting array and applies module-10 to get the checksum
            $crc = array_sum( $kvnrDigitsWeighted ) % 10;
        }

        return $crc;
    }
}