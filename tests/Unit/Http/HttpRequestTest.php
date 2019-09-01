<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Remember, requires are from the root in tests
require_once 'src/ShinePHP/Http/HttpRequest.php';
use ShinePHP\Http\{HttpRequest, HttpRequestException};

final class HttpRequestUnitTest extends TestCase {

	public function test_regular_url(): void {
		$this->assertEquals('https://google.com/', HttpRequest::build_url('https://google.com/', array()));
		$this->assertEquals('https://google.com/?q=search+query', HttpRequest::build_url('https://google.com/?q=search+query', array()));
		$this->assertEquals('https://google.com/?q=search+query&adam=mcgurk', HttpRequest::build_url('https://google.com/?q=search+query', array('adam' => 'mcgurk')));
		$this->assertEquals('https://google.com/?q=searchquery', HttpRequest::build_url('https://google.com/', array('q' => 'searchquery')));
	}

	public function test_invalid_method(): void {
		$this->expectException(HttpRequestException::class);
		HttpRequest::verify_method('HEAD');
	}

	public function test_valid_methods(): void {
		$this->assertEquals('PUT', HttpRequest::verify_method('put'));
		$this->assertEquals('GET', HttpRequest::verify_method('get'));
		$this->assertEquals('POST', HttpRequest::verify_method('post'));
		$this->assertEquals('DELETE', HttpRequest::verify_method('delete'));
	}

}
