<?php

 class GTime extends GField
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
		 * Format : HH:MM:SS
		 */
		return ((is_null($val) && $this->nullable) || (!is_null($val) && preg_match("/([0-9]{2}):([0-9]{2}):([0-9]{2})/", $val)));

		/*
		return ((is_null($val) && $this->nullable) ||
			    (!is_null($val) &&
			    (strlen($val) == 8) &&
				is_numeric(substr($val, 0, 2)) && is_numeric(substr($val, 0, 2)) && is_numeric(substr($val, 0, 2)) &&
				substr($val, 2, 1) == ':' && substr($val, 2, 1) == ':';
		*/
	}
 }

?>