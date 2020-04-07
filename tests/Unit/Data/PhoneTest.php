<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Remember, requires are from the root in tests
require 'src/ShinePHP/Data/AmericanPhone.php';
use ShinePHP\Data\AmericanPhone;

final class PhoneTest extends TestCase {

    public function test_invalid(): void {

        // invalid number
        $invalid_phone = new AmericanPhone('asldkjf');
        $this->assertNull($invalid_phone->validate_phone());

        // invalid number too many characters
        $invalid_too_many = new AmericanPhone('123456789012');
        $this->assertNull($invalid_too_many->validate_phone());

        // invalid number no 1 in area code
        $invalid_no_one = new AmericanPhone('1238675309');
        $this->assertNull($invalid_no_one->validate_phone());
    }

    public function test_valid(): void {

        // valid phone numbers, no formatting required
        $valid_no_format = new AmericanPhone('408-867-5309');
        $this->assertEquals($valid_no_format->validate_phone(), '408-867-5309');

        // valid stripped phone numbers
        $valid_stripped = new AmericanPhone('+1 (408) 867 - 5309');
        $this->assertEquals($valid_stripped->stripped_phone(), '14088675309');

        // valid phone formats
        $valid_phone = new AmericanPhone('14088675309');
        $this->assertEquals($valid_phone->format_phone(), '(408) 867-5309');
        $valid_phone = new AmericanPhone('14088675309');
        $this->assertEquals($valid_phone->format_phone(true), '1 (408) 867-5309');
    }

}
