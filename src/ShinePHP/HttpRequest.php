<?php
declare(strict_types=1);

namespace ShinePHP;

final class HttpRequest {

	/** 
	 *  @access private
	 *	@var string This is the base URL that you will be using to make calls on this instance of the class
	 */
	private $url;

	/** 
	 *  @access private
	 *	@var array This is the array of headers that will be passed on every request
	 */
	private $headers;

	public function __construct(string $url, array $headers = []) {

		// setting the class vars
		$this->url = $url;
		$this->headers = $headers;

	}

	public function post(string $stringified_data = '', array $query_params = []) {

		// setting URL
		if (!empty($query_params)) {

			$parsed_url = \parse_url($this->url);

			$url = (isset($parsed_url['query']) ? $this->url.'&'.http_build_query($query_params) : $this->url.'?'.http_build_query($query_params));

		} else {
			$url = $this->url;
		}

		// opening cURL
		$req = curl_init($url);

		// setting cURL options
		curl_setopt($req, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($req, CURLOPT_POST, 1);
		curl_setopt($req, CURLOPT_POSTFIELDS, $stringified_data);
		curl_setopt($req, CURLOPT_RETURNTRANSFER, true);

		// executing and closing the request
		$response = curl_exec($req);
		curl_close($req);

		return $response;

	} 

	public function get(string $stringified_data = '', array $query_params = []) {

		// setting URL
		if (!empty($query_params)) {

			$parsed_url = \parse_url($this->url);

			$url = (isset($parsed_url['query']) ? $this->url.'&'.http_build_query($query_params) : $this->url.'?'.http_build_query($query_params));

		} else {
			$url = $this->url;
		}

		// opening cURL
		$url = (empty($query_params) ? $this->url : $this->url.http_build_query($query_params));
		$req = curl_init($url);

		// setting cURL options
		curl_setopt($req, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($req, CURLOPT_RETURNTRANSFER, true);

		// executing and closing the request
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
	 * @return bool
	 *
	 */

	public static function is_https() : bool { return ($_SERVER['REQUEST_SCHEME'] === 'https' || $_SERVER['HTTP_HOST'] === 'localhost' ? true : false); }

	/**
	 *
	 * Checks to see if the request Content-Type is what you want it to be
	 *
	 * @access public
	 *
	 * @param string $type the Content-Type you want the request to have
	 * 
	 * @return bool
	 *
	 */

	public static function is_content_type(string $type) : bool { return ($_SERVER['CONTENT_TYPE'] === $type ? true : false); }

	/**
	 *
	 * Checks to see if the request method is what you want it to be and return a boolean based on that
	 *
	 * @access public
	 *
	 * @param string $method the method you want to check against
	 * 
	 * @return bool
	 *
	 */

	public static function is_request_method(string $method) : bool { return ($_SERVER['REQUEST_METHOD'] === $method ? true : false); }

	/**
	 *
	 * Makes it easy to accept JSON input from any url
	 *
	 * @access public
	 *
	 * @param OPTIONAL string $retrieve_url this is the url that you want to pull JSON data from. Defaults to php://input because mostly it deals with inputs
	 * 
	 * @return array of json data or empty array if there is no json
	 *
	 */

	public static function get_json_input(string $retrieve_url = 'php://input') : array {

		// decode JSON array
		$decoded_json = json_decode(file_get_contents($retrieve_url), true);

		// return an empty array if there was no json to return, otherwise return the decoded json
		return (is_null($decoded_json) ? array() : $decoded_json);
		
	}

}
