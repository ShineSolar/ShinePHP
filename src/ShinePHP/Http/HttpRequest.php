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

	public function get(array $query_params = []) {

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

}
