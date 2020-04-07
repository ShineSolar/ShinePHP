<?php
declare(strict_types=1);

namespace ShinePHP\Database;

/**
 * CRUD is a class to make a cleaner, simpler interface for working with PDO objects 
 * CRUD is an interface built for PHP developers to reduce all of the code repeating the PDO requires
 * CRUD also helps you organize your database connections in a more secure, performant way, with 
 * syntactical naming and safe by default queries WITH table sanitization built in (unlike vanilla PDO)
 * 
 * EXAMPLE USAGE:
 * $db_connection = new Crud();
 * $db_return = $db_connection->read('SELECT * FROM users WHERE id = ?', [1]);
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
	 *
	 * @param OPTIONAL bool $in_environment_variables if the database login details 
	 * @param OPTIONAL string $path_to_ini_file the file path to the ini file where the db login details are held if you are choosing to place your login parameters in an ini file
	 *
	 * Opens the initial database connection. 
	 * THIS SHOULD ONLY BE INITAILIZED ONCE PER SCRIPT!!! You will have performance issues otherwise
	 * 
	 * @throws CrudException when the there is a database failure to login
	 *
	 */

	public function __construct(bool $persistent_connections = true, bool $in_environment_variables = true, string $path_to_ini_file = '') {

		// getting the details from the right spot
		$db_login_details = ($in_environment_variables ? self::get_from_environment() : self::get_from_ini_file($path_to_ini_file));

		// PDO configs
		$pdo_configs = array(
	        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, 
	        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
	    );

	    if ($persistent_connections) $pdo_configs[\PDO::ATTR_PERSISTENT] = true;

		// setting up the actual connection with the DSN, username, password, and PDO options
	    try {
	        $pdo = new \PDO($db_login_details['dsn'], $db_login_details['username'], $db_login_details['password'], $pdo_configs);
	    } catch(\PDOException $ex) {
	    	throw new CrudException('Trying to link to your database failed. This is usually because you have a wrong username or password on your mysql client. Here is the error message so you can do more digging! '.$ex);
	    }

	    // setting the PDO object for private use in the class
	    $this->pdo = $pdo;

	}

	/**
	 *
	 * @access public
	 *
	 * Getting the database connection details from the environment. THIS IS THE MOST SECURE OPTION
	 * This is most secure because they are not saved in a place accessible to just anyone, like a configuration file is. This does require you to have access to set environment variables on the machine(s) though
	 *
	 * @throws CrudException when the required fields (DB_NAME, DB_USERNAME, DB_PASSWORD) are not populated in the environment
	 *
	 * @return array
	 *   string dsn - The DSN connect variable for the database
	 *   string username - The username for the database user
	 *   string password - The password for the database user
	 *
	 */

	public static function get_from_environment(): array {

		// doing the check to see if the bare necessities exist in the environment
		if (!\getenv('DB_NAME') || !\getenv('DB_USERNAME') || !\getenv('DB_PASSWORD')) {
			throw new CrudException('Database details not set in the environment. Please set your database name, username, and password in your environment variables. Or, if you prefer, use an ini file and pass the correct parameters to the constructor');
		}

		// doing the server check, becaues it's just a little too long for use in the ternary statement down there
		$server = (\getenv('DB_SERVER') ? \getenv('DB_SERVER') : '127.0.0.1');

		// returning the database connection details
		return array(
			'dsn' => (\getenv('DB_DSN') ? \getenv('DB_DSN') : 'mysql:host='.$server.';dbname='.\getenv('DB_NAME')),
			'username' => \getenv('DB_USERNAME'),
			'password' => \getenv('DB_PASSWORD')
		);
	}

	/**
	 *
	 * @access public
	 *
	 * @param string $path - the path to the ini file that is to be parsed
	 *
	 * Getting the database connection details from a predifined .ini file
	 *
	 * @throws CrudException when the required fields (DB_NAME, DB_USERNAME, DB_PASSWORD) are not populated in the .ini file
	 *
	 * @return array
	 *   string dsn - The DSN connect variable for the database
	 *   string username - The username for the database user
	 *   string password - The password for the database user
	 *
	 */

	public static function get_from_ini_file(string $path): array {

		// making sure the file path actually exits
		if (!\file_exists($path)) {
			throw new CrudException('Sorry, the file: '.$path.' was not found. The database could not be initialized');
		}

		// parsing the file and making sure it's a valid ini file
		$parsed_ini_file = \parse_ini_file($path, false, INI_SCANNER_RAW);
		if ($parsed_ini_file === FALSE) {
			throw new CrudException('Sorry, the file passed is not a valid .ini file.');
		}

		// making sure the variables that we need are set in the ini file
		if (!isset($parsed_ini_file['DB_NAME']) || !isset($parsed_ini_file['DB_USERNAME']) || !isset($parsed_ini_file['DB_PASSWORD'])) {
			throw new CrudException('Database details not set correctly in the ini file. Please set your database name, username, and password in your ini file as shown in the documentation. Or, if you prefer, set them in your environment variables.');
		}

		// doing the server check, becaues it's just a little too long for use in the ternary statement down there
		$server = (isset($parsed_ini_file['DB_SERVER']) ? $parsed_ini_file['DB_SERVER'] : '127.0.0.1');

		// returning the database connection details
		return array(
			'dsn' => (isset($parsed_ini_file['DB_DSN']) ? $parsed_ini_file['DB_DSN'] : 'mysql:host='.$server.';dbname='.$parsed_ini_file['DB_NAME']),
			'username' => $parsed_ini_file['DB_USERNAME'],
			'password' => $parsed_ini_file['DB_PASSWORD']
		);

	}

	/**
	 *
	 * Runs a PDO MySQL INSERT, UPDATE, or DELETE statement
	 * We also wrap running the actual query in a Try/Catch for transactional reasons
	 *
	 * @access public
	 *
	 * @param string $statement the correctly formed SQL statement
	 * @param OPTIONAL array $values the values to replace the SQL placeholders
	 * 
	 * @return array 
	 *	?string last_insert_id - Contains the ID of the last inserted row. If no row was inserted, it's null
	 *  int row_count - The number of rows affected by the query
	 *
	 */

	public function change(string $statement, array $values = array()) : array {

		// running a transaction
		try {
			
			$this->pdo->beginTransaction();
			$stmt = self::run_query($this->pdo, $statement, $values);
		
			// getting the most recent id inserted and getting the amount of rows affected
			$db_return = array(
				'last_insert_id' => $this->pdo->lastInsertId(),
				'row_count' => $stmt->rowCount()
			);

			$this->pdo->commit();

		} catch (\Exception|\PDOException $pex) {
			$this->pdo->rollBack();
			throw new CrudException('There has been a problem with the database transaction. It has been rolled back. Here is the error message: '.$pex->getMessage());
		}

		// closing the cursor for perf reasons
		$stmt->closeCursor();
		$stmt = null;

		// returning the change data
		return $db_return;

	}

	/**
	 *
	 * Runs a PDO MySQL SELECT statement
	 *
	 * @access public
	 *
	 * @param string $statement the correctly formed SQL statement
	 * @param OPTIONAL array $values the values to replace the SQL placeholders DEFAULTS TO EMPTY
	 * @param OPTIONAL boolean $return_single_level A flag to say if only one row is returned, only return the row, not an assoc arrary CONTAINING that row
	 * 
	 * @return array of rows.
	 *
	 * If nothing is fetched from the SQL statement, an empty array is returned
	 * Will always be a multi dimensional array (unless nothing is fetched, then again, it is just empty), so even if you wrote a SELECT * FROM ... LIMIT 1, you must still access it like this:
	 * $my_return = $Crud->read('SELECT * FROM table LIMIT 1');
	 * var_dump($my_return[0]);
	 *
	 */

	public function read(string $statement, array $values = array(), bool $return_single_level = false) : array {

		// running the query
		$stmt = self::run_query($this->pdo, $statement, $values);

		// getting the rows from the database
		$db_return = $stmt->fetchAll();

		// closing the cursor for perf reasons
		$stmt->closeCursor();
		$stmt = null;

		// This checks if there was only one row returned
		// AND IF the return single level flag was set as true, it just returns that one row, as opposed to an assoc array only CONTAINING that one row
		// I did this because sometimes it would be nice if we could access the return like this:
		// $Crud = new Crud();
		// $return = $Crud->read('SELECT name FROM users WHERE id = ?', array('1'));
		// echo $return['name'] -> "Adam"
		if (\count($db_return) === 1 && $return_single_level === true) {
			return $db_return[0];
		}

		// returning the SELECT'd data
		return $db_return;

	}

	public function kill(): void {
		$this->pdo = null;
	}

	/**
	 *
	 * Does the actual running of the SQL query
	 *
	 * @access private
	 *
	 * @param PDO $pdo - the PDO connection object that was set in the constructor
	 * @param string $statement - the SQL statement to run
	 * @param array $values - the values to replace the placeholders. This could be empty
	 *
	 * Both the change and read code is exactly the same, so this statically does the running of the SQL for both the read and change methods
	 *
	 * @return PDOStatement
	 *
	 */

	private static function run_query(\PDO $pdo, string $statement, array $values): \PDOStatement {

		// Checking if placeholder values exist, if not, a simple query will suffice
		if (empty($values) && !\strpos($statement, '?')) {

			// Running the statement and returning the return (no throwing exception on empty return)
			$stmt = $pdo->query($statement);

		} else {

			// Running the statement and returning the return (no throwing exception on empty return)
			$stmt = $pdo->prepare($statement);
			$stmt->execute($values);

		}

		// returning the statement
		return $stmt;

	}

	/**
	 *
	 * Sanitizes a dynamic table or column name
	 *
	 * @access public
	 *
	 * @param string $name this is the name you want to sanitize
	 * @param OPTIONAL array $white_list if you want a whitelist to validate the dynamic name THIS IS THE MOST SECURE OPTION.
	 *
	 * @throws CrudException when name does not exist in whitelist
	 * 
	 * @return string of the sanitized name
	 *
	 */

	public static function sanitize_mysql(string $name, array $white_list = array()) : string {

		$searched_for_value = \array_search($name, $white_list);

		// Checking the name whitelist, throwing exception if name is not in whitelist
		if (!empty($white_list) && $searched_for_value === false) {
			throw new CrudException('Value does not exist in value whitelist.');
		} 

		// returning the name if it passes the whitelist
		else if (!empty($white_list) && $searched_for_value !== false) { return $white_list[$searched_for_value]; } 

		// sanitizes a name for use in the database
		else { return '`'.\str_replace('`','``',$name).'`'; }

	}

}

// Custom Exception class. We don't need any more functionality other than the built in Exception class
final class CrudException extends \Exception {}
