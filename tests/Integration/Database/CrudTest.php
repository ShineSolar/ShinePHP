<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Remember, requires are from the root in tests
require 'src/ShinePHP/Database/Crud.php';
use ShinePHP\Database\{Crud, CrudException};

final class CrudTest extends TestCase {

	public function testIniNotExists(): void {
		$this->expectException(CrudException::class);
		Crud::get_from_ini_file('../../not-existing.ini');
	}

	public function testNotValidIniFile(): void {
		$this->expectException(CrudException::class);
		Crud::get_from_ini_file('Tests/Integration/Database/test_files/test.txt');
	}

	public function testIniNotEnoughValues(): void {
		$this->expectException(CrudException::class);
		Crud::get_from_ini_file('Tests/Integration/Database/test_files/test_invalid.ini');
	}

    public function testIni(): void {
        $db_details = Crud::get_from_ini_file('Tests/Integration/Database/test_files/test_valid.ini');
        $this->assertEquals('your_mysql_client', $db_details['username']);
    }

}
