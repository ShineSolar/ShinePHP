<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Remember, requires are from the root in tests
require 'src/ShinePHP/Crud.php';
use ShinePHP\Crud;
use ShinePHP\CrudException;

final class CrudTest extends TestCase {

	// Testing a valid class init
	public function testCanBeCreatedWithDatabaseCredentials() : void {
        $this->assertInstanceOf(
            Crud::class,
            new Crud(true)
        );
    }

    // Testing an invalid class init
    public function testCannotBeCreatedWithDefaultDatabaseCredentials() : void {
    	$this->expectException(CrudException::class);
    	new Crud();
    }

    // Testing valid INSERT/UPDATE/DELETE statements 
    public function testValidMakeChangeToDatabase() : void {

    	$pdo = new Crud(true);

    	// Testing INSERT/UPDATE/DELETE statement with no placeholder values or rowCount
    	$this->assertNull($pdo->makeChangeToDatabase('INSERT INTO table_1 VALUES (3, "Stephaine Laub")', []));
    	$this->assertNull($pdo->makeChangeToDatabase('UPDATE table_1 SET name="Stephaine Chandler" WHERE name="Stephaine Laub"', []));
    	$this->assertNull($pdo->makeChangeToDatabase('DELETE FROM table_1 WHERE name="Stephaine Chandler"', []));

    	// Testing INSERT/UPDATE/DELETE statement with placeholder values, but no rowCount
    	$this->assertNull($pdo->makeChangeToDatabase('INSERT INTO table_1 VALUES (3, ?)', ['Brian Laub']));
    	$this->assertNull($pdo->makeChangeToDatabase('UPDATE table_1 SET name="Chandler" WHERE name=?', ['Brian Laub']));
    	$this->assertNull($pdo->makeChangeToDatabase('DELETE FROM table_1 WHERE name=?', ['Chandler']));

    	// Testing INSERT/UPDATE/DELETE statement with placeholder values and rowCount
    	$this->assertNull($pdo->makeChangeToDatabase('INSERT INTO table_1 VALUES (3, ?)', ['Ryan Andersen'], 1));
    	$this->assertNull($pdo->makeChangeToDatabase('UPDATE table_1 SET name="Andersen" WHERE name=?', ['Ryan Andersen'], 1));
    	$this->assertNull($pdo->makeChangeToDatabase('DELETE FROM table_1 WHERE name=?', ['Andersen'], 1));

    }

    // Testing invalid INSERT/UPDATE/DELETE statement no bound parameters, but placeholders passed
    public function testInValidMakeChangeToDatabaseNoParametersBound() : void {

    	$pdo = new Crud(true);

    	// Testing INSERT statement with no placeholder values, but there are question marks (aka 'placeholders')
    	// Only testing the INSERT statement, because it will be the exact same for UPDATE and DELETE
    	$this->expectException(PDOException::class);
    	$pdo->makeChangeToDatabase('INSERT INTO table_1 VALUES (3, ?)', []);

    }

    // Testing invalid INSERT/UPDATE/DELETE statement, incorrect row return
    public function testInValidMakeChangeToDatabaseIncorrectRowReturn() : void {

    	$pdo = new Crud(true);

    	// Testing DELETE statement with no WHERE clause, so it DELETEs the whole table.
    	// We are only expecting 1 row to be effected, but there are either 1. more rows present OR 2. no rows present, so either way, it should throw exception
    	// Only testing the DELETE statement, because it will be the exact same for UPDATE and INSERT
    	$this->expectException(CrudException::class);
    	$pdo->makeChangeToDatabase('DELETE FROM table_2', [], 1);

    }

    // Testing valid INSERT/UPDATE/DELETE statements 
    public function testValidReadFromDatabase() : void {

    	$pdo = new Crud(true);

    	// Running a select statement on one row with no placeholders
    	$table1FirstName = $pdo->readFromDatabase('SELECT name FROM table_1 WHERE id=1', []);
    	$this->assertEquals('Adam McGurk', $table1FirstName[0]['name']);

    	// Running a select statement on one row with placeholders
    	$table1FirstName = $pdo->readFromDatabase('SELECT name FROM table_1 WHERE id=?', [1]);
    	$this->assertEquals('Adam McGurk', $table1FirstName[0]['name']);

    }

}