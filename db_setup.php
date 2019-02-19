<?php

// Getting the database server
while ($confirmdbserver !== 'yes' && $confirmdbserver !== 'y') {

	$server = readline('Please enter your MySQL server domain name or IP Address (leave blank to accept 127.0.0.1 as your MySQL server): ');
	$server = (empty($server)) ? '127.0.0.1' : $server;
	$confirmdbserver = readline('Is "'.$server.'" correct? (yes or no): ');

}

// Getting the database name
while ($confirmdbname !== 'yes' && $confirmdbname !== 'y') {

	$dbname = readline('Please enter your MySQL database name: ');
	$confirmdbname = readline('Is "'.$dbname.'" correct? (yes or no): ');

}

// Getting the database user name
while ($confirmuser !== 'yes' && $confirmuser !== 'y') {

	$user = readline('Please enter your MySQL user name: ');
	$confirmuser = readline('Is "'.$user.'" correct? (yes or no): ');

}

// Getting the database user password
while ($confirmpassword !== 'yes' && $confirmpassword !== 'y') {

	$password = readline('Please enter your MySQL user password: ');
	$confirmpassword = readline('Is "'.$password.'" correct? (yes or no): ');

}

echo "Your databse stuff is this: \n";

echo "Server: $server \n";

echo "Database: $dbname \n";

echo "MySQL User name: $user \n";

echo "MySQL Password: $password \n";

// var_dump(file('src/ShinePHP/TestingCrud.php'));
