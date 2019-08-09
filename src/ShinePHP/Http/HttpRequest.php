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

	public function send($prepared_data = '', array $query_params = array()): ?string {
		return self::request(array(
			'url' => self::build_url($this->url, $query_params),
			'headers' => $this->headers,
			'method' => $this->method
		), $prepared_data);
	}

	private function verify_method(string $method): string {

		// Request verbs should be normalized to uppercase
		$upper_cased_method = strtoupper($method);

		switch ($upper_cased_method) {

			// right now we only support POST and GET
			case 'POST':
			case 'GET':
				return $upper_cased_method;
			break;

			default:
				throw new \Exception('The HTTP request method must be one of POST or GET');

		}

	}

	public static function build_url(string $url, array $query_params): string {

		if (empty($query_params)) {
			return $url;
		}

		$parsed_url = \parse_url($url);

		return (isset($parsed_url['query']) ? $url.'&'.http_build_query($query_params) : $url.'?'.http_build_query($query_params));

	}

	private static function request(array $request_configs, $prepared_data): ?string {

		$request = curl_init($request_configs['url']);
		curl_setopt($request, CURLOPT_HTTPHEADER, $request_configs['headers']);
		curl_setopt($request, CURLOPT_RETURNTRANSFER, true);

		if ($request_configs['method'] === 'POST') {
			curl_setopt($request, CURLOPT_POST, 1);
			curl_setopt($request, CURLOPT_POSTFIELDS, $prepared_data);
		}

		$response = curl_exec($request);

		curl_close($request);

		return $response;

	}

}
