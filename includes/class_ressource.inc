<?php

class Ressource extends GObject
{

	public function __construct()
	{
		$this->table = 'planning_ressource';
		$this->fields['ressource_id'] = new GString('planning_ressource', 'ressource_id', 20, FALSE, '');
		$this->fields['nom'] = new GString('planning_ressource', 'nom', 50, FALSE, '');
		$this->fields['commentaire'] = new GString('planning_ressource', 'commentaire', 255, TRUE, NULL);
		$this->fields['exclusif'] = new GFloat('planning_ressource', 'exclusif', 4, TRUE, 0);
		$this->primaryKeys = array('ressource_id');

		parent::__construct();
	}

	public function getAPIData(){
		$data = array();
		$groupe = new Groupe();
		$data['id'] = valueAPI($this->ressource_id);
		$data['name'] = valueAPI($this->nom);
		$data['comment'] = valueAPI($this->commentaire);
		$data['exclusive'] = valueAPI($this->exclusif);
		return $data;
	}

	public function putAPI($resource_id, $name, $comment = '', $exclusive = ''){
		if(strlen($resource_id) > 20 || strlen($resource_id) < 1){
			throw new BadInputException('ResourceID length not valid (max 20)');
		}
		if(strlen(trim($name)) > 50 || strlen(trim($name)) <= 0){
			throw new BadInputException('Name length not valid (max 50)');
		}
		if(strlen(trim($comment)) > 255){
			throw new BadInputException('Comment length not valid (max 255)');
		}

		$this->db_load(array('ressource_id', '=', trim($resource_id)));

		$this->ressource_id = trim($resource_id);
		$this->nom = trim($name);
		$this->commentaire = (trim($comment) != '' ? trim($comment) : NULL);
		$this->exclusif = (trim($exclusive) == 'yes' ? '1' : '0');
		if(!$this->db_save()){
			throw new SaveErrorException('Unexpected error, please report maximum details to support@soplanning.org');
		}
		return true;

	}
}

?>