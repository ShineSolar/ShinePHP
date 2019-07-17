<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Remember, requires are from the root in tests
require 'src/ShinePHP/HandleData.php';
use ShinePHP\{HandleData, IpValidator, PrimitiveDataValidator};

final class HandleDataTest extends TestCase {

    public function testValidIpAddresses(): void {

        $reg_valid = new IpValidator('192.168.0.1');
        $this->assertEquals('192.168.0.1', $reg_valid->validate_general_ip());

        $priv_addy = new IpValidator('FC80:0000:0000:0000:903A:1C1A:E802:11E4');

        $this->assertEquals('FC80:0000:0000:0000:903A:1C1A:E802:11E4', $priv_addy->validate_private_ipv6());

        $valid_subnet = new IpValidator('255.255.255.0');
        $this->assertEquals('255.255.255.0', $valid_subnet->validate_subnet_mask());

    }

    public function testInvalidIpAddresses(): void {
        $reg_invalid = new IpValidator('256.71.83.1');
        $this->assertFalse($reg_invalid->validate_general_ip());

        $invalid_ipv4 = new IpValidator('FC80:0000:0000:0000:903A:1C1A:E802:11E4');
        $this->assertFalse($invalid_ipv4->validate_general_ipv4());

        $invalid_public_ipv4_reserved = new IpValidator('127.0.0.1');
        $this->assertFalse($invalid_public_ipv4_reserved->validate_public_ipv4());

        $invalid_public_ipv4_private = new IpValidator('192.168.0.1');
        $this->assertFalse($invalid_public_ipv4_private->validate_public_ipv4());

        $invalid_private_ipv4_reserved = new IpValidator('127.0.0.1');
        $this->assertFalse($invalid_private_ipv4_reserved->validate_private_ipv4());

        $invalid_private_ipv4_public = new IpValidator('207.124.51.1');
        $this->assertFalse($invalid_private_ipv4_public->validate_private_ipv4());

        $invalid_subnet = new IpValidator('192.168.0.1');
        $this->assertFalse($invalid_subnet->validate_subnet_mask());
    }

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
