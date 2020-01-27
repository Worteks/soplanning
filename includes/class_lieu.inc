<?php

class Lieu extends GObject
{

	public function __construct()
	{
		$this->table = 'planning_lieu';
		$this->fields['lieu_id'] = new GString('planning_lieu', 'lieu_id', 10, FALSE, '');
		$this->fields['nom'] = new GString('planning_lieu', 'nom', 50, FALSE, '');
		$this->fields['commentaire'] = new GString('planning_lieu', 'commentaire', 255, TRUE, NULL);
		$this->fields['exclusif'] = new GFloat('planning_lieu', 'exclusif', 4, TRUE, 0);
		$this->primaryKeys = array('lieu_id');
		parent::__construct();
	}

	

}

?>