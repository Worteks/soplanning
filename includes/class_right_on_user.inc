<?php

class Right_on_user extends GObject
{

	public function __construct()
	{
		$this->table = 'planning_right_on_user';

		$this->fields['right_id'] = new GInteger('planning_right_on_user', 'right_id', 11, FALSE, '');
		$this->fields['owner_id'] = new GString('planning_right_on_user', 'owner_id', 20, FALSE, '');
		$this->fields['allowed_id'] = new GString('planning_right_on_user', 'allowed_id', 20, FALSE, '');

		$this->primaryKeys = array('right_id');

		parent::__construct();
	}

	public function db_save($updateData = array(), $insertData = array(), $import=false)
	{
		if (!$this->saved && !$import) {
			$result = db_query('SELECT MAX(' . $this->primaryKeys[0] . ') AS max FROM ' . $this->table);
			$new = 1;
			while ($var = db_fetch_array($result)) {
				$new = $var['max'] + 1;
			}
			$this->data[$this->primaryKeys[0]] = $new;
		}
		return parent::db_save($updateData, $insertData);
	}

}

?>
