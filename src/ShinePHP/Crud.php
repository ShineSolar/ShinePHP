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
 * $Crud = new Crud();
 * $db_return = $Crud->read('SELECT * FROM users WHERE id = ?', [1]);
 * $user = $dbReturn[0];
 *
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
	 * THIS SHOULD ONLY BE INITAILIZED ONCE PER SCRIPT!!! You will have performance issues otherwise
	 *
	 * @param OPTIONAL bool $development_mode Pass true to this when you're developing the actual class in ANY OTHER CIRCUMSTANCE leave blank
	 * 
	 * @throws CrudException when the database login details ($dbname, $username, $password, etc...) remain the same as the defaults OR there was a database failure to login
	 *
	 */

	public function __construct(bool $development_mode = false) {
		$server = (getenv('DB_SERVER') ? getenv('DB_SERVER') : '127.0.0.1');
		if (!getenv('DB_NAME') || !getenv('DB_USERNAME') || !getenv('DB_PASSWORD')) {
			throw new CrudException('Database details not set. Please set your database name, username, and password in your environment variables.');
		}
		$dbname = getenv('DB_NAME');
		$dsn = 'mysql:host='.$server.';dbname='.$dbname; // Right now we only support mysql/mariadb
		$username = getenv('DB_USERNAME');
		$password = getenv('DB_PASSWORD');
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
	 * @param OPTIONAL array $values the values to replace the SQL placeholders
	 * 
	 * @return array 
	 *	string|null last_insert_id - Contains the ID of the last inserted row. If no row was inserted, it's null
	 *  int row_count - The number of rows affected by the query
	 *
	 */

	public function change(string $statement, array $values = []) : array {

		// Checking if placeholder values exist, if not, a simple query will suffice
		if (empty($values) && !strpos($statement, '?')) {

			// Running the statement and getting the row count
			$stmt = $this->pdo->query($statement);

		} else {

			// Running the statement and getting the row count
			$stmt = $this->pdo->prepare($statement);
			$stmt->execute($values);

		}

		// returning the most recent id inserted and getting the amount of rows affected
		return array(
			'last_insert_id' => $this->pdo->lastInsertId(),
			'row_count' => $stmt->rowCount()
		);

	}

	/**
	 *
	 * Runs a PDO MySQL SELECT statement
	 *
	 * @access public
	 *
	 * @param string $statement the correctly formed SQL statement
	 * @param OPTIONAL array $values the values to replace the SQL placeholders
	 * 
	 * @return array of rows.
	 *
	 * If nothing is fetched from the SQL statement, an empty array is returned
	 * Will always be a multi dimensional array, so even if you wrote a SELECT * FROM ... LIMIT 1, you must still access it like this:
	 * $my_return = $Crud->read('SELECT * FROM table LIMIT 1');
	 * var_dump($my_return[0]);
	 *
	 */

	public function read(string $statement, array $values = []) : array {

		// Checking if placeholder values exist, if not, a simple query will suffice
		if (empty($values) && !strpos($statement, '?')) {

			// Running the statement and returning the return (no throwing exception on empty return)
			$stmt = $this->pdo->query($statement);

		} else {

			// Running the statement and returning the return (no throwing exception on empty return)
			$stmt = $this->pdo->prepare($statement);
			$stmt->execute($values);

		}

		return $stmt->fetchAll();

	}

	/**
	 *
	 * Sanitizes a dynamic table or column name
	 *
	 * @access public
	 *
	 * @param string $name this is the name you want to sanitize
	 * @param OPTIONAL array $whiteList if you want a whitelist to validate the dynamic name THIS IS THE MOST SECURE OPTION.
	 *
	 * @throws CrudException when name does not exist in whitelist
	 * 
	 * @return string of the sanitized name
	 *
	 */

	public static function sanitize_mysql(string $name, array $whiteList = []) : string {

		// Checking the name whitelist, throwing exception if name is not in whitelist
		if (!empty($whiteList) && array_search($name, $whiteList) === false) { throw new CrudException('Value does not exist in value whitelist.'); } 

		// returning the name if it passes the whitelist
		else if (!empty($whiteList) && array_search($name, $whiteList) !== false) { return $name; } 

		// sanitizes a name for use in the database
		else { return '`'.str_replace('`','``',$name).'`'; }

	}

}

// Custom Exception class. We don't need any more functionality other than the built in Exception class
final class CrudException extends \Exception {}
