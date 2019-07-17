<?php
declare(strict_types=1);

namespace ShinePHP;

/**
 * HandleData is a class to make a cleaner, simpler interface for working input data
 * HandleData is an interface built for PHP developers to reduce all of the code repeating in sanitizing and validating data
 *
 * @author Adam McGurk <amcgurk@shinesolar.com>
 * @access public
 * @see https://github.com/ShineSolar/ShinePHP
 * 
 */

final class HandleData {

	/**
	 *
	 * Sanitize and validate a United States Phone Number, optionally including the "1" area code
	 *
	 * @access public
	 *
	 * @param string $phone string you want validated as a phone number
	 * @param OPTIONAL bool $include_us_country_code decide if you want a leading one in it or not
	 * 
	 * @return mixed validated phone or false on failure
	 *
	 */

	public static function american_phone(string $phone, bool $include_us_country_code = false) {

		$stripped_phone = preg_replace('/[^0-9]/', '', self::string($phone, false));

		// checking to see if it just matches basic phone validation anyways
		if (preg_match('/^1?[2-9]{1}[0-9]{2}[0-9]{3}[0-9]{4}$/', $stripped_phone) !== 1) {

			// return false on failure
			return false;

		} else {

			// checking the country code flag
			if ($include_us_country_code) { return (substr($stripped_phone,0,1) === '1' ? $stripped_phone : '1'.$stripped_phone); } 
			else { return (substr($stripped_phone,0,1) === '1' ? substr($stripped_phone,1) : $stripped_phone); }

		}

	}

}
