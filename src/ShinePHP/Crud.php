<?php
declare(strict_types=1);

namespace ShinePHP;

/**
 * CRUD is a class to make a cleaner, simpler interface for working with PDO objects 
 * CRUD is an interface built for PHP developers to reduce all of the code repeating the PDO requires
 * CRUD also helps you organize your database connections in a more secure, performant way, with 
 * syntactical naming and safe by default queries WITH table sanitization built in (unlike vanilla PDO)
 * 
 * EXAMPLE USAGE:
 * $pdo = new Crud();
 * $dbReturn = $pdo->readFromDatabase('SELECT * FROM users WHERE id = ?', [1]);
 * $user = $dbReturn[0];
 *
 * @package CRUD
 * @author Adam McGurk <amcgurk@shinesolar.com>
 * @access public
 * @see https://github.com/ShineSolar/ShinePHP
 * 
 */

final class Crud {

	/** 
	 *  @access private
	 *	@var object This is the actual database connection object returned by pdo. Used in all four CRUD public functions 
	 */
	private $pdo;

	/**
	 *
	 * @access public 
	 * Opens the initial database connection. 
	 * THIS SHOULD ONLY BE INITAILIZED ONCE PER SCRIPT!!! You will have perf issues otherwise
	 *
	 * @param OPTIONAL bool $developmentMode Pass true to this when you're developing the actual class in ANY OTHER CIRCUMSTANCE leave blank
	 * 
	 * @throws CrudException when the database login details ($dbname, $username, $password, etc...) remain the same as the defaults
	 *
	 */

	public function __construct(bool $developmentMode = false) {
		$server = '127.0.0.1'; // This one might not change
		$dbname = 'crud_test'; // Change this to the name of the database you are working with
		if ($dbname === 'crud_test' && $developmentMode === false) {
			throw new CrudException('Database details not changed! Please go into the class file and change the login details to match your specific DB.');
		}
		$dsn = 'mysql:host='.$server.';dbname='.$dbname; // Right now we only support mysql/mariadb
		$username = 'your_mysql_client'; // Change this to the name of your mysql user
		$password = 'your_mysql_password'; // Change this to the password of your mysql user
		$options = array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC);
	    try {
	        $pdo = new \PDO($dsn, $username, $password, $options);
	        $this->pdo = $pdo;
	    } catch(\PDOException $ex) {
	    	throw new CrudException('Trying to link to your database failed. This is usually because you have a wrong username or password on your mysql client. Here is the error message so you can do more digging! '.$ex);
	    }
	}

	/**
	 *
	 * Runs a PDO MySQL INSERT statement
	 *
	 * @access public
	 *
	 * @param string $statement the correctly formed SQL statement
	 * @param array $values the values to replace the SQL placeholders
	 * @param OPTIONAL string $table if you need to sanitize a dynamic table, pass a table.
	 * @param OPTIONAL array $tableWhiteList if you want a whitelist to validate the dynamic table name THIS IS THE MOST SECURE OPTION.
	 * @param OPTIONAL int $rowsReturned if you know how many rows your statement INSERT, UPDATE, or DELETE is supposed to affect, pass an integer with that number
	 *
	 * @throws CrudException when number of rows returned does not equal the rowCount of the statement result
	 * @throws PDOException when statement is incorrectly formed OR statement is rejected by the database
	 * @throws InvalidArgumentException when any of the parameters are passed with the incorrect type
	 * 
	 * @return void
	 *
	 */

	public function change(string $statement, array $values = []) : array {

		// Checking if placeholder values exist, if not, a simple query will suffice
		if (empty($values) && !strpos($statement, '?')) {

			// Running the statement and getting the row count
			$stmt = $this->pdo->query($statement);

			// returning the most recent id inserted and getting the amount of rows affected
			return [
				'last_insert_id' => $this->pdo->lastInsertId(),
				'row_count' => $stmt->rowCount()
			];

		} else {

			// Running the statement and getting the row count
			$stmt = $this->pdo->prepare($statement);
			$stmt->execute($values);

			// returning the most recent id inserted and getting the amount of rows affected
			return [
				'last_insert_id' => $this->pdo->lastInsertId(),
				'row_count' => $stmt->rowCount()
			];

		}

	}

	/**
	 *
	 * Runs a PDO MySQL SELECT statement
	 *
	 * @access public
	 *
	 * @param string $statement the correctly formed SQL statement
	 * @param array $values the values to replace the SQL placeholders
	 *
	 * @throws PDOException when statement is incorrectly formed OR statement is rejected by the database
	 * @throws InvalidArgumentException when any of the parameters are passed with the incorrect type
	 * 
	 * @return array
	 *
	 */

	public function read(string $statement, array $values = []) : array {

		// Checking if placeholder values exist, if not, a simple query will suffice
		if (empty($values) && !strpos($statement, '?')) {

			// Running the statement and returning the return (no throwing exception on empty return)
			$stmt = $this->pdo->query($statement);
			return $stmt->fetchAll();

		} else {

			// Running the statement and returning the return (no throwing exception on empty return)
			$stmt = $this->pdo->prepare($statement);
			$stmt->execute($values);
			return $stmt->fetchAll();
		}

	}

	/**
	 *
	 * Sanitizes a dynamic table name
	 *
	 * @access public
	 *
	 * @param string $name this is the name you want to sanitize
	 * @param OPTIONAL array $whiteList if you want a whitelist to validate the dynamic name THIS IS THE MOST SECURE OPTION.
	 *
	 * @throws CrudException when name does not exist in whitelist
	 * @throws InvalidArgumentException when any of the parameters are passed with the incorrect type
	 * 
	 * @return string
	 *
	 */

	public static function sanitize_mysql(string $name, array $whiteList = []) : string {
		if (!empty($whiteList) && array_search($name, $whiteList) === false) {
			throw new CrudException('Value does not exist in value whitelist.');
		} else if (!empty($whiteList) && array_search($name, $whiteList) !== false) {
			return $name;
		} else {
			return '`'.str_replace('`','``',$name).'`';
		}
	}

}

// Custom Exception class. We don't need any more functionality other than the built in Exception class
final class CrudException extends \Exception {}
