<?php

/**
 * @author    Davide Danna
 * @copyright 2020 GAIA AG, Hamburg
 * @package   KVNR-Validator
 *
 * Created using PhpStorm at 08.09.20 11:32
 */

use GaiaGroup\KVNR;
use PHPUnit\Framework\TestCase;

class KVNRTest extends TestCase
{
    /**
     * Tests KVN validation method with a set of known KVNR codes
     *
     * @dataProvider getKvnrCodesProvider
     * @param string $code KVNR code to be checked
     * @param bool $expected expected result
     */
    public function testKvnrValidation(string $code, bool $expected)
    {
        $this->assertSame(
            $expected,
            KVNR::validate($code)
        );
    }

    /**
     * Returns a set of known KVNR codes with expected result
     */
    public function getKvnrCodesProvider()
    {
        return [
            ["", false],
            [" ", false],
            ["0000000000", false],
            ["X000000001", false],
            ["X000000010", false],
            ["X000000000", true],
            ["X000000012", true],
        ];
    }
}