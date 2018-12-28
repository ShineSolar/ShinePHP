<?php
declare(strict_types=1);

namespace ShinePHP;

final class EasyHttp {

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

final class EasyHttpException extends \Exception {}
