<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// Remember, requires are from the root in tests
require 'src/ShinePHP/Database/Crud.php';
use ShinePHP\Database\{Crud, CrudException};

final class CrudTest extends TestCase {
    public function testIni(): void {
        Crud::get_from_ini_file('Tests/Unit/Database/test.ini');
    }
}
