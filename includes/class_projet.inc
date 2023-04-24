<?php

class Projet extends GObject
{

	public function __construct()
	{
		$this->table = 'planning_projet';
		
		$this->fields['projet_id'] = new GString('planning_projet', 'projet_id', 20, FALSE, '');
		$this->fields['nom'] = new GString('planning_projet', 'nom', 50, FALSE, '');
		$this->fields['iteration'] = new GString('planning_projet', 'iteration', 255, TRUE, NULL);
		$this->fields['couleur'] = new GString('planning_projet', 'couleur', 6, FALSE, 'FFFFFF');
		$this->fields['charge'] = new GFloat('planning_projet', 'charge', 11, TRUE, NULL);
		$this->fields['livraison'] = new GDate('planning_projet', 'livraison', 10, TRUE, NULL);
		$this->fields['lien'] = new GString('planning_projet', 'lien', 65000, TRUE, NULL);
		$this->fields['statut'] = new GString('planning_projet', 'statut', 10, FALSE, NULL);
		$this->fields['groupe_id'] = new GInteger('planning_projet', 'groupe_id', 11, TRUE, NULL);
		$this->fields['createur_id'] = new GString('planning_projet', 'createur_id', 20, FALSE, '');
		$this->primaryKeys = array('projet_id');

		parent::__construct();
	}

	public function getAPIData(){
		$data = array();
		$groupe = new Groupe();
		$data['id'] = valueAPI($this->projet_id);
		$data['group_id'] = valueAPI($this->groupe_id);
		$data['name'] = valueAPI($this->nom);
		$data['status'] = valueAPI($this->statut);
		$data['charge'] = valueAPI($this->charge);
		$data['delivery'] = valueAPI($this->livraison);
		$data['color'] = valueAPI($this->couleur);
		$data['link'] = valueAPI($this->lien);
		$data['owner_id'] = valueAPI($this->createur_id);
		$data['comment'] = valueAPI($this->iteration);
		return $data;
	}

	public function putAPI($project_id, $name, $owner_id, $status_id, $charge = '', $delivery = '', $color = '', $link = '', $comment = '', $group_id = ''){
		if(strlen($project_id) > 20 || strlen($project_id) < 1){
			throw new BadInputException('projectID length not valid (max 20)');
		}
		if(strlen(trim($name)) > 50 || strlen(trim($name)) <= 0){
			throw new BadInputException('Name length not valid (max 50)');
		}
		$owner = new User();
		if(trim($owner_id) == '' || !$owner->db_load(array('user_id', '=', trim($owner_id)))){
			throw new BadInputException('owner_id not existing in users list');
		}
		if(strlen(trim($color)) > 0 && strlen(trim($color)) != 6){
			throw new BadInputException('Color wrong value (6 chars, HTML color)');
		}
		$statut = new Status();
		if(trim($status_id) == '' || !$statut->db_load(array('status_id', '=', trim($status_id)))){
			throw new BadInputException('Status not found, please refer to statutes list');
		}
		if(strlen(trim($charge)) > 0 && (strlen(trim($charge)) > 20 || floatval(trim($charge)) == 0)){
			throw new BadInputException('Charge not valid (float with . as decimal)');
		}
		if(strlen(trim($delivery)) != '0' && (strlen(trim($delivery)) != '10' || !controlDateSql($delivery))){
			throw new BadInputException('Delivery date not valid (yyyy-mm-dd)');
		}
		if(strlen(trim($link)) > 500){
			throw new BadInputException('Link length not valid (max 500)');
		}
		if(strlen(trim($comment)) > 255){
			throw new BadInputException('Comment length not valid (max 255)');
		}
		if(strlen(trim($group_id)) > 0){
			$groupe = new Groupe();
			if(!$groupe->db_load(array('groupe_id', '=', trim($group_id)))){
				throw new BadInputException('Not existing group_id');
			}
		}

		$this->db_load(array('projet_id', '=', trim($project_id)));

		$this->projet_id = trim($project_id);
		$this->nom = trim($name);
		$this->createur_id = $owner_id;
		$this->statut = $status_id;
		$this->charge = (trim($charge) != '' ? floatval(trim($charge)) : NULL);
		if(trim($color) != ''){
			$this->couleur = trim($color);
		}
		$this->lien = (trim($link) != '' ? trim($link) : NULL);
		$this->livraison = (trim($delivery) != '' ? trim($delivery) : NULL);
		$this->iteration = (trim($comment) != '' ? trim($comment) : NULL);
		$this->groupe_id = (trim($group_id) > 0 ? trim($group_id) : NULL);
		if(!$this->db_save()){
			throw new SaveErrorException('Unexpected error, please report maximum details to support@soplanning.org');
		}
		return true;
	}

}

?>