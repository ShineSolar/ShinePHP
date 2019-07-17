<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Remember, requires are from the root in tests
require 'src/ShinePHP/HandleData.php';
require 'src/ShinePHP/Data/EmailValidator.php';
use ShinePHP\{HandleData, IpValidator, PrimitiveDataValidator};
use ShinePHP\Data\EmailValidator;

final class HandleDataTest extends TestCase {

    public function testInvalidPrimitiveTypes(): void {
        $EmptyStringValidator = new PrimitiveDataValidator('');
        $this->assertFalse($EmptyStringValidator->validate_string());
    }

    public function testInvalidEmailAddresses(): void {
        $EmailValidator = new EmailValidator('not an email');
        $this->assertNull($EmailValidator->validate_email());
        $DomainValidator = new EmailValidator('amcgurk@shinesolar.com');
        $this->assertNull($DomainValidator->validate_email_domain('gmail.com'));
        $InvalidEmailDomainValidator = new EmailValidator('nope');
        $this->assertNull($InvalidEmailDomainValidator->validate_email_domain('gmail.com'));
    }

    public function testValidEmailAddresses(): void {
        $EmailValidator = new EmailValidator('amcgurk@shinesolar.com');
        $this->assertEquals($EmailValidator->validate_email(), 'amcgurk@shinesolar.com');
        $DomainValidator = new EmailValidator('amcgurk@shinesolar.com');
        $this->assertEquals($DomainValidator->validate_email_domain('shinesolar.com'), 'amcgurk@shinesolar.com');
    }

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

    	/** 
    	 * String Testing
    	 */

    	// Testing regular old string
    	$plainString = HandleData::string('this_is_a_string');
    	$this->assertEquals('this_is_a_string', $plainString);

    	// Testing empty string
    	$emptyStringAllowed = HandleData::string('');
    	$this->assertEquals('', $emptyStringAllowed);

    	// Testing integer converted to string
    	$numbersConverted = HandleData::string(123456765456765);
    	$this->assertEquals('123456765456765', $numbersConverted);

    	/** 
    	 * URL Testing
    	 */

    	// Testing regular URL
    	$url = HandleData::url('https://shinesolar.com');
    	$this->assertEquals('https://shinesolar.com', $url);

    	/** 
    	 * Float Testing
    	 */

    	// Parsing string as float
    	$float = HandleData::float('1.292716');
    	$this->assertEquals(1.292716, $float);

    	// Parsing float that is less than one
    	$lessThanOneFloat = HandleData::float('0.292716');
    	$this->assertEquals(0.292716, $lessThanOneFloat);

    	// Parsing a negative float
    	$negativeFloat = HandleData::float('-1.292716');
    	$this->assertEquals(-1.292716, $negativeFloat);

    	// Parsing a whole number as a float
    	$wholeNumber = HandleData::float('1');
    	$this->assertEquals(1.00, $wholeNumber);

    	// Parsing 0 as a float where 0 strictness is not enforced
    	$zero = HandleData::float('0', true);
    	$this->assertEquals(0, $zero);

    	/** 
    	 * Integer Testing
    	 */

    	// Testing string passed as integer
    	$integer = HandleData::integer('2');
    	$this->assertEquals(2, $integer);

    	// Testing negative integer
    	$negativeInteger = HandleData::integer(-2);
    	$this->assertEquals(-2, $negativeInteger);

    	// Testing 0 with no 0 strictness
    	$zero = HandleData::integer(0, true);
    	$this->assertEquals(0, $zero);

    }

    // Testing invalid phone number
    public function testInvalidPhoneNumber() : void { $this->assertFalse(HandleData::american_phone('not a valid phone number')); }

    // Testing invalid phone number 10 digits, but starts with one
    public function testInvalidPhoneNumberStartsWithOneOnlyTenDigits() : void { $this->assertFalse(HandleData::american_phone('1408693599')); }

    // Testing invalid phone number 11 digits, but does not start with one
    public function testInvalidPhoneNumberStartsNoOneElevenDigits() : void { $this->assertFalse(HandleData::american_phone('40869359921')); }

    // Testing invalid phone number empty string
    public function testInvalidPhoneNumberEmptyString() : void { $this->assertFalse(HandleData::american_phone('')); }    

    // Testing empty string with emptiness enforced
    public function testInvalidString() : void { $this->assertFalse(HandleData::string('', false));}

    // Testing invalid url
    public function testInvalidUrl() : void { $this->assertFalse(HandleData::url('shinesolar.com')); }

    // Testing empty url
    public function testEmptyInvalidUrl() : void { $this->assertFalse(HandleData::url('')); }

    // Testing invalid float passing 0
    public function testInvalidFloatPassingZero() : void { $this->assertFalse(HandleData::float(0)); }

    // Testing invalid float passing a string
    public function testInvalidFloatPassingString() : void { $this->assertFalse(HandleData::float('not a valid float')); }

    // Testing invalid integer passing 0
    public function testInvalidIntegerPassingZero() : void { $this->assertFalse(HandleData::integer(0)); }

    // Testing invalid integer passing a string
    public function testInvalidIntegerPassingString() : void { $this->assertFalse(HandleData::integer('not a valid integer')); }

}
