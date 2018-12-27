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

	// Testing valid validation from htmlspecialchars
	public function testingValidPrepareSingularForOutputValidation() : void {
		$mitigatedXSS = HandleData::prepareSingularForOutputValidation('<script>alert("xss");</script>');
		$this->assertEquals('&lt;script&gt;alert(&quot;xss&quot;);&lt;/script&gt;', $mitigatedXSS);
	}

	// Testing prepareSingularForOutputValidation() method with no arg
	public function testingInvalidSingularOutputValidation() : void {
		$this->expectException(ArgumentCountError::class);
		HandleData::prepareSingularForOutputValidation();
	}

}
