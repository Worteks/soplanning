<?php

class Audit extends GObject
{

	public function __construct()
	{
		$this->table = 'planning_audit';
		$this->fields['audit_id'] = new GString('planning_audit', 'audit_id', 10, TRUE, '');
		$this->fields['date_modif'] = new GDatetime('planning_audit', 'date_modif', 19, FALSE, NULL);		
		$this->fields['user_modif'] = new GString('planning_audit', 'user_modif', 20, FALSE, NULL);
		$this->fields['type'] = new GString('planning_audit', 'type', 4, FALSE, NULL);		
		$this->fields['user_id'] = new GString('planning_audit', 'user_id', 20, TRUE, NULL);
		$this->fields['projet_id'] = new GString('planning_audit', 'projet_id', 20, TRUE, NULL);
		$this->fields['periode_id'] = new GString('planning_audit', 'periode_id', 20, TRUE, NULL);
		$this->fields['lieu_id'] = new GString('planning_audit', 'lieu_id', 20, TRUE, NULL);
		$this->fields['ressource_id'] = new GString('planning_audit', 'ressource_id', 20, TRUE, NULL);
		$this->fields['statut_id'] = new GString('planning_audit', 'statut_id', 20, TRUE, NULL);		
		$this->fields['equipe_id'] = new GString('planning_audit', 'equipe_id', 20, TRUE, NULL);		
		$this->fields['groupe_id'] = new GString('planning_audit', 'groupe_id', 20, TRUE, NULL);				
		$this->fields['anciennes_valeurs'] = new GString('planning_audit', 'anciennes_valeurs', 1024, TRUE, NULL);
		$this->fields['nouvelles_valeurs'] = new GString('planning_audit', 'nouvelles_valeurs', 1024, TRUE, NULL);
		$this->fields['informations'] = new GString('planning_audit', 'informations', 1024, TRUE, NULL);
		$this->fields['nbmodifs'] = new GInteger('planning_audit', 'nbmodifs', 11, TRUE, NULL);
		$this->primaryKeys = array('audit_id');
		parent::__construct();
	}

}

?>