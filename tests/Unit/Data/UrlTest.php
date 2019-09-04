<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Remember, requires are from the root in tests
require 'src/ShinePHP/Data/UrlValidator.php';
use ShinePHP\Data\UrlValidator;

final class UrlTest extends TestCase {

    public function test_invalid(): void {

        // regular invalid test
        $reg_invalid = new UrlValidator('not a url');
        $this->assertNull($reg_invalid->validate_url());

        // doesn't match domain
        $invalid_domain = new UrlValidator('https://shinesolar.com');
        $this->assertNull($invalid_domain->validate_domain('google.com'));

        // doesn't match protocol
        $invalid_protocol = new UrlValidator('https://shinesolar.com');
        $this->assertNull($invalid_domain->validate_protocol('http'));

    }

    public function test_valid(): void {

        // regular valid test
        $valid_domain = new UrlValidator('https://gmail.com');
        $this->assertEquals($valid_domain->validate_url(), 'https://gmail.com');

        // domain valid test
        $valid_domain = new UrlValidator('https://shinesolar.com');
        $this->assertEquals($valid_domain->validate_domain('shinesolar.com'), 'https://shinesolar.com');

        // protocol valid test, no paramter required for https
        $valid_domain = new UrlValidator('https://gmail.com');
        $this->assertEquals($valid_domain->validate_protocol(), 'https://gmail.com');
    }

}
