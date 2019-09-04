<?php
declare(strict_types=1);

namespace ShinePHP\Data;

final class IpValidator {

	private $raw_address;

	public function __construct(string $raw_address) {
		$this->raw_address = $raw_address;
	}

	public function validate_general_ip() { return filter_var($this->raw_address, FILTER_VALIDATE_IP); }

	public function validate_general_ipv4() { return filter_var($this->raw_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4); }

	public function validate_public_ipv4() {

		if (!filter_var($this->raw_address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) { return false; }

		return filter_var($this->raw_address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE);

	}

	public function validate_private_ipv4() {

		// making sure this is a correct IPv4 address
		$filtered_ipv4 = filter_var($this->raw_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);

		// checking if it's at least a valid IPv4 address
		if (!$filtered_ipv4) {
			return false;
		} else {

			// doing the private check
			$private_address_check = filter_var($this->raw_address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE);

			return (!$private_address_check ? $filtered_ipv4 : false);

		}

	}

	public function validate_subnet_mask() {

		// running it against the validation regex, we can do better
		$validated_subnet = preg_match('/^(((255\.){3})|((255\.){2}(255|254|252|248|240|224|192|128|0+)\.0)|((255\.)(255|254|252|248|240|224|192|128|0+)(\.0+){2})|((255|254|252|248|240|224|192|128|0+)(\.0+){3}))$/', $this->raw_address);

		return ($validated_subnet === 1 ? $this->raw_address : false);
	}

	public function validate_public_ipv6() {

		// filtering out non IPv6, then no private IPv6, then no reserved IPv6
		$validated_ipv6 = filter_var($this->raw_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
		$validated_ipv6 = filter_var($validated_ipv6, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE);
		$validated_ipv6 = filter_var($validated_ipv6, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE);

		return ($validated_ipv6 === false ? null : $validated_ipv6);

	}

	public function validate_general_ipv6(): ?string {

		// validating the IP address
		$validated_ipv6 = filter_var($this->raw_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);

		return ($validated_ipv6 === false ? null : $validated_ipv6);
	}

	public function validate_private_ipv6() {

		// making sure it's an ipv6 address
		$filtered_ipv6 = filter_var($this->raw_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);

		if (!$filtered_ipv6) {
			return null;
		} else {

			// doing the private check
			$private_address_check = filter_var($this->raw_address, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE);

			return (!$private_address_check ? $filtered_ipv6 : null);

		}

	}

}