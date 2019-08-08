<?php
declare(strict_types=1);

namespace ShinePHP\Http;

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

	/** 
	 *  @access private
	 *	@var string The REST method that we will be using
	 */
	private $method;

	public function __construct(string $url, string $method, array $headers = array()) {

		// setting the class vars
		$this->url = $url;
		$this->headers = $headers;
		$this->method = $this->verify_method($method);

	}

	public send($prepared_data = '', array $query_params = array()) {
		$this->build_url($query_params);
		return self::request($prepared_data, $this->method);
	}

	private function verify_method(string $method): string {

		$upper_cased_method = strtoupper($method);

		switch ($upper_cased_method) {

			case 'POST':
			case 'GET':
			case 'DELETE':
			case 'PUT':
			case 'HEAD':
				return $upper_cased_method;
			break;

			default:
				throw new Exception('The HTTP request method must be one of: POST, GET, PUT, DELETE, or HEAD');

		}

	}

	public function build_url(array $query_params): void {

		if (empty($query_params)) {
			return $this->url;
		}

		$parsed_url = \parse_url($this->url);

		return (isset($parsed_url['query']) ? $this->url.'&'.http_build_query($query_params) : $this->url.'?'.http_build_query($query_params));

	}

	private static function request($prepared_data, string $method) {

		$request = curl_init($this->url);
		curl_setopt($request, CURLOPT_HTTPHEADER, $this->headers);
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);

		if ($method === 'POST') {
			curl_setopt($request, CURLOPT_POST, 1);
			curl_setopt($request, CURLOPT_POSTFIELDS, $prepared_data);
		}

		$response = curl_exec($request);

		curl_close($request);

		return $response;

	}

}
