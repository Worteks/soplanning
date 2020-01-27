<?php

class GCollection
{
    /**
     * The name of the associated class.
     * @var		string 
     */
	private $itemClassName	= '';
    
	/**
     * The type of the collection : recodset or array.
     * @var		string 
     */
	protected $type	= FALSE;
    
	/**
     * The current object of the collection.
     * @var		object 
     */
	private $currentItem	= FALSE;
    
	/**
     * The number of the current page
     * @var		integer 
     */
	private $currentPage	= 1;
    
	/**
     * The result of the query after execution.
     * @var string 
     */
	private $resultId		= FALSE;
   
	/**
     * The query that was sent to the database
     * @var string 
     */
	private $sql			= '';
	
	/**
     * The query that was sent to the database
     * @var array 
     */
	private $orderBy			= array();
	
	/**
     * The number of results per page
     * @var		integer 
     */
	private $nbResultsPerPage = 0;
    
	/**
     * The array who contains the collection.
     * @var		array 
     */
	private $itemList			=	array();

    /**
     * The current index of the array.
     * @var		integer 
     */
	private $currentItemIndex	=	FALSE;

    /**
     * This is the constructor required to create a collection.
     * @param	string	$itemClassName	the name of the associated class
     */
	public function __construct($itemClassName)
	{ 
		$this->itemClassName = $itemClassName;
	}

	/**
	 * If collection exists and it's a recordset. Put the recodset on $itemList
	 * @return	boolean	TRUE if it happens correctly
	 */
	private function _recordset2array()
	{
		if ($this->type == 'recordset') {
			$this->seek(0);
			while($i = $this->fetch()) {
				$this->itemList[] = $i;
			}
			$this->type = 'array';
			return TRUE;
		}
		return FALSE;
	}

    /**
     * Loads a collection from an array.
     *
     * The collection is loaded means the variable {@link resultId} contains the result of a query.
     * The objects of the collection are extracted from it.
     * @param array $where.
	 * @param array $orderBy.
     * @return boolean TRUE if it happens correctly
     */
	public function db_load($where = array(), $orderBy = array())
	{
		if ($this->type != 'recordset') {
			$this->type = 'recordset';
		}
		if ($this->currentItem === FALSE) {
			$this->currentItem = new $this->itemClassName();
		}
		$this->sql = $this->currentItem->getSqlQuery($where);
		$this->orderBy = $orderBy;
		return $this->db_loadSql($this->sql . $this->currentItem->getSqlOrderBy($orderBy));
	}

    /**
     * Loads a collection from the database.
     *
     * The collection is loaded means the variable {@link resultId} contains the result of a query.
     * The objects of the collection are extracted from it.
     * @param string $sql the query to execute. If not given, a generic one will be used.
     * @return boolean TRUE if it happens correctly
     */
	public function db_loadSQL($sql = '')
	{
		if ($this->type != 'recordset') {
			$this->type = 'recordset';
		}
		if ($this->currentItem === FALSE) {
			$this->currentItem = new $this->itemClassName();
		}

		$this->sql = (!empty($sql)) ? $sql : $this->currentItem->getSqlQuery();
		$this->resultId = db_query($this->sql);
		return ($this->resultId !== FALSE);
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
		if ($this->currentItem === FALSE) {
			$this->currentItem = new $this->itemClassName();
		}
		return $this->currentItem->getSqlWhere($key, $op, $val);
	}

    /**
	 * Return the valid database table name.
	 * @return	string
     */
	public function getSqlTable()
	{
		if ($this->currentItem === FALSE) {
			$this->currentItem = new $this->itemClassName();
		}
		return $this->currentItem->getSqlTable();
	}

    /**
     * Save the collection in the database.
     * @return	boolean		TRUE if it happens correctly
     */
	public function db_save($params = array())
	{
		return $this->walk('db_save', $params);
	}

    /**
     * Delete the collection in the database.
     * @return	boolean		TRUE if it happens correctly
     */
	public function db_delete()
	{
		return $this->walk('db_delete');
	}

