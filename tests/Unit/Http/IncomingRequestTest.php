<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Remember, requires are from the root in tests
require_once 'src/ShinePHP/Http/IncomingRequest.php';
use ShinePHP\Http\{IncomingRequest, IncomingRequestException};

final class IncomingRequestUnitTest extends TestCase {

	public function test_cannot_pass_null(): void {
		$this->expectException(IncomingRequestException::class);
		IncomingRequest::require_input_data(null);
	}

	public function test_cannot_pass_empty_array(): void {
		$this->expectException(IncomingRequestException::class);
		IncomingRequest::require_input_data(array());
	}

	public function test_name_does_not_exist(): void {
		$this->expectException(IncomingRequestException::class);
		IncomingRequest::require_input_data(array('foo' => 'bar'), array('bash'));
	}

	public function test_requires(): void {
		$input_data = IncomingRequest::require_input_data(array('foo' => 'bar'), array('foo'));
		$this->assertEquals('bar', $input_data['foo']);
	}

}
