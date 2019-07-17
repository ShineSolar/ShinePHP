<?php
declare(strict_types=1);

namespace ShinePHP;

/**
 * HandleData is a class to make a cleaner, simpler interface for working input data
 * HandleData is an interface built for PHP developers to reduce all of the code repeating in sanitizing and validating data
 *
 * @author Adam McGurk <amcgurk@shinesolar.com>
 * @access public
 * @see https://github.com/ShineSolar/ShinePHP
 * 
 */

final class HandleData {

	/**
	 *
	 * Sanitize and validate a United States Phone Number, optionally including the "1" area code
	 *
	 * @access public
	 *
	 * @param string $phone string you want validated as a phone number
	 * @param OPTIONAL bool $include_us_country_code decide if you want a leading one in it or not
	 * 
	 * @return mixed validated phone or false on failure
	 *
	 */

	public static function american_phone(string $phone, bool $include_us_country_code = false) {

		$stripped_phone = preg_replace('/[^0-9]/', '', self::string($phone, false));

		// checking to see if it just matches basic phone validation anyways
		if (preg_match('/^1?[2-9]{1}[0-9]{2}[0-9]{3}[0-9]{4}$/', $stripped_phone) !== 1) {

			// return false on failure
			return false;

		} else {

			// checking the country code flag
			if ($include_us_country_code) { return (substr($stripped_phone,0,1) === '1' ? $stripped_phone : '1'.$stripped_phone); } 
			else { return (substr($stripped_phone,0,1) === '1' ? substr($stripped_phone,1) : $stripped_phone); }

		}

	}

	/**
	 *
	 * Sanitize a string
	 *
	 * @access public
	 *
	 * @param string $string this is the string you want sanitized
	 * @param OPTIONAL bool $canBeEmpty set to false when the string cannot be empty
	 * 
	 * @return mixed the sanitized string if successful, or false if not
	 *
	 */

	public static function string($string, bool $can_be_empty = true) {

		// sanitizing the actual string
		$sanitized_string = filter_var($string, FILTER_SANITIZE_STRING);

		// running the check
		return ($can_be_empty === false && $sanitized_string === '' ? false : $sanitized_string);

	}

	/**
	 *
	 * Sanitize and validate url
	 * TODO add domain validation
	 *
	 * @access public
	 *
	 * @param string $url string you want validated as URL
	 * 
	 * @return mixed validated URL on success or false
	 *
	 */

	public static function url(string $url) {

		// sanitizing the url
		$sanitized_url = filter_var(self::string($url, false), FILTER_SANITIZE_URL);

		// returning the values
		return (filter_var($sanitized_url, FILTER_VALIDATE_URL) ? $sanitized_url : false);

	}

	/**
	 *
	 * Return a boolean based on any data you provide
	 *
	 * @access public
	 *
	 * @param mixed $variable_to_make_boolean variable you want returned as a boolean
	 * 
	 * @return bool
	 *
	 */

	public static function boolean($variable_to_make_boolean) : bool { return filter_var($variable_to_make_boolean, FILTER_VALIDATE_BOOLEAN); }

	/**
	 *
	 * Validate and return a float value
	 *
	 * @access public
	 *
	 * @param mixed $number variable you want validated as a float
	 * @param OPTIONAL bool $can_be_zero if set to true, the return can be 0.00
	 * 
	 * @return float on success, if not valid float, return false
	 *
	 */

	public static function float($number, bool $can_be_zero = false) {

		// sanitizing and validating the input as a float
		$sanitized_number = filter_var($number, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		$validated_float = filter_var($sanitized_number, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);

		// Doing the float checks and throwing exceptions or returning the valid float
		if ($validated_float === false) {
			return false;
		} else if (!$can_be_zero && $validated_float === 0.00) {
			return false;
		} else {
			return $validated_float;
		}

	}

	/**
	 *
	 * Validate and return an integer variable
	 *
	 * GOTCHA: If you pass a float, like 7.50, it will just drop the decimal and trailing zeroes. So 7.50 will be returned as 75
	 * 
	 * @access public
	 *
	 * @param mixed $number variable you want validated as an integer
	 * @param OPTIONAL bool $can_be_zero if set to true, the return can be 0
	 * 
	 * @return int on success, if not valid int, return false
	 *
	 */

	public static function integer($number, bool $can_be_zero = false) {

		// sanitizing and validating the input as an integer
		$sanitized_number = filter_var($number, FILTER_SANITIZE_NUMBER_INT);
		$validated_int = filter_var($sanitized_number, FILTER_VALIDATE_INT);

		// Doing the integer checks and throwing exceptions or returning the valid integer
		if ($validated_int === false) {
			return false;
		} else if (!$can_be_zero && $validated_int === 0) {
			return false;
		} else {
			return $validated_int;
		}

	}

}

final class PrimitiveDataValidator {

	private $primitive_data;

	public function __construct($primitive_data) {
		$this->primitive_data = $primitive_data;
	}

