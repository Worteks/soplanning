<?php

abstract class GObject
{
    /**
     * The name of the table associated with the object.
     * @var	string
     */
	protected $table        = '';

    /**
     * This boolean is used to know whether the object already exists in the database or not.
     * @var	boolean
     */
	protected $saved        = FALSE;

    /**
     * The data (the fields of the field in the db) of the object.
     * @var	array
     */
	protected $data			= array();

	/**
     * The data (the type fields of the table in the db) of the object.
     * @var	array
     */
	protected $fields		= array();

    /**
     * The primary keys of the table.
     * @var	array
     */
	protected $primaryKeys	= array();
	
    /**
     * This is the constructor required to create an object.
     * It has to be called in every child class.
     */
    public function __construct()
	{
		foreach ($this->fields as $k => $v) {
			$this->$k = $v->default;
		}
    }

    /**
     * Returns the value of the required field if it exists in the array data
	 * @param	string	$var	the name of the field
	 * @return	string			the content of the field
     */
	public function __get($var)
	{
		return $this->data[$var];
	}

    /**
     * Sets the value of the data field to the given one, even if this field does not exist yet.
	 * @param	string	$key	the name of the field to set
	 * @param	string	$val	the new value to assign
     */
	public function __set($key, $val)
	{
		if ($this->fields[$key]->table != $this->table) {
			return FALSE;
		} else {
			$this->data[$key] = $val;
			if (!empty($this->fields[$key]->dependantTable)) {
				foreach ($this->fields as $k => $v) {
					if ($v->table == (empty($this->fields[$key]->dependantTableAlias) ? $this->fields[$key]->dependantTable : $this->fields[$key]->dependantTableAlias )) {
						if ($this->fields[$key]->getSqlDependantWhere($val) === FALSE) {
							return FALSE;
						}
						$sql = 'SELECT ' . $v->getSqlName() . ' FROM '
										 . $this->fields[$key]->getSqlDependantTable() . ' WHERE '
										 . $this->fields[$key]->getSqlDependantWhere($val);
						$result = db_query($sql);
						if ($result) {
							$row = db_fetch_array($result);
							$this->data[$k] = $this->sql2val($row[$v->name]);
						} else {
							if (DEBUG) {
								echo 'GObject::set Dépendance non trouvé pour le champ :' . $v->getSqlName() . '<br />';
							}
							$this->data[$k] = NULL;
						}
					}
				}
			}
		}
		return TRUE;
	}

	/**
	 * Return a cleaned value of the database field
	 * @param	string	$val
	 * @return	string
	 */
	protected final function sql2val($val)
	{
		if ($val == "\0") {
			return '';
		}
		return $val;
	}

	/**
	 * Return a cleaned value of the form field
	 * @param	string	$val
	 * @return	string
	 */
	protected final function form2val($form)
	{
		return trim($form);
	}

    /**
     * Returns the complete array of data, including the primary keys and the boolean saved
	 * @return	array	the data of the object and the attributes `saved` and `primaryKeys`
     */
	public function getData()
	{
		$data = $this->data;
		$data['primaryKeys'] = $this->primaryKeys;
		$data['saved'] = ($this->saved) ? 1 : 0;
		return $data;
	}


    /**
     * Returns the complete array of data, including the primary keys and the boolean saved
	 * @return	array	the data of the object and the attributes `saved` and `primaryKeys`
     */
	public function getSmartyData()
	{
	    return $this->getData();
	}


    /**
     * Changes the current data to the given one and modifies the boolean saved and the primary keys
	 * @param	array	$data	the new data to set
     */
	public function setData($data)
	{
		$this->saved = (isset($data['saved']) && $data['saved'] == 1);
		$this->primaryKeys = $data['primaryKeys']; 
		unset($data['saved']);
		unset($data['primaryKeys']);
		$this->data = $data;
	}
	
    // ------------------------------------------------------------------------

    /**
     * Loads an object from the database.
     *
     * The object comes from the given {@link table} and after the query,
     * the function {@link db_loadArray()} is called to load the object.
     * @where	array		$params	an array containing the fields to match with
     * @return	boolean		TRUE if it happens correctly
     */
    public function db_load($where)
	{
		$sql = $this->getSqlQuery($where);
		if ($sql === FALSE) {
			return FALSE;
		}
		$result = db_query($sql);
		if (!$result || db_num_rows($result) != 1) return FALSE;
		$row = db_fetch_array($result);
		$this->db_loadArray($row);
		return TRUE;
	}
    
