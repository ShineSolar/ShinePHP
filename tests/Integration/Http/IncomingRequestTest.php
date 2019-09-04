<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Remember, requires are from the root in tests
require_once 'src/ShinePHP/Http/IncomingRequest.php';
use ShinePHP\Http\{IncomingRequest, IncomingRequestException};

final class IncomingRequestIntegrationTest extends TestCase {

	public function testValidCreate() : void {

		$Incoming = new IncomingRequest();

		$this->assertInstanceOf(
			IncomingRequest::class,
			$Incoming
		);

	}

	// Testing valid JSON input from url (will usually be from php://input though)
	public function testingValidJsonInputFromUrl() : void {

		$Incoming = new IncomingRequest();

		$jsonRetrieved = $Incoming::retrieve_json_input('https://jsonplaceholder.typicode.com/posts');
		$this->assertArrayHasKey(50, $jsonRetrieved);
	}

}
