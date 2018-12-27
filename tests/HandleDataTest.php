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