    /**
     * Loads an object with a specific sql request. The function {@link db_loadArray()} is used here.
     * @param	string	$sql	the query
     * @return	boolean			TRUE if the load is ok
     */
    public function db_loadSql($sql)
    {
        $result = db_query($sql);
		if (!$result || db_num_rows($result) != 1) return FALSE;
		$row = db_fetch_array($result);
		$this->db_loadArray($row);
		return TRUE;
    }

    /**
     * Fills the data of the object with the values of the given row.
     * It puts the variable saved to TRUE.
     * @param	array	$row	the row : ('key' => 'value')
     */
	public function db_loadArray($row)
	{
		reset($row);
		foreach ($row as $k => $v) {
			if (isset($this->fields[$k])) {
				/*
				if (DEBUG) {
					echo 'GObject::db_loadArray Variable existante : ' . $k . '<br />';
				}
				*/
				$this->$k = $this->sql2val($v);
			} else {
				/*
				if (DEBUG) {
					echo 'GObject::db_loadArray Variable inexistante : ' . $k . '<br />';
				}
				*/
				$this->data[$k] = $this->sql2val($v);
			}
		}
		$this->saved = TRUE;
	}

    /**
     * Saves the object in the database.
     *
     * The object is saved with some of its fields updated if necessary.
     * The boolean saved is used to know whether the object will be updated or inserted.
     * @param	array	$updateData	the data to set (for an update) before saving
     * @param	array	$insertData	the data to set (for an insert) before saving
     * @return	boolean				TRUE if it happens correctly
     */
	public function db_save($updateData = array(), $insertData = array())
	{
		$set = array();
		if ($this->saved) {
			// we update the db
			reset($this->data);
			foreach($this->data as $key => $val) {
				if (isset($this->fields[$key]) && $this->fields[$key]->table == $this->table) {
					if ($this->fields[$key]->getSqlSet($val) === FALSE) {
						return FALSE;
					}
					$set[$key] = $this->fields[$key]->getSqlSet($val);
				}
			}
			if (count($updateData) > 0) {
				// some data were given for update
				reset($updateData);
				foreach($updateData as $k => $v) {
					if ($this->fields[$k]->getSqlSet($val) === FALSE) {
					return FALSE;
					}
					$set[$k] = $this->fields[$k]->getSqlSet($v);
				}
			}
			$where = array();
			reset($this->primaryKeys);
			foreach($this->primaryKeys as $key) {
				$where[] = $set[$key];
				unset($set[$key]);
			}
			$sql = 'UPDATE ' . $this->getSqlTable() . ' SET ' . implode(', ', $set) . ' WHERE ' . implode(' AND ', $where);	
			
		} else {
            // the data has to be inserted into the db
			reset($this->data);
			foreach($this->data as $key => $val) {
				if ($this->fields[$key]->table == $this->table) {
					if ($this->fields[$key]->getSqlValue($val) === FALSE) {
						return FALSE;
					}
					$set[$this->fields[$key]->getSqlName()] = $this->fields[$key]->getSqlValue($val);
				}
			}
			if (count($insertData) > 0) {
				// some data were given for insert
				reset($insertData);
				foreach($insertData as $k => $v) {
					if ($this->fields[$k]->getSqlValue($v) === FALSE) {
						return FALSE;
					}
					$set[$this->fields[$k]->getSqlName()] = $this->fields[$k]->getSqlValue($v);
				}
			}
			$sql = 'INSERT INTO ' . $this->getSqlTable() . ' (' . implode(', ', array_keys($set)) . ') VALUES (' . implode(', ', $set) . ')';
		}
		$result = db_query($sql);
		if (!$result) {
			return FALSE;
		}
		$this->saved = TRUE;
		return TRUE;
	}
	
    /**
     * Deletes the object in the db.
     *
     * The operation requires the array primaryKeys to have been filled.
     * Note that it is the only thing required, with the name of the table, to delete the object.
     * @return	boolean	TRUE if it happens correctly
     */
	public function db_delete()
	{
		$where = array();
		foreach($this->primaryKeys as $key) {
			if ($this->fields[$key]->getSqlWhere($this->$key) === FALSE) {
				return FALSE;
			}
			$where[] = $this->fields[$key]->getSqlWhere($this->$key);
		}

		$sql = 'DELETE FROM ' . $this->getSqlTable() . ' WHERE ' . implode(' AND ', $where);
		if (($resultId = db_query($sql)) === FALSE) return FALSE;

		$this->saved = FALSE;
		return TRUE;
	}

