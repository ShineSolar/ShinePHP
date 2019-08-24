<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Remember, requires are from the root in tests
require_once 'src/ShinePHP/Http/HttpRequest.php';
use ShinePHP\Http\{HttpRequest};

final class HttpRequestTest extends TestCase {

	public function test_fail_to_create_instance(): void {
		$this->expectException(Exception::class);
		new HttpRequest('Not a method', 'https://google.com/');
	}

	public function test_can_create_instance(): void {

		$this->assertInstanceOf(
			HttpRequest::class,
			new HttpRequest('https://alsjdflaksj', 'POST')
		);

	}

	public function test_regular_url(): void {
		$this->assertEquals('https://google.com/', HttpRequest::build_url('https://google.com/', array()));
		$this->assertEquals('https://google.com/?q=search+query', HttpRequest::build_url('https://google.com/?q=search+query', array()));
		$this->assertEquals('https://google.com/?q=search+query&adam=mcgurk', HttpRequest::build_url('https://google.com/?q=search+query', array('adam' => 'mcgurk')));
		$this->assertEquals('https://google.com/?q=searchquery', HttpRequest::build_url('https://google.com/', array('q' => 'searchquery')));
	}

	public function test_get_request(): void {
		$req = new HttpRequest('https://postman-echo.com/get?foo=bar');
		$req->set_request_headers(array('Content-Type: application/json'));
		$res = $req->send();
		$decoded_res = json_decode($res, true);
		$this->assertArrayHasKey('args', $decoded_res);
	}

}