	/**
	 * Update all values of $param_name at $param_value at each object
	 * @param	string	$param_name		name of the parameter
	 * @param	array	$param_value	value of the parameter
	 * @return	boolean					TRUE if it happens correctly
	 */
	function set($param_name, $param_value) {
		return $this->walk('__set', array('prm1' => $param_name, 'prm2' => $param_value));
	}

	/**
	 * Return an array who contains all $param_name values 
	 * @param	string	$param_name		name of the parameter
	 * @return	array
	 */
	function get($param_name) {
		return $this->map('__get', array('prm1' => $param_name));
	}

	/**
     * Add an item on the collection.
	 * @param	object	$item
	 * @return	boolean	TRUE	if it happens corectly
     */
	public function addItem($item)
	{
		if (get_class($item) == $this->itemClassName) {
			if ($this->type == 'recordset') {
				if (!$this->_recordset2array()) {
					return FALSE;
				}
			} elseif ($this->type === FALSE) {
				$this->type = 'array';
			}
			$this->itemList[] = $item;
			$this->currentItemIndex = $this->getCount() - 1;
			return TRUE;
		}
		return FALSE;
	}
	
	/**
     * Remove an item on the collection.
	 * @param	object	$item
	 * @return	boolean	TRUE	if it happens corectly
     */
	public function deleteItem($item)
	{
		if (get_class($item) == $this->itemClassName) {
			if ($this->type == 'recordset') {
				if (!$this->_recordset2array()) {
					return FALSE;
				}
			} elseif ($this->type == 'array') {
				foreach ($this->itemList as $key => $val) {
					if ($item == $val) {
						if ($this->currentItemIndex == $key) {
							$this->seek($this->currentItemIndex);
						}
						array_splice ($this->itemList, $key, 1);
						return TRUE;
					}
				}
			}
		}
		return FALSE;
	}

	/**
     * Puts the cursor (the one that points on the current row of the result) on the given index.
	 * @param	integer	$index	the index of the array to which the cursor should point
     * @return	boolean	TRUE	if it happens correctly
     */
	public function seek($index)
	{
		if (($index < 0) || ($index >= $this->getCount())) {
			$this->currentItemIndex = FALSE;
			return FALSE;
		}
		if ($this->type == 'recordset') {
			return ($this->resultId) ? db_data_seek($this->resultId, $index) : FALSE;
		} elseif ($this->type == 'array') {
				$this->currentItemIndex = $index;
				return TRUE;
		} else {
			return FALSE;
		}
	}

    /**
     * Loads the object pointed by the current cursor.
     * @return	object	the current item
     */
	public function fetch()
	{
		if ($this->type == 'recordset') {
			$row = db_fetch_array($this->resultId);
			if (!$row) {
				return FALSE;
			}
			$this->currentItem->db_loadArray($row);
		} elseif ($this->type == 'array') {
			if ($this->currentItemIndex === FALSE) {
				return FALSE;
			}
			$this->currentItem = $this->itemList[$this->currentItemIndex++];
			if ($this->currentItemIndex == $this->getCount()) {
				$this->currentItemIndex = FALSE;
			}
		} else {
			return FALSE;
		}

		return $this->currentItem;
	}

    /**
     * Return the previous object pointed by the current cursor.
     * @return	object	the previous item
     */
	public function getPreviousItem() 
	{
		if ($this->type == 'recordset') {
			$currentPosition = $this->getCount();
			while($row = $this->fetch()) {
				$currentPosition--;
			}
		} elseif ($this->type == 'array') {
			$currentPosition = $this->currentItemIndex;
		} else {
			return FALSE;
		}

		if ($currentPosition === FALSE || $currentPosition == 0) {
			$this->seek($currentPosition);
			return FALSE;
		} else {
			$this->seek($currentPosition-1);
			$previousItem = $this->fetch();
			$this->seek($currentPosition);
			return $previousItem;			
		}
	}

