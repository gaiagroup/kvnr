<?php

use PHPUnit\Framework\TestCase;
use GaiaGroup\KvnrValidator;

/**
 * @author    Davide Danna
 * @copyright 2020 GAIA AG, Hamburg
 * @package   KVNR-Validator
 *
 * Created using PhpStorm at 08.09.20 11:32
 */

class KvnrValidatorTest extends TestCase
{
    /**
     * @dataProvider getKvnrCodesProvider
     * @param string $code KVNR code to be checked
     * @param bool $expected expected result
     */
    public function testKvnrCodeIsValid(string $code, bool $expected)
    {
        $result = KvnrValidator::validate($code);
        $this->assertSame($expected, $result);
    }

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