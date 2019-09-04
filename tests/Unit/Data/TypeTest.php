<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Remember, requires are from the root in tests
require 'src/ShinePHP/Data/TypeValidator.php';
use ShinePHP\Data\TypeValidator;

final class TypeTest extends TestCase {

    public function test_invalid(): void {

        // can't validate an empty string
        $invalid_string = new TypeValidator('');
        $this->assertNull($invalid_string->validate_string());

        // a string isn't an integer
        $invalid_int_string = new TypeValidator('not an integer');
        $this->assertNull($invalid_int_string->validate_int());

        // can't validate 0 as int
        $invalid_int_zero = new TypeValidator(0);
        $this->assertNull($invalid_int_zero->validate_int());

        // a string isn't a float
        $invalid_float_string = new TypeValidator('not a float');
        $this->assertNull($invalid_float_string->validate_float());

        // can't validate 0 as float
        $invalid_float_zero = new TypeValidator(0);
        $this->assertNull($invalid_float_zero->validate_float());

        // text isn't boolean
        $invalid_boolean = new TypeValidator('alskjdfa');
        $this->assertNull($invalid_boolean->validate_boolean());

    }

    public function test_valid(): void {

        // valid string
        $valid_string = new TypeValidator('this is a valid string');
        $this->assertEquals($valid_string->validate_string(), 'this is a valid string');

        // string can be empty
        $valid_empty_string = new TypeValidator('');
        $this->assertEquals($valid_empty_string->validate_string(true), '');

        // valid int
        $valid_int = new TypeValidator(123);
        $this->assertEquals($valid_int->validate_int(), 123);

        // valid zero
        $valid_int_zero = new TypeValidator(0);
        $this->assertEquals($valid_int_zero->validate_int(), 0);

        // valid float
        $valid_float = new TypeValidator(12.45);
        $this->assertEquals($valid_float->validate_float(), 12.45);

        // valid zero float
        $valid_float_zero = new TypeValidator(0);
        $this->assertEquals($valid_float_zero->validate_float(), 0.00);

        // valid true boolean
        $valid_true_boolean = new TypeValidator(true);
        $this->assertTrue($valid_true_boolean->validate_boolean());
        $valid_true_boolean = new TypeValidator(1);
        $this->assertTrue($valid_true_boolean->validate_boolean());
        $valid_true_boolean = new TypeValidator('1');
        $this->assertTrue($valid_true_boolean->validate_boolean());
        $valid_true_boolean = new TypeValidator('yes');
        $this->assertTrue($valid_true_boolean->validate_boolean());
        $valid_true_boolean = new TypeValidator('on');
        $this->assertTrue($valid_true_boolean->validate_boolean());

        // valid false boolean
        $valid_false_boolean = new TypeValidator(false);
        $this->assertFalse($valid_false_boolean->validate_boolean());
        $valid_false_boolean = new TypeValidator(0);
        $this->assertFalse($valid_false_boolean->validate_boolean());
        $valid_false_boolean = new TypeValidator('0');
        $this->assertFalse($valid_false_boolean->validate_boolean());
        $valid_false_boolean = new TypeValidator('no');
        $this->assertFalse($valid_false_boolean->validate_boolean());
        $valid_false_boolean = new TypeValidator('off');
        $this->assertFalse($valid_false_boolean->validate_boolean());
        $valid_false_boolean = new TypeValidator('');
        $this->assertFalse($valid_false_boolean->validate_boolean());

    }

}
