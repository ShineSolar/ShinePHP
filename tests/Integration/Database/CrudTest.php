<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Remember, requires are from the root in tests
require_once 'src/ShinePHP/Database/Crud.php';
use ShinePHP\Database\{Crud, CrudException};

final class CrudIntegrationTest extends TestCase {

	public function testEnvNotEnoughValues(): void {
		$this->expectException(CrudException::class);
		putenv('DB_NAME=test_db');
		Crud::get_from_environment();
	}

	public function testEnv(): void {
		putenv('DB_NAME=test_db');
		putenv('DB_USERNAME=your_mysql_client');
		putenv('DB_PASSWORD=your_mysql_password');
		$env_variables = Crud::get_from_environment();
		$this->assertEquals('your_mysql_client', $env_variables['username']);
	}

	public function testIniNotExists(): void {
		$this->expectException(CrudException::class);
		Crud::get_from_ini_file('../../not-existing.ini');
	}

	public function testNotValidIniFile(): void {
		$this->expectException(CrudException::class);
		Crud::get_from_ini_file('tests/Integration/Database/test_files/test.txt');
	}

	public function testIniNotEnoughValues(): void {
		$this->expectException(CrudException::class);
		Crud::get_from_ini_file('tests/Integration/Database/test_files/test_invalid.ini');
	}

    public function testIni(): void {
        $db_details = Crud::get_from_ini_file('tests/Integration/Database/test_files/test_valid.ini');
        $this->assertEquals('your_mysql_client', $db_details['username']);
    }

}
