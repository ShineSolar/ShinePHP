<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Remember, requires are from the root in tests
require 'src/ShinePHP/Data/IpValidator.php';
use ShinePHP\Data\IpValidator;

final class IpTest extends TestCase {

    public function test_invalid(): void {

        // general invalid ip address
        $reg_invalid = new IpValidator('256.71.83.1');
        $this->assertFalse($reg_invalid->validate_general_ip());

        // not an IPv4 address
        $invalid_ipv4 = new IpValidator('FC80:0000:0000:0000:903A:1C1A:E802:11E4');
        $this->assertFalse($invalid_ipv4->validate_general_ipv4());

        // not a public IPv4 address
        $invalid_public_ipv4_reserved = new IpValidator('127.0.0.1');
        $this->assertFalse($invalid_public_ipv4_reserved->validate_public_ipv4());
        $invalid_public_ipv4_private = new IpValidator('192.168.0.1');
        $this->assertFalse($invalid_public_ipv4_private->validate_public_ipv4());

        // not a valid private IPv4 address
        $invalid_private_ipv4_reserved = new IpValidator('127.0.0.1');
        $this->assertFalse($invalid_private_ipv4_reserved->validate_private_ipv4());
        $invalid_private_ipv4_public = new IpValidator('207.124.51.1');
        $this->assertFalse($invalid_private_ipv4_public->validate_private_ipv4());

        // not a valid IPv6 address
        $invalid_ipv6 = new IpValidator('207.87.10.1');
        $this->assertNull($invalid_ipv6->validate_general_ipv6());

        // not a valid private IPv6 address
        $invalid_private_ipv6 = new IpValidator('::1');
        $this->assertNull($invalid_private_ipv6->validate_private_ipv6());

        // not a valid public IPv6 address
        $invalid_private_ipv6 = new IpValidator('::1');
        $this->assertNull($invalid_private_ipv6->validate_public_ipv6());

        // not a valid subnet address
        $invalid_subnet = new IpValidator('192.168.0.1');
        $this->assertFalse($invalid_subnet->validate_subnet_mask());

    }

    public function test_valid(): void {

        // valid regular ipv4 addresses
        $reg_valid = new IpValidator('192.168.0.1');
        $this->assertEquals('192.168.0.1', $reg_valid->validate_general_ip());
        $reg_valid = new IpValidator('127.0.0.1');
        $this->assertEquals('127.0.0.1', $reg_valid->validate_general_ip());
        $reg_valid = new IpValidator('207.81.75.1');
        $this->assertEquals('207.81.75.1', $reg_valid->validate_general_ip());

        // valid regular ipv6 addresses
        $reg_valid = new IpValidator('::1');
        $this->assertEquals('::1', $reg_valid->validate_general_ip());
        $reg_valid = new IpValidator('FC80:0000:0000:0000:903A:1C1A:E802:11E4');
        $this->assertEquals('FC80:0000:0000:0000:903A:1C1A:E802:11E4', $reg_valid->validate_general_ip());
        $reg_valid = new IpValidator('1200:0000:AB00:1234:0000:2552:7777:1313');
        $this->assertEquals('1200:0000:AB00:1234:0000:2552:7777:1313', $reg_valid->validate_general_ip());

        // valid general IPv4 address
        $gen_ipv4 = new IpValidator('127.0.0.1');
        $this->assertEquals('127.0.0.1', $gen_ipv4->validate_general_ipv4());

        // valid private IPv4 address
        $private_ipv4 = new IpValidator('192.168.0.1');
        $this->assertEquals('192.168.0.1', $private_ipv4->validate_private_ipv4());

        // valid public IPv4 address
        $public_ipv4 = new IpValidator('207.81.75.1');
        $this->assertEquals('207.81.75.1', $public_ipv4->validate_public_ipv4());

        // valid general IPv6 address
        $gen_ipv6 = new IpValidator('::1');
        $this->assertEquals('::1', $gen_ipv6->validate_general_ipv6());

        // valid private IPv6 address
        $priv_ipv6 = new IpValidator('FC80:0000:0000:0000:903A:1C1A:E802:11E4');
        $this->assertEquals('FC80:0000:0000:0000:903A:1C1A:E802:11E4', $priv_ipv6->validate_private_ipv6());

        // valid public IPv6 address
        $public_ipv6 = new IpValidator('1200:0000:AB00:1234:0000:2552:7777:1313');
        $this->assertEquals('1200:0000:AB00:1234:0000:2552:7777:1313', $public_ipv6->validate_general_ip());

        // valid subnet
        $valid_subnet = new IpValidator('255.255.255.0');
        $this->assertEquals('255.255.255.0', $valid_subnet->validate_subnet_mask());

    }

}
