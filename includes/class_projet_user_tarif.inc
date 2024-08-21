<?php

class Projet_user_tarif extends GObject
{

	public function __construct()
	{
		$this->table = 'planning_projet_user_tarif';

		$this->fields['projet_user_tarif_id'] = new GInteger('planning_projet_user_tarif', 'projet_user_tarif_id', 11, FALSE, '');
		$this->fields['user_id'] = new GString('planning_projet_user_tarif', 'user_id', 20, FALSE, '');
		$this->fields['projet_id'] = new GString('planning_projet_user_tarif', 'projet_id', 20, FALSE, '');
		$this->fields['tarif_horaire'] = new GFloat('planning_projet_user_tarif', 'tarif_horaire', 15, TRUE, NULL);

		$this->primaryKeys = array('projet_user_tarif_id');

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
