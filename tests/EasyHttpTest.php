<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Remember, requires are from the root in tests
require_once 'src/ShinePHP/EasyHttp.php';
use ShinePHP\{EasyHttp, EasyHttpException};

final class HandleDataTest extends TestCase {

	public function testValidHttpRequests() : void {

		// GET request
		$EasyHttpGetReq = new EasyHttp('https://jsonplaceholder.typicode.com/posts', ['Content-Type' => 'application/json']);
		$getRes = $EasyHttpGetReq->makeGetRequest();
		$jsonGetRes = json_decode($getRes, true);
		$this->assertArrayHasKey('userId', $jsonGetRes[0]);

		// POST request
		$EasyHttpPostReq = new EasyHttp('https://jsonplaceholder.typicode.com/posts', ['Content-Type' => 'application/json']);
		$postRes = $EasyHttpPostReq->makePostRequest(json_encode(array('title' => 'lorem ipsum', 'userId' => 1000, 'body' => 'setet dolor')));
		$jsonPostRes = json_decode($postRes, true);
		$this->assertArrayHasKey('id', $jsonPostRes);

	}

	// Testing valid JSON input from url (will usually be from php://input though)
	public function testingValidJsonInputFromUrl() : void {
		$jsonRetrieved = EasyHttp::turnJsonInputIntoArray('https://jsonplaceholder.typicode.com/posts');
		$this->assertArrayHasKey(50, $jsonRetrieved);
	}

	// Testing non existent JSON input
	public function testingInvalidJsonInputFromUrl() : void {
		$this->expectException(EasyHttpException::class);
		EasyHttp::turnJsonInputIntoArray();
	}
}
