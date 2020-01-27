<?php

 class GDate extends GField
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
		 * Format : YYYY-MM-DD
		 */
		return ((is_null($val) && $this->nullable) ||
			    (!is_null($val) &&
			    (strlen($val) == 10) &&
				((checkdate(substr($val, 5, 2), substr($val, 8, 2), substr($val, 0, 4))) || ($val == '0000-00-00')) &&
				(($val[4] == '/') || ($val[4] == '-')) &&
				(($val[7] == '/') || ($val[7] == '-'))));
	}
 }

?>