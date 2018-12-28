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
	 *  @access public
	 *	@var array This is the data set on class init 
	 */
	public $data;

	/**
	 *
	 * @access public 
	 * Sets the data as the class variable. 
	 *
	 * @param array $data This is the data input 
	 * 
	 * @throws ArgumentCountError when there are no parameters passed
	 * @throws InvalidArgumentException when the parameter passed isn't an array
	 * @throws HandleDataException when the array is empty
	 *
	 */

	public function __construct(array $data) {

		// Checking if array is empty, if it is, throw exception, if not set class data
		if (empty($data)) {
			throw new HandleDataException('There was no data in the array passed to the constructor');
		} else {
			$this->data = $data;
		}

	}

	/**
	 *
	 * Makes it easy to validate an email address AND gives you control over the domain if you want
	 *
	 * @access public
	 *
	 * @param string $email this is the string you want validated as an email address
	 * @param OPTIONAL string $domainToValidate only pass a paramter to this if you only want email addresses belonging to certain domains to be validated
	 *
	 * @throws ArgumentCountError when there are no parameters passed
	 * @throws HandleDataException the email passed isn't a valid email OR when a valid email doesn't validate to the domain
	 * @throws InvalidArgumentException when the parameter is passed with the incorrect type
	 * 
	 * @return string valid email address
	 *
	 */

	public static function email(string $email, string $domainToValidate = '') : string {

		// setting the original variables
		$sanitizedEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
		$emailDomain = substr($sanitizedEmail, strpos($sanitizedEmail, "@") + 1);

		// Checking if 
		if (filter_var($sanitizedEmail, FILTER_VALIDATE_EMAIL) !== false) {
			if ($domainToValidate !== '' && $emailDomain !== $domainToValidate) {
				throw new HandleDataException('Email does not adhere to the domain validation passed');
			} else {
				return $sanitizedEmail;
			}
		} else {
			throw new HandleDataException('String passed was not a valid email address');
		}
	}

	public static function phone(string $phone) : string {
		//
	}

	/**
	 *
	 * Sanitize a string
	 *
	 * @access public
	 *
	 * @param string $string this is the string you want sanitized
	 * @param OPTIONAL bool $canBeEmpty set to false when 
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
	 *
	 * @access public
	 *
	 * @param string $url string you want validated as URL
	 *
	 * @throws ArgumentCountError when there are no parameters passed
	 * @throws HandleDataException if $url is not a valid URL
	 * @throws InvalidArgumentException when the parameter is passed with the incorrect type
	 * 
	 * @return string validated URL
	 *
	 */

	public static function url(string $url) : string {
		$sanitizedUrl = filter_var($url, FILTER_SANITIZE_URL);
		if (filter_var($sanitizedUrl, FILTER_VALIDATE_URL)) {
			return $sanitizedUrl;
		} else {
			throw new HandleDataException('Invalid URL passed');
		}
	}

	/**
	 *
	 * Return a boolean
	 *
	 * @access public
	 *
	 * @param mixed $variableToMakeBoolean variable you want returned as a boolean
	 *
	 * @throws ArgumentCountError when there are no parameters passed
	 * 
	 * @return bool
	 *
	 */

	public static function boolean($variableToMakeBoolean) : bool {
		return filter_var($variableToMakeBoolean, FILTER_VALIDATE_BOOLEAN);
	}

	public static function ipAddress(string $ip) : string {
		$validatedIp = filter_var($ip, FILTER_VALIDATE_IP);
		if ($validatedIp) {
			return $validtedIp;
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

	public function prepareAllForOutputValidation() : array {
		//
		foreach ($this->data as $data) {
			//
		}
	}

	public static function prepareSingularForOutputValidation(string $varToPrepare) : string {
		return htmlspecialchars($varToPrepare);
	}

	/**
	 *
	 * Makes it easy to accept JSON input from any url
	 *
	 * @access public
	 *
	 * @param OPTIONAL string $urlToRetrieveFrom this is the url that you want to pull JSON data from. Defaults to php://input because mostly it deals with inputs
	 *
	 * @throws HandleDataException there is null data retrieved from the url
	 * @throws InvalidArgumentException when the parameter is passed with the incorrect type
	 * 
	 * @return array of json data
	 *
	 */

	public static function turnJsonInputIntoArray(string $urlToRetrieveFrom = 'php://input') : array {

		// Check if JSON is null, if it is, throw HandleDataException, if not, return the decoded assoc array.
		if (json_decode(file_get_contents($urlToRetrieveFrom), true) === null) {
			throw new HandleDataException('No data retrieved from url: '.$urlToRetrieveFrom);
		} else {
			return json_decode(file_get_contents($urlToRetrieveFrom), true);
		}
		
	}

}

// Custom Exception class. We don't need any more functionality other than the built in Exception class
final class HandleDataException extends \Exception {}
