<?php

class Ferie extends GObject
{

	public function __construct()
	{
		$this->table = 'planning_ferie';
		
		$this->fields['date_ferie'] = new GDate('planning_ferie', 'date_ferie', 10, FALSE, date('Y-m-d'));
		$this->fields['libelle'] = new GString('planning_ferie', 'libelle', 50, TRUE, NULL);
		$this->fields['couleur'] = new GString('planning_ferie', 'couleur', 6, TRUE, NULL);
		$this->primaryKeys = array('date_ferie');

		parent::__construct();
	}

	

}

?>