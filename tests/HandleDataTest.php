<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Remember, requires are from the root in tests
require 'src/ShinePHP/HandleData.php';
use ShinePHP\{HandleData, HandleDataException};

final class HandleDataTest extends TestCase {

	// Testing a valid class init
	public function testCanBeCreatedWithValidArray() : void {
        $this->assertInstanceOf(
            HandleData::class,
            new HandleData(HandleData::turnJsonInputIntoArray('http://127.0.0.1/'))
        );
    }

    // Testing an invalid class init with no parameters
    public function testCannotBeCreatedWithNoParametersPassed() : void {
    	$this->expectException(ArgumentCountError::class);
    	new HandleData();
    }

    // Testing an invalid class init with no parameters
    public function testCannotBeCreatedWithEmptyArray() : void {
    	$this->expectException(HandleDataException::class);
    	new HandleData([]);
    }

    // Testing valid emails
    public function testValidEmailData() : void {
    	$plainEmail = HandleData::email('eldermcgurk@gmail.com');
    	$this->assertEquals('eldermcgurk@gmail.com', $plainEmail);
    	$domainToValidateEmail = HandleData::email('amcgurk@shinesolar.com', 'shinesolar.com');
    	$this->assertEquals('amcgurk@shinesolar.com', $domainToValidateEmail);
    }

    // Testing invalid emails
    public function testInvalidEmail() : void {
    	$this->expectException(HandleDataException::class);
    	$plainEmail = HandleData::email('not a valid email');
    }

    // Testing invalid domain to validate against an address
    public function testInvalidDomainOnEmail() : void {
    	$this->expectException(HandleDataException::class);
    	$domainToValidateEmail = HandleData::email('amcgurk@shinesolar.com', 'gmail.com');
    }

    // Testing valid strings
    public function testValidStringData() : void {
    	$plainString = HandleData::string('this_is_a_string');
    	$this->assertEquals('this_is_a_string', $plainString);
    	$emptyStringAllowed = HandleData::string('');
    	$this->assertEquals('', $emptyStringAllowed);
    	$numbersConverted = HandleData::string(123456765456765);
    	$this->assertEquals('123456765456765', $numbersConverted);
    }

    // Testing invalid string
    public function testInvalidString() : void {
    	$this->expectException(HandleDataException::class);
    	HandleData::string('', false);
    }

    // Testing valid url
    public function testValidUrl() : void {
    	$url = HandleData::url('https://shinesolar.com');
    	$this->assertEquals('https://shinesolar.com', $url);
    }

    // Testing invalid url
    public function testInvalidUrl() : void {
    	$this->expectException(HandleDataException::class);
    	HandleData::url('shinesolar.com');
    }

    // Testing valid ip addresses
    public function testValidIpAddress() : void {
    	$publicIp = HandleData::ipAddress('157.201.87.254');
    	$this->assertEquals('157.201.87.254', $publicIp);
    	$privateRange = HandleData::ipAddress('192.168.10.110');
    	$this->assertEquals('192.168.10.110', $privateRange);
    	$ipV6 = HandleData::ipAddress('::1');
    	$this->assertEquals('::1', $ipV6);
    }

    // Testing invalid ip addresses
    public function testIpAddress() : void {
    	$this->expectException(HandleDataException::class);
    	HandleData::ipAddress('256.101.7.10');
    }

    // Testing valid float
    public function testValidFloat() : void {
    	$float = HandleData::float('1.292716');
    	$this->assertEquals(1.292716, $float);
    	$lessThanOneFloat = HandleData::float('0.292716');
    	$this->assertEquals(0.292716, $lessThanOneFloat);
    	$negativeFloat = HandleData::float('-1.292716');
    	$this->assertEquals(-1.292716, $negativeFloat);
    	$wholeNumber = HandleData::float('1');
    	$this->assertEquals(1.00, $wholeNumber);
    	$zero = HandleData::float('0', true);
    	$this->assertEquals(0, $zero);
    }

    // Testing invalid float passing 0
    public function testInvalidFloatPassingZero() : void {
    	$this->expectException(HandleDataException::class);
    	HandleData::float(0);
    }

    // Testing invalid float passing a string
    public function testInvalidFloatPassingString() : void {
    	$this->expectException(HandleDataException::class);
    	HandleData::float('not a valid float');
    }

    // Testing valid integer
    public function testValidInteger() : void {
    	$integer = HandleData::integer('2');
    	$this->assertEquals(2, $integer);
    	$floatToMakeInteger = HandleData::integer(-2);
    	$this->assertEquals(-2, $floatToMakeInteger);
    	$zero = HandleData::integer(0, true);
    	$this->assertEquals('0', $zero);
    }

    // Testing invalid integer passing 0
    public function testInvalidIntegerPassingZero() : void {
    	$this->expectException(HandleDataException::class);
    	HandleData::integer(0);
    }

    // Testing invalid integer passing a string
    public function testInvalidIntegerPassingString() : void {
    	$this->expectException(HandleDataException::class);
    	HandleData::integer('not a valid float');
    }

	// Testing valid JSON input from url
	public function testingValidJsonInputFromUrl() : void {
		$jsonRetrieved = HandleData::turnJsonInputIntoArray('http://127.0.0.1/');
		$this->assertArrayHasKey('person_1', $jsonRetrieved);
	}

	// Testing non existent JSON input
	public function testingInvalidJsonInputFromUrl() : void {
		$this->expectException(HandleDataException::class);
		HandleData::turnJsonInputIntoArray();
	}

}
