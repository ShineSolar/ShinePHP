<?php
declare(strict_types=1);

namespace ShinePHP\Data;

interface PhoneValidator {

	public function __construct(string $phone_input);

	public function format_phone($include_country_code = false): ?string;

	public function validate_phone(): ?string;

	public function stripped_phone(): ?string;

}

final class AmericanPhone implements PhoneValidator {

	private $stripped_phone;
	private $raw_phone;
	const PHONE_REGEX = '/^1?[2-9]{1}[0-9]{2}[0-9]{3}[0-9]{4}$/';

	public function __construct(string $phone_input) {

		// setting just the raw input AND the stripped input
		$this->raw_phone = $phone_input;
		$this->stripped_phone = \preg_replace('/[^0-9]/', '', $this->raw_phone);

	}

	public function format_phone($include_country_code = false): ?string {

		// validating phone to make sure the string replacement will work...if not valid, return null
		$validated_phone = $this->validate_phone();
		if (\is_null($validated_phone)) return null;

		// setting the validated phone to just the 10 digits that make up an American phone number
		// The country code will be added later if necessary
		$validated_phone = (\substr($this->stripped_phone,0,1) === '1' ? \substr($this->stripped_phone,1) : $this->stripped_phone);

		// breaking them out into the individual strings needed for concatenation
		$area_code = \substr($validated_phone,0,3);
		$prefix = \substr($validated_phone,3,3);
		$last_four = \substr($validated_phone,6,4);

		// building the number, because regardless of the country code, the base number will still be formatted the same
		$formatted_number = '('.$area_code.') '.$prefix.'-'.$last_four;

		// returning the formate requested
		return ($include_country_code ? '1 '.$formatted_number : $formatted_number);

	}

	public function stripped_phone(): ?string {

		// validating phone number
		return (\is_null($this->validate_phone()) ? null : $this->stripped_phone);

	}

	public function validate_phone(): ?string {

		// checking if the stripped phone number passes the regex
		$validated_number = \preg_match(self::PHONE_REGEX, $this->stripped_phone);

		// if it does, it's safe to assume the raw phone number was valid, it just had other characters in it
		return (!$validated_number ? null : $this->raw_phone);

	}

}
