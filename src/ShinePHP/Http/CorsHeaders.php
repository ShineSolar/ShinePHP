<?php
declare(strict_types=1);

namespace ShinePHP\Http;

final class CorsHeaders {

	static function basic_cors(string $allowed_domain = '*', string $allowed_methods = 'GET, POST, OPTIONS'): void {
		\header('Access-Control-Allow-Origin: '.$allowed_domain);
		\header('Access-Control-Allow-Methods: '.$allowed_methods);
	}

	static function cors_allowed_headers(string $header_string): void {
		\header('Access-Control-Allow-Headers: '.$header_string);
	}

	static function cors_allow_cookies(): void {
		\header('Access-Control-Allow-Credentials: true');
	}

}
