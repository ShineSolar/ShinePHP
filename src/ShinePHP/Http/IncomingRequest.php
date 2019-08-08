<?php
declare(strict_types=1);

namespace ShinePHP\Http;

final class IncomingRequest {

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

	public function require_input_data(?array $input_data, array $field_names_to_validate): array {

		if (empty($input_data) || is_null($input_data)) {
			throw new \Exception('Input cannot be empty');
		}

		foreach ($required_input_names as $name) {

			if (array_key_exists($name, $input_data) === false) {
				throw new \Exception($name.' cannot be omitted');
			}
			
		}

		return $input_data;

	}

}
