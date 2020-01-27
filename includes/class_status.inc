<?php

class Status extends GObject
{

	public function __construct()
	{
		$this->table = 'planning_status';

		$this->fields['status_id'] = new GString('planning_status', 'status_id', 10, FALSE, '');
		$this->fields['nom'] = new GString('planning_status', 'nom', 50, FALSE, '');
		$this->fields['commentaire'] = new GString('planning_status', 'commentaire', 255, TRUE, NULL);
		$this->fields['affichage'] = new GString('planning_status', 'affichage', 2, TRUE, NULL);
		$this->fields['barre'] = new GString('planning_status', 'barre', 2, TRUE, NULL);
		$this->fields['gras'] = new GString('planning_status', 'gras', 2, TRUE, NULL);
		$this->fields['italique'] = new GString('planning_status', 'italique', 2, TRUE, NULL);
		$this->fields['souligne'] = new GString('planning_status', 'souligne', 2, TRUE, NULL);
		$this->fields['defaut'] = new GString('planning_status', 'defaut', 2, TRUE, NULL);
		$this->fields['affichage_liste'] = new GString('planning_status', 'affichage_liste', 2, TRUE, 1);
		$this->fields['pourcentage'] = new GFloat('planning_status', 'pourcentage', 4, TRUE, 0);
		$this->fields['couleur'] = new GString('planning_status', 'couleur', 6, TRUE, '');
		$this->fields['priorite'] = new GInteger('planning_status', 'priorite', 4, TRUE, 5);
		$this->primaryKeys = array('status_id');

		parent::__construct();
	}

	

}

?>