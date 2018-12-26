<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Remember, requires are from the root in tests
require 'src/ShinePHP/HandleData.php';
use ShinePHP\{HandleData, HandleDataException};

final class HandleDataTest extends TestCase {

	public function testingValidJsonInputFromUrl() : void {
		$jsonRetrieved = HandleData::turnJsonInputIntoArray('http://127.0.0.1/');
		$this->assertArrayHasKey('person_1', $jsonRetrieved);
	}

	public function testingInvalidJsonInputFromUrl() : void {
		$this->expectException(HandleDataException::class);
		HandleData::turnJsonInputIntoArray();
	}

}
