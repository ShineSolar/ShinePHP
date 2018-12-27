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

	public static function email(string $email) : string {
		//
	}

	public static function phone(string $phone) : string {
		//
	}

	public static function string(string $string) : string {
		//
	}

	public static function url(string $url) : string {
		//
	}

	public static function ipAddress(string $ip, bool $isIpV6 = false) : string {
		//
	}

	public static function float($number, bool $canBeZero = false) : float {
		//
	}

	public static function integer($number, bool $canBeZero = false) : int {
		//
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
