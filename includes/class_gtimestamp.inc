<?php

class GTimeStamp extends GField
 {
 	/**
 	* This is the constructor required to create an object.
 	* It has to be called in every child class.
 	*/
	public function __construct($table, $name, $size, $nullable, $default, $dependantKey = NULL, $dependantTable = NULL, $dependantTableAlias = NULL)
	{
 		parent::__construct($table, $name, $size, $nullable, $default, $dependantKey, $dependantTable, $dependantTableAlias);
 	}

	/**
	 * Test if the value is valid with the type of this field.
	 * @param	string	$val
	 * @return	boolean	TRUE if $val is a valid value
	 */
	public function isValid($val)
	{
		/**
		 * Format : YYYYMMDDHHMMSS
		 */

		return ((is_null($val) && $this->nullable) ||
			    (!is_null($val) &&
			    (strlen($val) == 14) &&
				(checkdate(substr($val, 4, 2), substr($val, 6, 2), substr($val, 0, 4))) &&
				(substr(8,2) >= 0) && (substr(8,2) < 24) &&
				(substr(10,2) >= 0) && (substr(10,2) < 60) &&
				(substr(12,2) >= 0) && (substr(12,2) < 60)));
	}
 }

?>