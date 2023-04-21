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

	public function db_delete() {
		$projets = new GCollection('Projet');
		$projets->db_load(array('groupe_id', '=', $this->groupe_id));
		while($projet = $projets->fetch()){
			$projet->groupe_id = NULL;
			$projet->db_save();
		}

		$return = parent::db_delete();
		return $return;
	}

	public function getAPIData(){
		$data = array();
		$groupe = new Groupe();
		$data['id'] = valueAPI($this->groupe_id);
		$data['name'] = valueAPI($this->nom);
		return $data;
	}

	public function putAPI($group_id, $name){
		if(strlen(trim($group_id)) > 0 && (!is_numeric(trim($group_id)) || !$this->db_load(array('groupe_id', '=', trim($group_id))))) {
			throw new BadInputException('group_id unknown in database');
		}
		if(strlen(trim($name)) > 50 || strlen(trim($name)) <= 0){
			throw new BadInputException('Name length not valid (max 50)');
		}
		$this->nom = trim($name);

		if(!$this->db_save()){
			throw new SaveErrorException('Unexpected error, please report maximum details to support@soplanning.org');
		}
		return true;

	}

}

?>
