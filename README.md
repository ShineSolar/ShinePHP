# ShinePHP

This is a very simple PHP library that allows developers to SHINE!

### GO TO:


## Class Features
- Secure by default CRUD (Create, Read, Update, Delete) database interactions
- Method to sanitize table or column names (MySQL/MariaDB only right now, more RDBMS' on the way soon!!!), which PDO does not do
- Ability to easily sanitize and validate phone numbers, email addresses, ip addresses, urls, strings, floats, integers, and booleans
- Secure by default HTTP methods, and several other methods for HTTP request validation
- Strongly typed methods
- Protects against SQL injection automatically
- Namespaced to prevent name clashes
- And more!

## Why You Might Need It

Shine PHP is for the developer with general PHP needs. If you need to give CRUD (Create, Read, Update, and Delete) functionality to your user base, and don't want to worry about the potential SQL injection vulnerabilities, our Crud() class is perfect for that, using the native PHP PDO plugins. 

If you need an easy, secure by default way of validating and sanitizing data, we have eight specifc static class methods for doing just that (including methods to work with email addresses, urls, IP addresses, and United States phone numbers, among others) and we are adding more continously. 

If you need a secure way of requesting HTTP endpoints, our EasyHttp class uses the secure-by-default native PHP cURL functions (while also giving you methods to verify the user navigating to your URLs is the user you want there). 

## What is Shine PHP not?
Shine PHP is not a feature-rich framework, it's not meant to be and we have no immediate intention of turning it into a framework. It's a library that is designed to make database transactions, handling data, and HTTP requesting easy. 

Shine PHP never intends to be backwards compatible with anything below PHP 7. The whole idea of this framework is to force secure and safe PHP programming. 

And finally, Shine PHP is not meant to be used for developers with very specific needs. If you're starting up just a small blog or a simple web application, Shine PHP will probably be exactly what you need and want. However, if you have very specific database needs in particular, Shine PHP is not for you. Or if you're in a country other than the United States, the methods in HandleData don't work with non-United States phone numbers.

Pretty much, unless you're in the 5% of PHP developers in the world, Shine PHP will fit your needs *at least* somewhere.

## Requirements

- Minimum PHP version of PHP 7.0.0
- The cURL driver for PHP
- The PCRE driver for PHP

## Installation and Execution

ShinePHP is available on Packagist (using semantic versioning), and installation via Composer is the recommended way to install ShinePHP. Just add this line to your composer.json file:

```json
"adammcgurk/shine-php": "^1.0.0"
```

or run:

```sh
composer require adammcgurk/shine-php
```

Here is how you include the library:

```php
<?php
declare(strict_types=1);

require_once 'path/to/vendor/autoload.php';
use ShinePHP\{Crud, CrudException, HandleData, EasyHttp};

// Put the rest of your code here

```

# Class and Method documentation

## Crud

Crud is a pretty simple class that allows you to interact with the database. It has no limitation on what RDMBS's it can interact with (it's table/column sanitization, however, has a few limitations) and you can declare the credentials via environment variables (MOST SECURE) or a config file. Here is how you do either:

### Environment Variable Configuration:

