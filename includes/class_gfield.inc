<?php

abstract class GField
{

    /**
     * Name of the table to which the field belongs.
     * @var string 
     */
	public $table = '';
    
    /**
     * Database name of the field.
     * @var string 
     */
	public $name = '';

	/**
     * Size of the field. For an enum, this size contains all the possible values.
     * @var integer 
     */
	public $size = 0;
    
	/**
     * Indicate if the field can be null.
     * @var boolean 
     */
	public $nullable = TRUE;
    
	/**
     * Indicate the default value of the field.
     * @var string 
     */
	public $default = NULL;
   
	/**
     * If this field involves a dependance with another table, 
	 * this variable contains the key of this one.
     * @var string 
     */
	public $dependantKey = NULL;

	/**
     * If this field involves a dependance with another table, 
	 * this variable contains the name of this one.
     * @var string 
     */
	public $dependantTable = NULL;

	/**
     * If this field involves a dependance with another table, 
	 * this variable contains the alias at the dependant table.
     * @var string 
     */
	public $dependantTableAlias = NULL;
	
	/**
     * This is the constructor required to create an object.
     * It has to be called in every child class.
	 * It permits to initialize all variables
     */
	public function __construct($table, $name, $size, $nullable, $default, $dependantKey = NULL, $dependantTable = NULL, $dependantTableAlias = NULL) {
		$this->table = $table;
		$this->name = $name;
		$this->size = $size;
		$this->nullable = $nullable;
		$this->default = $default;
		$this->dependantKey = $dependantKey;
		$this->dependantTable = $dependantTable;
		$this->dependantTableAlias = $dependantTableAlias;
	}

	/**
	 * Test if the value is valid with the type of this field.
	 * @param	string	$val
	 * @return	boolean	TRUE if $val is a valid value
	 */
	public abstract function isValid($val);

	/**
	 * Return the valid database field name.
	 * @return	string	
	 */
	public function getSqlName()
	{
		return $this->name;
	}

	/**
	 * Return the valid database table name.
	 * @return	string	
	 */
	public function getSqlTable()
	{
		return $this->table;
	}

	/**
	 * Return the valid slq value of this field.
	 * @param	string	$val
	 * @return	string	
	 */
	public function getSqlValue($val)
	{
		global $cfgConnection;
		$val = trim((string) $val);
		if (($val == '') || is_null($val)) {
			$val = NULL;
		}
		if ($this->isValid($val)) {
			if (is_null($val)) {
				return 'NULL';
			} else {
				return '\'' . mysqli_real_escape_string($cfgConnection, $val) . '\'';
			}
		} else {
			if (DEBUG) {
				echo '<pre>';
				echo 'DEBUG - GField::getSqlValue<br />';
				echo "Value: " . (is_null($val) ? 'NULL' : $val) . '<br />';
				print_r($this);
				echo '</pre>';
			}
			return FALSE;
		}
	}

	/**
	 * Return the valid slq clause where of this field.
	 * @param	string	$val
	 * @param	string	$op		the operateur used in the slq clause where
	 * @return	string	
	 */
	public function getSqlWhere($val, $op = '=')
	{
		if (((strtoupper(trim($op)) == 'IN') || (strtoupper(trim($op)) == 'NOT IN'))) {
			$finalValues = array();
			if (is_array($val)) {
				foreach ((array)$val as $value) {
					if ($this->isValid($value)) {
						$finalValues[] = $this->getSqlValue($value);
					}
				}
			} elseif ($this->isValid($val)) {
				$finalValues[] = $this->getSqlValue($val);
			}

			if (count($finalValues) > 0) {
				return $this->getSqlName() . ' ' . $op . ' (' . implode(', ', $finalValues) . ')';
			} else {
				if (DEBUG) {
					echo '<pre>';
					echo 'DEBUG - GField::getSqlWhere<br />';
					echo "Value: " . (is_null($val) ? 'NULL' : print_r($val)) . '<br />';
					print_r($this);
					echo '</pre>';
				}
				return FALSE;
			}
		} elseif ($this->isValid($val)) {
			return $this->getSqlName() . ' ' . $op . ' ' . $this->getSqlValue($val);
		} else {
			if (DEBUG) {
				echo '<pre>';
				echo 'DEBUG - GField::getSqlWhere<br />';
				echo "Value: " . (is_null($val) ? 'NULL' : $val) . '<br />';
				print_r($this);
				echo '</pre>';
			}
			return FALSE;
		}
	}

	/**
	 * Return the valid slq clause set of this field.
	 * @param	string	$val
	 * @return	string	
	 */
	public function getSqlSet($val)
	{
		if ($this->isValid($val)) {
			return $this->getSqlName() . ' = ' . $this->getSqlValue($val);
		} else {
			if (DEBUG) {
				echo '<pre>';
				echo 'DEBUG - GField::getSqlSet<br />';
				echo "Value: " . (is_null($val) ? 'NULL' : $val) . '<br />';
				print_r($this);
				echo '</pre>';
			}
			return FALSE;
		}
	}

	/**
	 * Return the valid slq clause where of the dependant table name.
	 * @param	string	$val
	 * @return	string	
	 */
	public function getSqlDependantWhere($val)
	{
		if ($this->isValid($val) && !empty($this->dependantTable)) {
			if (!empty($this->dependantTableAlias)) {
				if (is_null($val)) {
					return $this->dependantTableAlias . '.' . $this->dependantKey . ' IS NULL';
				} else {
					return $this->dependantTableAlias . '.' .$this->dependantKey . ' = ' . $this->getSqlValue($val);
				}
			} else {
				if (is_null($val)) {
					return $this->dependantTable . '.' .$this->dependantKey . ' IS NULL';
				} else {
					return $this->dependantTable . $this->dependantKey . ' = ' . $this->getSqlValue($val);					
				}
			}
		} elseif (empty($val) && !empty($this->dependantTable)) {
			if (!empty($this->dependantTableAlias)) {
				return $this->dependantTableAlias . '.' . $this->dependantKey .  ' IS NULL';
			} else {
				return $this->dependantTable . '.' . $this->dependantKey . ' IS NULL';
			}
		} else {
			if (DEBUG) {
				echo '<pre>';
				echo 'DEBUG - GField::getSqlDependantWhere<br />';
				echo "Value: " . (is_null($val) ? 'NULL' : $val) . '<br />';
				print_r($this);
				echo '</pre>';
			}
			return FALSE;
		}
	}

	/**
	 * Return the valid slq dependant table name.
	 * @return	string	
	 */
	public function getSqlDependantTable()
	{
		if (!empty($this->dependantTable)) {
			if (!empty($this->dependantTableAlias)) {
				return $this->dependantTable . ' AS ' . $this->dependantTableAlias;
			} else {
				return $this->dependantTable ;
			}
		} else {
			if (DEBUG) {
				echo '<pre>';
				echo 'DEBUG - GField::getSqlDependantTable<br />';
				echo "Value: " . (is_null($val) ? 'NULL' : $val) . '<br />';
				print_r($this);
				echo '</pre>';
			}
			return FALSE;
		}
	}

	/**
	 * Return the valid slq dependant table field name.
	 * @return	string	
	 */
	public function getSqlDependantName()
	{
		if (!empty($this->dependantTable)) {
			if (!empty($this->dependantTableAlias)) {
				return $this->dependantTableAlias . '.' . $this->dependantKey ;
			} else {
				return $this->dependantTable . '.' . $this->dependantKey;
			}
		} else {
			if (DEBUG) {
				echo '<pre>';
				echo 'DEBUG - GField::getSqlDependantName<br />';
				echo "Value: " . (is_null($val) ? 'NULL' : $val) . '<br />';
				print_r($this);
				echo '</pre>';
			}
			return FALSE;
		}
	}
}
?>