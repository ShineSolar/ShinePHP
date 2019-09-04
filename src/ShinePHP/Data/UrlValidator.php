<?php
declare(strict_types=1);

namespace ShinePHP\Data;

final class UrlValidator {

	private $validated_url;
	private $domain;
	private $protocol;

	public function __construct(string $raw_url) {
		$sanitized_url = \filter_var($raw_url, FILTER_SANITIZE_URL);
		$validated_url = \filter_var($sanitized_url, FILTER_VALIDATE_URL);
		$this->validated_url = ($validated_url === false ? null : $validated_url);

		if (!\is_null($this->validated_url)) {
			$this->domain = \parse_url($this->validated_url, PHP_URL_HOST);
			$this->protocol = \parse_url($this->validated_url, PHP_URL_SCHEME);
		}

	}

	public function validate_url(): ?string { return $this->validated_url; }

	public function validate_domain(string $domain): ?string {

		// not a valid url, so just stop
		if (\is_null($this->validated_url)) return null;

		// doing the domain check
		return ($this->domain === $domain ? $this->validated_url : null);

	}

	public function validate_protocol(string $protocol = 'https'): ?string {

		// not a valid url, so just stop
		if (\is_null($this->validated_url)) return null;

		// doing the domain check
		return ($this->protocol === $protocol ? $this->validated_url : null);

	}

}