	public function validate_string(bool $can_be_empty = false) {

		// sanitizing the actual string
		$sanitized_string = filter_var($this->primitive_data, FILTER_SANITIZE_STRING);

		// running the check
		return ($can_be_empty === false && $sanitized_string === '' ? false : $sanitized_string);

	}

	public function validate_int(bool $can_be_zero = false) {

		// sanitizing and validating the input as an integer
		$sanitized_number = filter_var($this->primitive_data, FILTER_SANITIZE_NUMBER_INT);
		$validated_int = filter_var($sanitized_number, FILTER_VALIDATE_INT);

		// Doing the integer checks and throwing exceptions or returning the valid integer
		if ($validated_int === false) {
			return false;
		} else if (!$can_be_zero && $validated_int === 0) {
			return false;
		} else {
			return $validated_int;
		}

	}

	public function validate_float(bool $can_be_zero = false) {

		// sanitizing and validating the input as a float
		$sanitized_number = filter_var($this->primitive_data, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		$validated_float = filter_var($sanitized_number, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);

		// Doing the float checks and throwing exceptions or returning the valid float
		if ($validated_float === false) {
			return false;
		} else if (!$can_be_zero && $validated_float === 0.00) {
			return false;
		} else {
			return $validated_float;
		}

	}

	public function validate_boolean(): bool { return filter_var($this->primitive_data, FILTER_VALIDATE_BOOLEAN); }

}

final class EmailValidator {

	private $validated_email;
	private $email_domain;

	public function __construct(string $raw_email) {

		// setting the original variable
		$sanitized_email = filter_var($raw_email, FILTER_SANITIZE_EMAIL);
		$this->validated_email = filter_var($sanitized_email, FILTER_VALIDATE_EMAIL);

		// Checking if it is actually a valid email after the sanitization
		if ($this->validated_email !== false) {
			$this->email_domain = substr($raw_email, strpos($raw_email, "@") + 1);
		}

	}

	public function validate_email() { return $this->validated_email; }

	public function validate_email_domain(string $domain) {

		if (!$this->validated_email) return false;

		return ($domain === $this->email_domain ? $this->validated_email : false);

	}

}

final class UrlValidator {

	private $domain;
	private $protocol;
	private $validated_url;

	public function __construct(string $raw_url) {
		$sanitized_url = filter_var($raw_url, FILTER_SANITIZE_URL);
		$this->validated_url = filter_var($sanitized_url, FILTER_VALIDATE_URL);
		$this->domain = '';//;
	}

	public function validate_url() { return $this->validated_url; }

	public function validate_domain(string $domain) {

		// not a valid url, so just stop
		if (!$this->validated_url) return false;

		// doing the domain check
		return ($this->domain === $domain ? $this->validated_url : false);

	}

	public function validate_protocol(string $protocol) {

		// not a valid url, so just stop
		if (!$this->validated_url) return false;

		// doing the domain check
		return ($this->protocol === $protocol ? $this->validated_url : false);

	}

}

final class IpValidator {

	private $raw_address;

	public function __construct(string $raw_address) {
		$this->raw_address = $raw_address;
	}

	public function validate_general_ip() { return filter_var($this->raw_address, FILTER_VALIDATE_IP); }

	public function validate_general_ipv4() { return filter_var($this->raw_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4); }

	public function validate_public_ipv4() {

		if (!filter_var($this->raw_address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) { return false; }

		return filter_var($this->raw_address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE);

	}

	public function validate_private_ipv4() {

		// making sure this is a correct IPv4 address
		$filtered_ipv4 = filter_var($this->raw_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);

		// checking if it's at least a valid IPv4 address
		if (!$filtered_ipv4) {
			return false;
		} else {

			// doing the private check
			$private_address_check = filter_var($this->raw_address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE);

			return (!$private_address_check ? $filtered_ipv4 : false);

		}

	}

	public function validate_subnet_mask() {

		// running it against the validation regex, we can do better
		$validated_subnet = preg_match('/^(((255\.){3})|((255\.){2}(255|254|252|248|240|224|192|128|0+)\.0)|((255\.)(255|254|252|248|240|224|192|128|0+)(\.0+){2})|((255|254|252|248|240|224|192|128|0+)(\.0+){3}))$/', $this->raw_address);

		return ($validated_subnet === 1 ? $this->raw_address : false);
	}

	public function validate_public_ipv6() { return filter_var($this->raw_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6, FILTER_FLAG_NO_PRIV_RANGE); }

	public function validate_general_ipv6() { return filter_var($this->raw_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6); }

	public function validate_private_ipv6() {

		// doing the check for the local IPv6 address, which isn't being filtered out by the FILTER_FLAG_NO_PRIV_RANGE for some reason...bug report in process
		if ($this->raw_address === '::1') { return '::1'; }

		// making sure it's an ipv6 address
		$filtered_ipv6 = filter_var($this->raw_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);

		if (!$filtered_ipv6) {
			return false;
		} else {

			// doing the private check
			$private_address_check = filter_var($this->raw_address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE);

			return (!$private_address_check ? $filtered_ipv6 : false);

		}

	}

}