    /**
     * Return the next object pointed by the current cursor.
     * @return	object	the next item
     */
	public function getNextItem() 
	{
		if ($this->type == 'recordset') {
			$currentPosition = $this->getCount();
			while($row = $this->fetch()) {
				$currentPosition--;
			}
		} elseif ($this->type == 'array') {
			$currentPosition = $this->currentItemIndex;
		} else {
			return FALSE;
		}
		if ($currentPosition === FALSE || $currentPosition+1 == $this->getCount()) {
			$this->seek($currentPosition);
			return FALSE;
		} else {
			$this->seek($currentPosition+1);
			$nextItem = $this->fetch();
			$this->seek($currentPosition);
			return $nextItem;			
		}
	}

    /**
     * Counts the number of items in the result of the collection
     * @return	integer	the number of rows in the result of the query
     */
	public function getCount()
	{
		if ($this->type == 'recordset') {
			return ($this->resultId) ? db_num_rows($this->resultId) : 0;
		} elseif ($this->type == 'array') {
			return count($this->itemList);
		} else {
			return FALSE;
		}
	}

	private	function _compare($a, $b)
	{
		$result = 0;
		reset($this->orderBy);
		foreach ($this->orderBy as $k => $v){
			$result = strnatcmp($a->$k, $b->$k);
			if ($v == 'DESC') {
				$result = - $result;
			}
			if ($result != 0) {
				return $result;
			}
		}
		return $result;
	}

	/**
     * Sort the collection based on $orderBy field.
	 * @param	array	$orderBy
     */
	public function sort($orderBy)
	{
		$this->orderBy = $orderBy;
		if ($this->type == 'recordset') {
			$tmp = new $this->itemClassName();
			return $this->db_loadSql($this->sql . $tmp->getSqlOrderBy($this->orderBy));
		} elseif ($this->type == 'array') {
			reset($this->orderBy);
			return usort($this->itemList,array($this,"_compare"));
		} else {
			return FALSE;
		}
	}

	/**
     * set data of each object in the collection.
     */
	function setData($data) {

		$this->type				= FALSE;
		$this->currentItem		= FALSE;
		$this->currentPage		= 1;
		$this->resultId			= FALSE;
		$this->sql				= '';
		$this->orderBy			= array();
		$this->itemList			= array();
		$this->currentItemIndex	= FALSE;

		foreach($data as $obj_data) {
			$obj = new $this->itemClassName();
			$obj->setData($obj_data);
			$this->addItem($obj);
		}
	}

    /**
     * Return data contained in one page of the collection.
     * @return	array
     */
	public function getData($paginationOn = FALSE) {
		return $this->map('getData', array(), $paginationOn);
	}
	
    /**
     * Return data contained in one page of the collection.
     * @return	array
     */
	function getSmartyData($paginationOn = FALSE) {
		return $this->map('getSmartyData', array(), $paginationOn);
	}

    /**
     * Return an assciative array containing data in associative array.
     * @return	array
     */
	public function getSmartyKeyData($key, $paginationOn = FALSE) {
		$smartyData = $this->getSmartyData($paginationOn);
		$data = array();
		foreach ($smartyData as $val) {
			$data[$val[$key]] = $val;
		}
		return $data;
	}

    /**
     * Return an assciative array containing data for a select menu.
     * @return	array
     */
	function getSmartyOptions($key, $value) {
		$this->reset();
		$tab = array();
		while($obj = $this->fetch()) {
			$tab[$obj->$key] = $obj->$value;
		}
		return $tab;
	}

    /**
     * Sets the position of the cursor (the one that points on the current row of the result) to the beginning of the result.
     * @return	boolean	TRUE	if it happens correctly
     */
	public function reset()
	{
		return ($this->getCount() > 0 ? $this->seek(0) : FALSE);
	}

    /**
     * Returns the current object to work on it.
     * @return	object	the current object
     */
	public function getCurrentItem()
	{
		return $this->currentItem;
	}

