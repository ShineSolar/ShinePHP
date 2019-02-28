# ShinePHP
This is a very simple PHP library that allows developers to SHINE!

## Class Features
- Secure by default CRUD (Create, Read, Update, Delete) database interactions
- Method to sanitize table or column names (MySQL/MariaDB only right now, more RDBMS' on the way soon!!!), which PDO does not do
- Ability to easily sanitize and validate phone numbers, email addresses, ip addresses, urls, strings, floats, integers, and booleans
- Secure by default HTTP methods, and several other methods for HTTP request validation
- Strictly Typed
- Protects against SQL injection automatically
- Namespaced to prevent name clashes
- And more!

## Why You Might Need It

Shine PHP is for the developer with general PHP needs. If you need to give CRUD (Create, Read, Update, and Delete) functionality to your user base, and don't want to worry about the potential SQL injection vulnerabilities, our Crud() class is perfect for that, using the native PHP PDO plugins. 

If you need an easy, secure by default way of validating and sanitizing data, we have eight specifc static class methods for doing just that (including methods to work with email addresses, urls, IP addresses, and United States phone numbers, among others) and we are adding more continously. 

If you need a secure way of requesting HTTP endpoints, our EasyHttp class uses the secure-by-default native PHP cURL functions (while also giving you methods to verify the user navigating to your URLs is the user you want there). 

## What is Shine PHP not?
Shine PHP is not a feature-rich framework, it's not meant to be and we have no immediate intention of turning it into a framework. It's a library that is designed to make database transactions, handling data, and HTTP requesting easy. 

Shine PHP never intends to be backwards compatible with anything below PHP 7. The whole idea of this framework is to force secure PHP programming. 

And finally, Shine PHP is not meant to be used for developers with very specific needs. If you're starting up just a small blog or a simple web application, Shine PHP will probably be exactly what you need and want. However, if you have very specific database needs in particular, Shine PHP is not for you. Or if you're in a country other than the United States, the methods in HandleData don't work with non-United States phone numbers.

Pretty much, unless you're in the 5% of PHP developers in the world, Shine PHP will fit your needs *at least* somewhere.

## Requirements

- Minimum PHP version of PHP 7.0.0
- The only database driver that is supported right now is MySQL/MariaDB, so the MySQL PDO driver 
- The cURL driver for PHP
- The PCRE driver for PHP

## Installation and Execution

ShinePHP is available on Packagist (using semantic versioning), and installation via Composer is the recommended way to install ShinePHP. Just add this line to your composer.json file:

```json
"adammcgurk/shine-php": "^0.0.3"
```

or run:

```sh
composer require adammcgurk/shine-php
```

And right now, until the work being done in the automate_sql_details branch is finished, you need to go into the Crud class in the vendor/adammcgurk/shine-php/src/ShinePHP/ directory and input your database credentials on lines 46, 51, and 52 (The database name, MySQL username, and MySQL password respectively) to make sure the Crud class actually works. And the permissions given to your MySQL client need to be restricted to the localhost server, and given to no others.

Here is how you include the library:

```php
<?php
declare(strict_types=1);

require_once 'path/to/vendor/autoload.php';
use ShinePHP\{Crud, CrudException, HandleData, HandleDataException, EasyHttp, EasyHttpException};

// Put the rest of your code here

```

# Class and Method documentation

## Crud

### Method Signatures and examples

#### readFromDatabase(string $statement, array $values = []) : array

The readFromDatabase class method is used for SELECT SQL statement execution. You supply a SQL statement in the first parameter (with placeholders in the form of a question mark '?' representing your input data), your inputs in the second paramter (empty by default), and you receive a multi-dimensional array back.

##### readFromDatabase() Example 1:

Databse Schema:
```sql
CREATE TABLE table (id int(3), name varchar(100));

INSERT INTO table VALUES (123,'Adam McGurk');
INSERT INTO table VALUES (321,'Joe Bob');
INSERT INTO table VALUES (231,'Jane Doe');
```

PHP Code:
```php
$id = 123;
$sql = 'SELECT name FROM table WHERE id = ?';
try {
	$Crud = new Crud();
	$response = $Crud->readFromDatabase($sql,[$id]);
} catch (CrudException $cex) {
	echo "Uh oh! There's a problem! Crud probably doesn't have the right MySQL user details, but let's check the error message here: ".$cex->getMessage();
} catch (PDOException $pex) {
	echo "Uh oh! There's actually a problem with the SQL itself most likely, but let's check the error message: ".$pex->getMessage();
}
var_dump($response);
```

Outputs:
```php
array(1) {
  [0]=>
  array(1) {
    ["name"]=>
    string(11) "Adam McGurk"
  }
}
```

You access the name like this:
```php
$response[0]['name'];
```

Which represents:
```php
"Adam McGurk"
```


#### makeChangeToDatabase(string $statement, array $values = [], int $rowsAffected = 0) : void

The makeChangeToDatabase class method is used for INSERT, UPDATE, or DELETE SQL statement execution. You supply a SQL statement in the first parameter (with placeholders in the form of a question mark '?' representing your input data), your inputs in the second paramter (empty by default), and you can optionally supply an integer representing the number of rows you expect to be affected. If this is zero, the feature is turned off. This is the default.

##### makeChangeToDatabase() example #1:

Databse Schema:
```sql
CREATE TABLE table (id int(3), name varchar(100));

INSERT INTO table VALUES (123,'Adam McGurk');
INSERT INTO table VALUES (321,'Joe Bob');
INSERT INTO table VALUES (231,'Jane Doe');
```

PHP Code:
```php
$id = 123;
$sql = 'DELETE FROM table WHERE id = ?';
try {
	$Crud = new Crud();
	$response = $Crud->makeChangeToDatabase($sql,[$id]);
} catch (CrudException $cex) {
	echo "Uh oh! There's a problem! Crud probably doesn't have the right MySQL user details, or an incorrect number of rows were affected, but let's check the error message here: ".$cex->getMessage();
} catch (PDOException $pex) {
	echo "Uh oh! There's actually a problem with the SQL itself most likely, but let's check the error message: ".$pex->getMessage();
}
```

Which deletes the record with an ID matching 123.

##### makeChangeToDatabase() example #2 (showing the rowsAffected feature):

Databse Schema:
```sql
CREATE TABLE table (id int(3), name varchar(100));

INSERT INTO table VALUES (123,'Adam McGurk');
INSERT INTO table VALUES (321,'Joe Bob');
INSERT INTO table VALUES (231,'Jane Doe');
```

PHP Code:
```php
$id = 39023812983;
$sql = 'DELETE FROM table WHERE id = ?';
try {
	$Crud = new Crud();
	$response = $Crud->makeChangeToDatabase($sql,[$id], 1);
} catch (CrudException $cex) {
	echo "Uh oh! There's a problem! Crud probably doesn't have the right MySQL user details, or an incorrect number of rows were affected, but let's check the error message here: ".$cex->getMessage();
} catch (PDOException $pex) {
	echo "Uh oh! There's actually a problem with the SQL itself most likely, but let's check the error message: ".$pex->getMessage();
}
```

Outputs:
```php
Uh oh! There's a problem! Crud probably doesn't have the right MySQL user details, or an incorrect number of rows were affected, but let's check the error message here: Incorrect number of rows affected. The expected number of rows to be affected is 1 and 0 were affected.
```
