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
