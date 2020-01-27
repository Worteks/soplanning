<?php

class GInteger extends GField
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
		return ((is_null($val) && $this->nullable) || 
			    (!is_null($val) && preg_match("/^[-+]?[0-9]+$/", $val)));
	}

	/**
	 * Return the valid slq value of this field.
	 * @param	string	$val
	 * @return	string	
	 */
	public function getSqlValue($val)
	{
		if ($this->isValid($val)) {
			if (is_null($val)) {
				return 'NULL';
			} else {
				return $val;	
			}
		} else {
			if (DEBUG) {
				echo '<pre>';
				echo 'DEBUG - GInteger::getSqlValue<br />';
				echo "Value: " . (is_null($val) ? 'NULL' : $val) . '<br />';
				print_r($this);
				echo '</pre>';
			}
			return FALSE;
		}
	}
 }

?>