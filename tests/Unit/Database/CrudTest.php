<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Remember, requires are from the root in tests
require_once 'src/ShinePHP/Database/Crud.php';
use ShinePHP\Database\{Crud, CrudException};

final class CrudUnitTest extends TestCase {

    // Testing table name sanitization
    public function test_valid_sanitiziation() : void {

    	// With whitelist
    	$this->assertEquals('table_1', Crud::sanitize_mysql('table_1', ['table_1', 'table_2']));

    	// No whitelist with no backticks
    	$this->assertEquals('`table_1`', Crud::sanitize_mysql('table_1'));

    	// No whitelist with backticks 
    	$this->assertEquals('```table_1```', Crud::sanitize_mysql('`table_1`'));
    }

    // Making sure non existent table names throw an exception
    public function test_invalid_sanitization() : void {

    	// Testing a table name that doesn't exist in the whitelist
    	$this->expectException(CrudException::class);
    	Crud::sanitize_mysql('table_1237812031', ['table_1', 'table_2']);

    }

}
