<?php
declare(strict_types=1);

namespace ShinePHP\Data;

final class TypeValidator {

	private $primitive_data;

	public function __construct($primitive_data) {
		$this->primitive_data = $primitive_data;
	}

	public function validate_string(bool $can_be_empty = false): ?string {

		// sanitizing the actual string
		$sanitized_string = filter_var($this->primitive_data, FILTER_SANITIZE_STRING);

		// running the check
		return ($can_be_empty === false && $sanitized_string === '' ? null : $sanitized_string);

	}

	public function validate_int(bool $can_be_zero = false): ?int {

		// sanitizing and validating the input as an integer
		$sanitized_number = filter_var($this->primitive_data, FILTER_SANITIZE_NUMBER_INT);
		$validated_int = filter_var($sanitized_number, FILTER_VALIDATE_INT);

		// Doing the integer checks and throwing exceptions or returning the valid integer
		if ($validated_int === false) {
			return null;
		} else if (!$can_be_zero && $validated_int === 0) {
			return null;
		} else {
			return $validated_int;
		}

	}

	public function validate_float(bool $can_be_zero = false): ?float {

		// sanitizing and validating the input as a float
		$sanitized_number = filter_var($this->primitive_data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		$validated_float = filter_var($sanitized_number, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);

		// Doing the float checks and throwing exceptions or returning the valid float
		if ($validated_float === false) {
			return null;
		} else if (!$can_be_zero && $validated_float === 0.00) {
			return null;
		} else {
			return $validated_float;
		}

	}

	public function validate_boolean(): ?bool {

		// will only validate to a boolean in a few circumstances. Read here to see where it will validate to bool: https://www.php.net/manual/en/filter.filters.validate.php
		return filter_var($this->primitive_data, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
		
	}

}
