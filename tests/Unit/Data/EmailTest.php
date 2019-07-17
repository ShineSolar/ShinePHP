<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Remember, requires are from the root in tests
require 'src/ShinePHP/Data/EmailValidator.php';
use ShinePHP\Data\EmailValidator;

final class EmailTest extends TestCase {

    public function test_invalid(): void {

    	// regular not an email
        $EmailValidator = new EmailValidator('not an email');
        $this->assertNull($EmailValidator->validate_email());

        // valid email, but not matching domain
        $DomainValidator = new EmailValidator('amcgurk@shinesolar.com');
        $this->assertNull($DomainValidator->validate_email_domain('gmail.com'));

        // invalid email trying to validate domain
        $InvalidEmailDomainValidator = new EmailValidator('nope');
        $this->assertNull($InvalidEmailDomainValidator->validate_email_domain('gmail.com'));
    }

    public function test_valid(): void {

    	// regular valid email
        $EmailValidator = new EmailValidator('amcgurk@shinesolar.com');
        $this->assertEquals($EmailValidator->validate_email(), 'amcgurk@shinesolar.com');

        // domain validated email
        $DomainValidator = new EmailValidator('amcgurk@shinesolar.com');
        $this->assertEquals($DomainValidator->validate_email_domain('shinesolar.com'), 'amcgurk@shinesolar.com');
        
    }

}
