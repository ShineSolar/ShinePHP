<?php
declare(strict_types=1);

namespace ShinePHP;

/**
 * HandleData is a class to make a cleaner, simpler interface for working input data
 * HandleData is an interface built for PHP developers to reduce all of the code repeating in sanitizing and validating data
 * 
 * EXAMPLE USAGE:
 * $DataInput = new HandleData($_POST);
 * $email = $DataInput->email($DataInput['email']);
 * echo HandleData::output($email);
 *
 * @package HandleData
 * @author Adam McGurk <amcgurk@shinesolar.com>
 * @access public
 * @see https://github.com/ShineSolar/ShinePHP
 * 
 */

final class HandleData {

	/**
	 *
	 * Makes it easy to validate an email address AND gives you control over the domain if you want
	 *
	 * @access public
	 *
	 * @param string $email this is the string you want validated as an email address
	 * @param OPTIONAL string $optional_domain only pass a paramter to this if you only want email addresses belonging to certain domains to be validated
	 * 
	 * @return mixed valid email address or false on failure
	 *
	 */

	public static function email(string $email, string $optional_domain = '') {

		// setting the original variables
		$sanitized_email = filter_var($email, FILTER_SANITIZE_EMAIL);
		$domain = substr($sanitized_email, strpos($sanitized_email, "@") + 1);

		// Checking if 
		if (filter_var($sanitized_email, FILTER_VALIDATE_EMAIL) !== false) {
			if ($optional_domain !== '' && $domain !== $optional_domain) {
				return false;
			} else {
				return $sanitized_email;
			}
		} else {
			return false;
		}
	}

	/**
	 *
	 * Sanitize and validate a United States Phone Number
	 * TODO - Add 555 invalidation
	 *
	 * @access public
	 *
	 * @param string $phone string you want validated as a phone number
	 *
	 * @throws ArgumentCountError when there are no parameters passed
	 * @throws HandleDataException if $phone is not a valid United States phone number OR is an empty string
	 * @throws InvalidArgumentException when the parameter is passed with the incorrect type
	 * 
	 * @return string validated phone
	 *
	 */

	public static function american_phone(string $phone) : string {
		$strippedPhone = preg_replace('/[^0-9]/', '', self::string($phone, false));
		if (preg_match('/^1?[2-9]{1}[0-9]{2}[0-9]{3}[0-9]{4}$/', $strippedPhone) !== 1) {
			throw new HandleDataException('Invalid phone number');
		} else {
			return $strippedPhone;
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
	 * @throws ArgumentCountError when there are no parameters passed
	 * @throws HandleDataException if $canBeEmpty is false and the string is empty, throw cannot be empty exception
	 * @throws InvalidArgumentException when the parameter is passed with the incorrect type
	 * 
	 * @return string sanitized string
	 *
	 */

	public static function string($string, bool $canBeEmpty = true) : string {
		$sanitizedString = filter_var($string, FILTER_SANITIZE_STRING);
		if ($canBeEmpty === false && $sanitizedString === '') {
			throw new HandleDataException('Data cannot be empty');
		} else {
			return $sanitizedString;
		}
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
	 * @throws ArgumentCountError when there are no parameters passed
	 * @throws HandleDataException if $url is not a valid URL OR is an empty string
	 * @throws InvalidArgumentException when the parameter is passed with the incorrect type
	 * 
	 * @return string validated URL
	 *
	 */

	public static function url(string $url) : string {
		$sanitizedUrl = filter_var(self::string($url, false), FILTER_SANITIZE_URL);
		if (filter_var($sanitizedUrl, FILTER_VALIDATE_URL)) {
			return $sanitizedUrl;
		} else {
			throw new HandleDataException('Invalid URL passed');
		}
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

	public static function boolean($variable_to_make_boolean) : bool {
		return filter_var($variable_to_make_boolean, FILTER_VALIDATE_BOOLEAN);
	}

	/**
	 *
	 * Validate and return an ip address
	 *
	 * @access public
	 *
	 * @param string $ip variable you want validated as an ip address
	 *
	 * @throws ArgumentCountError when there are no parameters passed
	 * @throws HandleDataException if $ip cannot be validated as an ip address
	 * @throws InvalidArgumentException when the parameter is passed with the incorrect type
	 * 
	 * @return string
	 *
	 */

	public static function ipAddress(string $ip) : string {
		$validated_ip = filter_var($ip, FILTER_VALIDATE_IP);
		if ($validated_ip) {
			return $validated_ip;
		} else {
			throw new HandleDataException('Not a valid ip address');
		}
	}

	/**
	 *
	 * Validate and return a float value
	 *
	 * @access public
	 *
	 * @param mixed $number variable you want validated as a float
	 * @param OPTIONAL bool $canBeZero if set to true, the return can be 0.00
	 *
	 * @throws ArgumentCountError when there are no parameters passed
	 * @throws HandleDataException if $number cannot be validated as float
	 * @throws InvalidArgumentException when the parameter is passed with the incorrect type
	 * 
	 * @return float
	 *
	 */

	public static function float($number, bool $canBeZero = false) : float {

		// sanitizing and validating the input as a float
		$sanitizedNumber = filter_var($number, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
		$validatedFloat = filter_var($sanitizedNumber, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);

		// Doing the float checks and throwing exceptions or returning the valid float
		if ($validatedFloat === false) {
			throw new HandleDataException('Not a vaild float');
		} else if (!$canBeZero && $validatedFloat === 0.00) {
			throw new HandleDataException('Float cannot be 0. The return number is: '.$validatedFloat);
		} else {
			return $validatedFloat;
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
	 * @param OPTIONAL bool $canBeZero if set to true, the return can be 0
	 *
	 * @throws ArgumentCountError when there are no parameters passed
	 * @throws HandleDataException if $number cannot be validated as integer
	 * @throws InvalidArgumentException when the parameter is passed with the incorrect type
	 * 
	 * @return int
	 *
	 */

	public static function integer($number, bool $canBeZero = false) : int {

		// sanitizing and validating the input as an integer
		$sanitizedNumber = filter_var($number, FILTER_SANITIZE_NUMBER_INT);
		$validatedInt = filter_var($sanitizedNumber, FILTER_VALIDATE_INT);

		// Doing the integer checks and throwing exceptions or returning the valid integer
		if ($validatedInt === false) {
			throw new HandleDataException('Not a vaild integer');
		} else if (!$canBeZero && $validatedInt === 0) {
			throw new HandleDataException('Integer cannot be 0. The return number is: '.$validatedInt);
		} else {
			return $validatedInt;
		}

	}

	public static function prepareSingularForOutputValidation(string $varToPrepare) : string {
		return htmlspecialchars($varToPrepare);
	}

}

// Custom Exception class. We don't need any more functionality other than the built in Exception class
final class HandleDataException extends \Exception {}
