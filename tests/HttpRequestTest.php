<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Remember, requires are from the root in tests
require_once 'src/ShinePHP/HttpRequest.php';
use ShinePHP\{HttpRequest};

final class HttpRequestTest extends TestCase {

	public function testValidHttpRequests() : void {

		// GET request
		$EasyHttpGetReq = new HttpRequest('https://jsonplaceholder.typicode.com/posts', ['Content-Type' => 'application/json']);
		$getRes = $EasyHttpGetReq->get();
		$jsonGetRes = json_decode($getRes, true);
		$this->assertArrayHasKey('id', $jsonGetRes);

		// POST request
		$EasyHttpPostReq = new HttpRequest('https://jsonplaceholder.typicode.com/posts', ['Content-Type' => 'application/json']);
		$postRes = $EasyHttpPostReq->post(json_encode(array('title' => 'lorem ipsum', 'userId' => 1000, 'body' => 'setet dolor')), ['name' => 'adam']);
		$jsonPostRes = json_decode($postRes, true);
		$this->assertArrayHasKey('id', $jsonPostRes);

	}

	// Testing valid JSON input from url (will usually be from php://input though)
	public function testingValidJsonInputFromUrl() : void {
		$jsonRetrieved = HttpRequest::get_json('https://jsonplaceholder.typicode.com/posts');
		$this->assertArrayHasKey(50, $jsonRetrieved);
	}
}
