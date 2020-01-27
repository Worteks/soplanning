<?php

class Groupe extends GObject
{

	public function __construct()
	{
		$this->table = 'planning_groupe';

		$this->fields['groupe_id'] = new GInteger('planning_groupe', 'groupe_id', 11, FALSE, 0);
		$this->fields['nom'] = new GString('planning_groupe', 'nom', 30, FALSE, '');
		$this->fields['ordre'] = new GInteger('planning_groupe', 'ordre', 11, TRUE, NULL);

		$this->primaryKeys = array('groupe_id');

		parent::__construct();
	}

	public function db_save($updateData = array(), $insertData = array())
	{
		if (!$this->saved) {
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
