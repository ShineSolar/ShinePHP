<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Remember, requires are from the root in tests
require 'src/ShinePHP/HandleData.php';
use ShinePHP\{HandleData, HandleDataException};

final class HandleDataTest extends TestCase {

    public function testingValidData() : void {

    	/** 
    	 * Email Testing
    	 */

    	// Testing regular valid email
    	$plainEmail = HandleData::email('eldermcgurk@gmail.com');
    	$this->assertEquals('eldermcgurk@gmail.com', $plainEmail);

    	// Testing domain restricted valid email
    	$domainToValidateEmail = HandleData::email('amcgurk@shinesolar.com', 'shinesolar.com');
    	$this->assertEquals('amcgurk@shinesolar.com', $domainToValidateEmail);

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
    	 * IP Address Testing
    	 */

    	// Testing public IP address	
    	$publicIp = HandleData::ip('157.201.87.254');
    	$this->assertEquals('157.201.87.254', $publicIp);

    	// Testing private IP address
    	$privateRange = HandleData::ip('192.168.10.110');
    	$this->assertEquals('192.168.10.110', $privateRange);

    	// Testing IPV6
    	$ipV6 = HandleData::ip('::1');
    	$this->assertEquals('::1', $ipV6);

    	// Testing subnet masks
    	$subnetMask = HandleData::ip('255.255.255.0');
    	$this->assertEquals('255.255.255.0', $subnetMask);

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

    // Testing invalid emails
    public function testInvalidEmail() : void { $this->assertFalse(HandleData::email('not a valid email')); }

    // Testing invalid domain to validate against an address
    public function testInvalidDomainOnEmail() : void { $this->assertFalse(HandleData::email('amcgurk@shinesolar.com', 'gmail.com')); }

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

    // Testing invalid ip address (regular 4 octect numerals)
    public function testInvalidIpAddress() : void { $this->assertFalse(HandleData::ip('256.101.7.10')); }

/*

    // Testing invalid float passing 0
    public function testInvalidFloatPassingZero() : void { $this->assertFalse(HandleDataException::class); HandleData::float(0); }

    // Testing invalid float passing a string
    public function testInvalidFloatPassingString() : void { $this->assertFalse(HandleDataException::class); HandleData::float('not a valid float'); }

    // Testing invalid integer passing 0
    public function testInvalidIntegerPassingZero() : void { $this->assertFalse(HandleDataException::class); HandleData::integer(0); }

    // Testing invalid integer passing a string
    public function testInvalidIntegerPassingString() : void { $this->assertFalse(HandleDataException::class); HandleData::integer('not a valid integer'); }*/

}
