<?php
declare(strict_types=1);

namespace ShinePHP\Data;

final class EmailValidator {

	private $validated_email;
	private $email_domain;

	public function __construct(string $raw_email) {

		// setting the original variable
		$sanitized_email = \filter_var($raw_email, FILTER_SANITIZE_EMAIL);
		$filtered_email = \filter_var($sanitized_email, FILTER_VALIDATE_EMAIL);
		$this->validated_email = ($filtered_email === false ? null : $filtered_email);

		// Checking if it is actually a valid email after the sanitization
		if (!\is_null($this->validated_email)) {
			$this->email_domain = \substr($raw_email, \strpos($raw_email, "@") + 1);
		}

	}

	public function validate_email(): ?string { 
		return $this->validated_email; 
	}

	public function validate_email_domain(string $domain): ?string {

		if (\is_null($this->validated_email)) return $this->validated_email;

		return ($domain === $this->email_domain ? $this->validated_email : null);

	}

}