	/**
     * Returns the total number of the pages.
     * @return	integer
     */
	public function getNbPages() {
		if ($this->getCount() % $this->nbResultsPerPage == 0) {
			return (int)($this->getCount() / $this->nbResultsPerPage);
		} else {
			return (int)($this->getCount() / $this->nbResultsPerPage) + 1;
		}
	}

	/**
     * Returns the number of the current page.
     * @return	integer
     */
	public function getCurrentPage() {
		return $this->currentPage;
	}

	/**
     * Sets the value of the currentPage field .
	 * @param	string	$page	the new value to assign
     */
	public function setCurrentPage($page) {
		$this->currentPage = $page;
	}

	/**
     * Returns the value of the nbResultsPerPage field.
	 * @return	integer
	 */
	public function getPagination() {
		return $this->nbResultsPerPage;
	}

	/**
     * Sets the value of the nbResultsPerPage field .
	 * @param	string	$nbResultsPerPage	the new value to assign
     */
	public function setPagination($nbResultsPerPage) {
		if ($nbResultsPerPage != 0) {
			$currentFirstItem = ($this->currentPage - 1) * $this->nbResultsPerPage + 1;
			if ($currentFirstItem % $nbResultsPerPage == 0) {
				$this->currentPage =  (int)($currentFirstItem / $nbResultsPerPage);
			} else {
				$this->currentPage =  (int)($currentFirstItem / $nbResultsPerPage) + 1;
			}
		} else {
			$this->currentPage = 1;
		}
		$this->nbResultsPerPage = $nbResultsPerPage;
	}

	/**
     * Gets XML from the collection.
	 * @return	array	$rows
     */
	public function getXML($paginationOn = FALSE) {
		return $this->map('getXML', array(), $paginationOn);
	}

	/**
     * Applies $funcName with $params to the collection.
	 * @param	string	$funcName		the name of the fonction
	 * @param	array	$params			parameters of the fonction
	 * @param	boolean	$paginationOn	indicate if pagination is active
	 * @return	array					contain the result of the application  of the function on an object
     */
	public function map($funcName, $params = array(), $paginationOn = FALSE) {
		if (!$paginationOn) {
			$this->seek(0);
		} else {
			$this->seek(($this->currentPage - 1) * $this->nbResultsPerPage);
		}
		$prm = array();
		foreach ($params as $key => $val) {
			$prm[] = '$params[\'' . $key . '\']';
		}
		$i = 0;
		$result = array();
		while (( ($i < $this->nbResultsPerPage) || ($this->nbResultsPerPage == 0) || !$paginationOn ) && ($item = $this->fetch())) {
			$myString = '$item->' . $funcName . '(' . implode(', ', $prm) . ');';
			$result[$i++] = eval("return $myString");
			// TODO : a enlever
			//$result[$i++] = eval('$item->' . $funcName . '(' . implode(', ', $prm) . ');');
		}
		return $result;
	}

	/**
	 * Apply a function at all objet in the collection and checks if it happens correctly
	 * @param	string	$funcName		the name of the fonction
	 * @param	array	$params			parameters of the fonction
	 * @param	boolean	$paginationOn	indicate if pagination is active
	 * @return	boolean
	 */
	public function walk($funcName, $params = array(), $paginationOn = FALSE)
	{
		$result = $this->map($funcName, $params, $paginationOn);
		reset($result);
		foreach ($result as $key => $val) {
			if ($val === FALSE) {
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * Find a value in the collection
	 * @param	string	$key		the name of search key
	 * @param	string	$val		the searched value
	 * @return	integer|FALSE		position of $val or FALSE if not find
	 */
	public function find($key, $val)
	{
		$this->reset();
		$index = 0;
		while($obj = $this->fetch()) {
			if ($obj->$key == $val) {
				$this->seek($index);
				return $index;
			}
			$index++;
		}
		return FALSE;
	}


}

?>