    /**
     * Creates a SELECT query from the table of the object and the given conditions
     * @param	array	$where		the conditions of the query : array(field1, operator1, value1, field2, op2, val2...)
     * @param	array	$orderBy	the order by conditions of the query : array(fieldName => order)
	 * @return	string				the sql query
     */
	public function getSqlQuery($where = array(), $orderBy = array())
	{
		$fields = array();
		foreach ($this->fields as $key => $val) {
			$fields[$key] = $val->getSqlName() . ' AS ' . $key;
		}
		$from = array();
		reset($this->fields);
		foreach ($this->fields as $key => $val) {
			if (!empty($val->dependantTable)) {
				$from[] = ' LEFT JOIN ' . $val->getSqlDependantTable() . ' ON (' . $val->getSqlName() . ' = ' . $val->getSqlDependantName() . ')';
			}
		}
		$sqlWhere = array();
		if (count($where) % 3 != 0) {
		    return FALSE;
		}
		reset($where);
		for ($i=0; $i<count($where); $i+=3) {
			if ($this->getSqlWhere($where[$i], $where[$i+1], $where[$i+2]) === FALSE) {
				return FALSE;
			}
			$sqlWhere[] = $this->getSqlWhere($where[$i], $where[$i+1], $where[$i+2]);
		}
		return 'SELECT ' . implode(', ', $fields) . 
			   ' FROM ' . $this->getSqlTable() . ' ' . ((!empty($from)) ? implode(' ', $from) : '') . 
			   ((!empty($sqlWhere)) ? ' WHERE ' . implode(' AND ', $sqlWhere) : '') . 
			   $this->getSqlOrderBy($orderBy);
	}

    /**
     * Creates an order by clause for a sql query
     * @param	array	$orderBy	the order by conditions of the query : array(fieldName => order)
	 * @return	string				the order by clause
     */
	public function getSqlOrderBy($orderBy = array())
	{
		$orderBySql = array();
		foreach ($orderBy as $key => $val) {
			$orderBySql[] = $this->fields[$key]->getSqlName() . ' ' . $val;
		}
		return ((!empty($orderBySql)) ? ' ORDER BY ' . implode( ', ', $orderBySql) : '');
	}

	/**
	 * Return the valid database table name.
	 * @return	string	
	 */
	public function getSqlTable()
	{
		return $this->table ;
	}

    /**
	 * Return the valid slq clause where of a field.
     * @param	string	$key	the key's field name
	 * @param	string	$op		the operator used in the slq clause where
	 * @param	string	$val	the key's field value
	 * @return	string			the where clause
     */
	public function getSqlWhere($key, $op, $val)
	{
		return $this->fields[$key]->getSqlWhere($val, $op);
	}

    /**
     * Fills the data of the object with the values of the given row.
     * It puts the variable saved to the value given in the row under the name 'saved'.
     * Note that the values may come from the fields of a form. Thus they are cleaned before.
     * @param	array	$row	the row : ('key' => 'value')
     */
	public function loadArray($row)
	{
		reset($this->data);
		foreach ($this->data as $k => $v) {
			if (isset($row[$k])) {
				if ($this->form2val($row[$k]) == '') {
					$this->$k = NULL;	
				} else {
					$this->$k = $this->form2val($row[$k]);
				}
			}
		}
		if (isset($row['saved'])) $this->saved = ($this->form2val($row['saved']) ? TRUE : FALSE);
	}

    /**
     * Valid all fields with their declarations.
     * @return	{ TRUE		if all fields are validate
	 *			{ array()	with all keys not valid		
     */
	public function check($excludePK = false)
	{
		$err = array();
		reset($this->fields);
		foreach ($this->fields as $key => $val) {
			if (($this->fields[$key]->table == $this->table) && ($val->isValid($this->$key) === FALSE)) {
				if($excludePK && in_array($key, $this->primaryKeys)) {
					continue;	
				}
				$err[] = $key;
			} 
		}

		return (count($err) == 0 ? TRUE : $err);	
	}

    /**
     * Valid one field with this declaration.
     * @return	{ TRUE		if the field $field is valid
     */
	public function isValid($key)
	{
		if (!isset($this->data[$key]) || !isset($this->fields[$key])) {
			return FALSE;
		} elseif (($this->fields[$key]->table != $this->table)) {
			return FALSE;
		} elseif ($this->fields[$key]->isValid($this->data[$key]) === FALSE) {
			return FALSE;
		} else {
			return TRUE;
		}
	}

	public function isSaved()
	{
		return $this->saved;
	}

	public function getFieldType($field) {
		if(!array_key_exists($field, $this->fields)) {
			return false;
		}
		$class = get_class($this->fields[$field]);
		return $class;
	}

}

?>