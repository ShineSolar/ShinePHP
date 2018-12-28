<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Remember, requires are from the root in tests
require 'src/ShinePHP/EasyHttp.php';
use ShinePHP\{EasyHttp, EasyHttpException};

final class HandleDataTest extends TestCase {

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