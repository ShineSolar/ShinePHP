<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Remember, requires are from the root in tests
require_once 'src/ShinePHP/Http/HttpRequest.php';
use ShinePHP\Http\{HttpRequest, HttpRequestException};

final class HttpRequestIntegrationTest extends TestCase {

	public function test_fail_to_create_instance(): void {
		$this->expectException(HttpRequestException::class);
		new HttpRequest('https://google.com/', 'HEAD');
	}

	public function test_can_create_instance(): void {

		$this->assertInstanceOf(
			HttpRequest::class,
			new HttpRequest('https://alsjdflaksj', 'POST')
		);

	}

	public function test_get_request(): void {
		$req = new HttpRequest('https://postman-echo.com/get?foo=bar');
		$req->set_request_headers(array('Content-Type: application/json'));
		$res = $req->send();
		$decoded_res = json_decode($res, true);
		$this->assertArrayHasKey('args', $decoded_res);
	}

}
