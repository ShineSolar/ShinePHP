<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Remember, requires are from the root in tests
require 'tests/db_details.php';
require 'src/ShinePHP/Crud.php';
use ShinePHP\{Crud, CrudException};

final class CrudTest extends TestCase {

	// Testing a valid class init
	public function testCanBeCreatedWithDatabaseCredentials() : void {
        $this->assertInstanceOf(
            Crud::class,
            new Crud()
        );
    }

    // Testing valid INSERT/UPDATE/DELETE statements 
    public function testValidChange() : void {

    	$db_connect = new Crud();

    	// Testing INSERT/UPDATE/DELETE statement with no placeholder values or rowCount
    	$this->assertArrayHasKey('row_count', $db_connect->change('INSERT INTO table_1 VALUES (11, "Stephaine Laub")', []));
    	$this->assertArrayHasKey('row_count', $db_connect->change('UPDATE table_1 SET name="Stephaine Chandler" WHERE name="Stephaine Laub"', []));
    	$this->assertArrayHasKey('row_count', $db_connect->change('DELETE FROM table_1 WHERE name="Stephaine Chandler"', []));

    	// Testing INSERT/UPDATE/DELETE statement with placeholder values, but no rowCount
    	$this->assertArrayHasKey('row_count', $db_connect->change('INSERT INTO table_1 VALUES (21, ?)', ['Brian Laub']));
    	$this->assertArrayHasKey('row_count', $db_connect->change('UPDATE table_1 SET name="Chandler" WHERE name=?', ['Brian Laub']));
    	$this->assertArrayHasKey('row_count', $db_connect->change('DELETE FROM table_1 WHERE name=?', ['Chandler']));

    	// Testing INSERT/UPDATE/DELETE statement with placeholder values and rowCount
    	$this->assertArrayHasKey('row_count', $db_connect->change('INSERT INTO table_1 VALUES (31, ?)', ['Ryan Andersen']));
    	$this->assertArrayHasKey('row_count', $db_connect->change('UPDATE table_1 SET name="Andersen" WHERE name=?', ['Ryan Andersen']));
    	$this->assertArrayHasKey('row_count', $db_connect->change('DELETE FROM table_1 WHERE name=?', ['Andersen']));

    }

    // Testing invalid INSERT/UPDATE/DELETE statement no bound parameters, but placeholders passed
    public function testInvalidChangeNoParametersBound() : void {

    	$db_connect = new Crud();

    	// Testing INSERT statement with no placeholder values, but there are question marks (aka 'placeholders')
    	// Only testing the INSERT statement, because it will be the exact same for UPDATE and DELETE
    	$this->expectException(PDOException::class);
    	$db_connect->change('INSERT INTO table_1 VALUES (3, ?)', []);

    }

    // Testing valid INSERT/UPDATE/DELETE statements 
    public function testValidRead() : void {

    	$db_connect = new Crud();

    	// Running a select statement on one row with no placeholders
    	$table1FirstName = $db_connect->read('SELECT name FROM table_1 WHERE id=1', []);
    	$this->assertEquals('Adam McGurk', $table1FirstName[0]['name']);

    	// Running a select statement on one row with placeholders
    	$table1FirstName = $db_connect->read('SELECT name FROM table_1 WHERE id=?', [1]);
    	$this->assertEquals('Adam McGurk', $table1FirstName[0]['name']);

    	// Running a select statement on multiple rows with no placeholders
    	$table1FirstName = $db_connect->read('SELECT name FROM table_1', []);
    	$this->assertEquals('Adam McGurk', $table1FirstName[0]['name']);
    	$this->assertEquals('Megan Laub', $table1FirstName[1]['name']);
    	$this->assertEquals('Sam Anderson', $table1FirstName[2]['name']);

    	// Running a select statement on multiple rows with no placeholders
    	$table1FirstName = $db_connect->read('SELECT name FROM table_1 WHERE id BETWEEN ? AND ?', [1,3]);
    	$this->assertEquals('Adam McGurk', $table1FirstName[0]['name']);
    	$this->assertEquals('Megan Laub', $table1FirstName[1]['name']);
    	$this->assertEquals('Sam Anderson', $table1FirstName[2]['name']);

    	// Running an empty select statement (with placeholders and no placeholders) so we confirm it won't throw an exception
    	$emptyReturn = $db_connect->read('SELECT name FROM table_1 WHERE id=1209384912342084', []);
    	$this->assertEmpty($emptyReturn);
    	$emptyReturn = $db_connect->read('SELECT name FROM table_1 WHERE id=?', [1209384912342084]);
    	$this->assertEmpty($emptyReturn);

    }

    // Testing invalid SELECT statement no bound parameters, but placeholders passed
    public function testInvalidReadNoParametersBound() : void {

    	$db_connect = new Crud();

    	// Testing SELECT statement with no placeholder values, but there are question marks (aka 'placeholders')
    	$this->expectException(PDOException::class);
    	$db_connect->change('SELECT * FROM table_1 WHERE id=?', []);

    }

    // Testing table name sanitization
    public function testValidSanitizingTableNames() : void {

    	// With whitelist
    	$this->assertEquals('table_1', Crud::sanitize_mysql('table_1', ['table_1', 'table_2']));

    	// No whitelist with no backticks
    	$this->assertEquals('`table_1`', Crud::sanitize_mysql('table_1'));

    	// No whitelist with backticks 
    	$this->assertEquals('```table_1```', Crud::sanitize_mysql('`table_1`'));
    }

    // Making sure non existent table names throw an exception
    public function testInvalidSanitizingTableNames() : void {

    	// Testing a table name that doesn't exist in the whitelist
    	$this->expectException(CrudException::class);
    	Crud::sanitize_mysql('table_1237812031', ['table_1', 'table_2']);

    }

}
