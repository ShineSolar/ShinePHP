<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Remember, requires are from the root in tests
require 'src/ShinePHP/HandleData.php';
use ShinePHP\{HandleData, IpValidator, PrimitiveDataValidator};

final class HandleDataTest extends TestCase {

    public function testingValidData() : void {

        /** 
         * Phone Testing
         */

        // Testing valid phone number with no country code
        $noCountryCodePhone = HandleData::american_phone('4086935992');
        $this->assertEquals('4086935992', $noCountryCodePhone);

        // Testing valid phone number with no country code
        $countryCodePhone = HandleData::american_phone('14086935992');
        $this->assertEquals('4086935992', $countryCodePhone);

        // Testing valid phone number with extra characters sent through
        $countryCodePhone = HandleData::american_phone('+1 (408) 693-5992', true);
        $this->assertEquals('14086935992', $countryCodePhone);

        // Testing valid phone number with a desired appended country code
        $noCountryCodePhone = HandleData::american_phone('4086935992', true);
        $this->assertEquals('14086935992', $noCountryCodePhone);

    }

    // Testing invalid phone number
    public function testInvalidPhoneNumber() : void { $this->assertFalse(HandleData::american_phone('not a valid phone number')); }

    // Testing invalid phone number 10 digits, but starts with one
    public function testInvalidPhoneNumberStartsWithOneOnlyTenDigits() : void { $this->assertFalse(HandleData::american_phone('1408693599')); }

    // Testing invalid phone number 11 digits, but does not start with one
    public function testInvalidPhoneNumberStartsNoOneElevenDigits() : void { $this->assertFalse(HandleData::american_phone('40869359921')); }

    // Testing invalid phone number empty string
    public function testInvalidPhoneNumberEmptyString() : void { $this->assertFalse(HandleData::american_phone('')); }

}
