<?php

class GEmail extends GString
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
		if (!is_null($val)) {
			$email = trim($val);
			$valid = (preg_match("/^.+@(([0-9a-zA-Z-]+\.)+[a-zA-Z]{2,15})$/", $email));
		} else {
			$email = $val;
		}
		return ((is_null($val) && $this->nullable) || (!is_null($val) && $valid));
	}
 }

?>