<?php
declare(strict_types=1);

namespace ShinePHP\Http;

final class IncomingRequest {

	public function validate_https(): bool {
		return ($_SERVER['REQUEST_SCHEME'] === 'https' || \strpos($_SERVER['HTTP_HOST'], 'localhost') !== false ? true : false);
	}

	public function validate_content_type(string $type): bool {
		return ($_SERVER['CONTENT_TYPE'] === $type ? true : false);
	}

	public function validate_request_method(string $method): bool {
		return ($_SERVER['REQUEST_METHOD'] === $method ? true : false);
	}

	public function get_custom_header_value(string $header_value): ?string {

		// replacing dashes with underscores
		$safe_value = \strtoupper(\str_replace('-', '_', $header_value));

		// returning the value or null
		return (isset($_SERVER['HTTP_'.$safe_value]) ? $_SERVER['HTTP_'.$safe_value] : null);

	}

	public function retrieve_json_input(string $retrieve_url = 'php://input'): array {

		// decode JSON array
		$decoded_json = \json_decode(file_get_contents($retrieve_url), true);

		// return an empty array if there was no json to return, otherwise return the decoded json
		return (\is_null($decoded_json) ? array() : $decoded_json);

	}

	public function require_input_data(?array $input_data, array $field_names_to_validate = array()): array {

		// making sure the data isn't null or empty
		if (empty($input_data) || \is_null($input_data)) {
			throw new IncomingRequestException('Input cannot be empty');
		}

		// if you pass an array to $field_names_to_validate, this will make sure that it won't be omitted
		\array_map(function($field) use ($input_data) {
			if (\array_key_exists($field, $input_data) === false) {
				throw new IncomingRequestException($field.' cannot be omitted');
			}
		}, $field_names_to_validate);

		// returning the array
		return $input_data;

	}

}

final class IncomingRequestException extends \Exception {}
