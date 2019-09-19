<?php
declare(strict_types=1);

namespace ShinePHP\Http;

final class HttpRequest {

	/** 
	 *  @access private
	 *	@var CURLResource the actual curl resource used throughout the app
	 */
	private $curl_handle;

	public function __construct(string $url, string $method = 'GET', array $query_params = array()) {

		// setting the class vars
		$verified_method = self::verify_method($method);

		$this->curl_handle = \curl_init(self::build_url($url, $query_params));

		if ($verified_method === 'POST') {
			\curl_setopt($this->curl_handle, CURLOPT_POST, 1);
		} else if ($verified_method !== 'GET') {
			\curl_setopt($this->curl_handle, CURLOPT_CUSTOMREQUEST, $verified_method);
		}

		\curl_setopt($this->curl_handle, CURLOPT_RETURNTRANSFER, true);
		\curl_setopt($this->curl_handle, CURLOPT_FAILONERROR, true);
		\curl_setopt($this->curl_handle, CURLOPT_DEFAULT_PROTOCOL, 'https');

	}

	public function set_request_headers(array $headers): void {
		\curl_setopt($this->curl_handle, CURLOPT_HTTPHEADER, $headers);
	}

	public function do_basic_auth(string $username, string $password): void {
		\curl_setopt($this->curl_handle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		\curl_setopt($this->curl_handle, CURLOPT_USERPWD, $username.':'.$password);
	}

	public function set_oauth_bearer_token(string $token): void {
		\curl_setopt($this->curl_handle, CURLOPT_XOAUTH2_BEARER, $token);
	}

	public function send($post_data = '', bool $throw_on_error = false): ?string {

		if (!empty($post_data)) {
			\curl_setopt($this->curl_handle, CURLOPT_POSTFIELDS, $post_data);
		}

		$curl_response = \curl_exec($this->curl_handle);

		if ($curl_response === false && $throw_on_error === true) {
			throw new HttpRequestException('HTTP Error: '.curl_error($this->curl_handle));
		}

		return $curl_response;

	}

	public static function verify_method(string $method): string {

		// Request verbs should be normalized to uppercase
		$upper_cased_method = \strtoupper($method);

		switch ($upper_cased_method) {

			// right now we only support POST, GET, PUT, and DELETE
			case 'POST':
			case 'GET':
			case 'PUT':
			case 'DELETE':
				return $upper_cased_method;
			break;

			default:
				throw new HttpRequestException('The HTTP request method must be one of POST, GET, PUT, or DELETE');

		}

	}

	public static function build_url(string $url, array $query_params): string {

		if (empty($query_params)) {
			return $url;
		}

		$parsed_url = \parse_url($url);

		return (isset($parsed_url['query']) ? $url.'&'.\http_build_query($query_params) : $url.'?'.\http_build_query($query_params));

	}

}

final class HttpRequestException extends \Exception {}
