<?php
declare(strict_types=1);

namespace ShinePHP\Http;

final class IncomingRequest {

	/** 
	 *  @access private
	 *	@var array These are the headers you want set for your request
	 */
	private $headers;

	public function __construct(array $headers = []) {

		// looping through and setting each header individually
		foreach($headers as $key => $value) {
			\header($key.': '.$value);
		}

	}

	public function validate_https(): bool {
		return ($_SERVER['REQUEST_SCHEME'] === 'https' || $_SERVER['HTTP_HOST'] === 'localhost' ? true : false);
	}

	public function validate_content_type(string $type): bool {
		return ($_SERVER['CONTENT_TYPE'] === $type ? true : false);
	}

	public function validate_request_method(string $method): bool {
		return ($_SERVER['REQUEST_METHOD'] === $method ? true : false);
	}

	public function get_custom_header_value(string $header_value): ?string {

		// replacing dashes with underscores
		$safe_value = str_replace('-', '_', $header_value);

		// returning the value or null
		return (isset($_SERVER['HTTP_'.$safe_value]) ? $_SERVER['HTTP_'.$safe_value] : null);

	}

	public function retrieve_json_input(string $retrieve_url = 'php://input'): array {

		// decode JSON array
		$decoded_json = json_decode(file_get_contents($retrieve_url), true);

		// return an empty array if there was no json to return, otherwise return the decoded json
		return (is_null($decoded_json) ? array() : $decoded_json);

	}

}
