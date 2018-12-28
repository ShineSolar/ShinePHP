<?php
declare(strict_types=1);

namespace ShinePHP;

require_once 'HandleData.php';
use ShinePHP\{HandleData, HandleDataException};

final class EasyHttp {

	protected $url;
	protected $headers;

	public function __construct(string $url, array $headers = []) {
		$this->url = HandleData::url($url);
		$this->headers = $headers;
	}

	public function makePostRequest(string $postFields) : array {
		$req = curl_init($this->url);
		curl_setopt($req, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($req, CURLOPT_POST, 1);
		curl_setopt($req, CURLOPT_POSTFIELDS, $jsonData);
		curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($req);
		curl_close($req);
		return $response;
	} 

	public function makeGetRequest() : array {
		$req = curl_init($this->url);
		curl_setopt($req, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($req);
		curl_close($req);
		return $response;
	}

	/**
	 *
	 * Checks if the request scheme is HTTPS
	 *
	 * @access public
	 *
	 * @throws EasyHttpException if the request is not served over HTTPS
	 * 
	 * @return void
	 *
	 */

	public static function checkHttps() : void {
		if ($_SERVER['REQUEST_SCHEME'] !== 'https' && $_SERVER['HTTP_HOST'] !== 'localhost') {
			throw new EasyHttpException('Not served over https');
		}
	}

	/**
	 *
	 * Checks to see if the request Content-Type is what you want it to be
	 *
	 * @access public
	 *
	 * @param string $type the Content-Type you want the request to have
	 *
	 * @throws EasyHttpException if the Content-Type does not match what you want it to match
	 * 
	 * @return void
	 *
	 */

	public static function checkContentType(string $type) : void {
		if ($_SERVER['CONTENT_TYPE'] !== $type) {
			throw new EasyHttpException("Wrong content-type. Type provided was: $_SERVER[CONTENT_TYPE]");
		}
	}

	/**
	 *
	 * Checks to see if the request method is what you want it to be
	 *
	 * Different from EasyHttp::isRequestMethod() because EasyHttp::checkRequestType() strictly checks against the request method used and what you want
	 * and throws an exception if it's not exactly what you want. Use EasyHttp::checkRequestType() when validating that API requests are correct and use
	 * EasyHttp::isRequestMethod() when routing website-based navigations (ie page views, form submits, etc...)
	 *
	 * @access public
	 *
	 * @param string $type the method you want the request to have
	 *
	 * @throws EasyHttpException if the method does not match what you want it to match
	 * 
	 * @return void
	 *
	 */

	public static function checkRequestType(string $type) : void {
		if ($_SERVER['REQUEST_METHOD'] !== $type) {
			throw new EasyHttpException("Wrong request method. Request method used was $_SERVER[REQUEST_METHOD]");
		}
	}

	/**
	 *
	 * Checks to see if the request method is what you want it to be and return a boolean based on that
	 *
	 * Different from EasyHttp::checkRequestType() because EasyHttp::checkRequestType() strictly checks against the request method used and what you want
	 * and throws an exception if it's not exactly what you want. Use EasyHttp::checkRequestType() when validating that API requests are correct and use
	 * EasyHttp::isRequestMethod() when routing website-based navigations (ie page views, form submits, etc...)
	 *
	 * @access public
	 *
	 * @param string $requestMethod the method you want to check against
	 *
	 * @throws EasyHttpException if the method does not match one of the 5 main REST methods
	 * 
	 * @return bool
	 *
	 */

	public static function isRequestMethod(string $requestMethod) : bool {

		// Switch statement on the 5 main REST HTTP methods
		switch ($requestMethod) {
			case 'GET':
				if ($_SERVER['REQUEST_METHOD'] === 'GET') return true;
				else return false;
			break;
			case 'POST':
				if ($_SERVER['REQUEST_METHOD'] === 'POST') return true;
				else return false;
			break;
			case 'PATCH':
				if ($_SERVER['REQUEST_METHOD'] === 'PATCH') return true;
				else return false;
			break;
			case 'DELETE':
				if ($_SERVER['REQUEST_METHOD'] === 'DELETE') return true;
				else return false;
			break;
			case 'PUT':
				if ($_SERVER['REQUEST_METHOD'] === 'PUT') return true;
				else return false;
			break;
			default:
				throw new EasyHttpException('Unrecognized HTTP request method passed');
		}

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

final class EasyHttpException extends \Exception {}
