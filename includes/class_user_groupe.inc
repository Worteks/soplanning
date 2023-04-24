<?php

class User_groupe extends GObject
{

	public function __construct()
	{
		$this->table = 'planning_user_groupe';

		$this->fields['user_groupe_id'] = new GInteger('planning_user_groupe', 'user_groupe_id', 11, FALSE, 0);
		$this->fields['nom'] = new GString('planning_user_groupe', 'nom', 150, FALSE, '');

		$this->primaryKeys = array('user_groupe_id');

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

	public function getAPIData(){
		$data = array();
		$groupe = new Groupe();
		$data['id'] = valueAPI($this->user_groupe_id);
		$data['name'] = valueAPI($this->nom);
		return $data;
	}

	public function putAPI($team_id, $name){
		if(strlen(trim($team_id)) > 0 && (!is_numeric(trim($team_id)) || !$this->db_load(array('user_groupe_id', '=', trim($team_id))))) {
			throw new BadInputException('team_id unknown in database');
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
