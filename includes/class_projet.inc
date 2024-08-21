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
		$this->fields['livraison'] = new GDate('planning_projet', 'livraison', 10, TRUE, NULL);
		$this->fields['lien'] = new GString('planning_projet', 'lien', 65000, TRUE, NULL);
		$this->fields['statut'] = new GString('planning_projet', 'statut', 10, FALSE, NULL);
		$this->fields['groupe_id'] = new GInteger('planning_projet', 'groupe_id', 11, TRUE, NULL);
		$this->fields['createur_id'] = new GString('planning_projet', 'createur_id', 20, FALSE, '');
		$this->fields['budget_montant'] = new GFloat('planning_projet', 'budget_montant', 20, TRUE, NULL);
		$this->fields['budget_temps'] = new GFloat('planning_projet', 'budget_temps', 20, TRUE, NULL);
		$this->fields['montant_consomme'] = new GFloat('planning_projet', 'montant_consomme', 20, TRUE, NULL);
		$this->fields['temps_consomme'] = new GFloat('planning_projet', 'temps_consomme', 20, TRUE, NULL);
		$this->fields['montant_restant'] = new GFloat('planning_projet', 'montant_restant', 20, TRUE, NULL);
		$this->fields['temps_restant'] = new GFloat('planning_projet', 'temps_restant', 20, TRUE, NULL);

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
		$data['delivery'] = valueAPI($this->livraison);
		$data['color'] = valueAPI($this->couleur);
		$data['link'] = valueAPI($this->lien);
		$data['owner_id'] = valueAPI($this->createur_id);
		$data['comment'] = valueAPI($this->iteration);
		$data['budget_amount'] = valueAPI($this->budget_montant);
		$data['amount_consumed'] = valueAPI($this->montant_consomme);
		$data['amount_remaining'] = valueAPI($this->montant_restant);
		$data['budget_time'] = valueAPI($this->budget_temps);
		$data['time_consumed'] = valueAPI($this->temps_consomme);
		$data['time_remaining'] = valueAPI($this->temps_restant);
		return $data;
	}

	public function putAPI($project_id, $name, $owner_id, $status_id, $delivery = '', $color = '', $link = '', $comment = '', $group_id = '', $budget_amount = '', $budget_time = ''){
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
		if(strlen(trim($budget_amount)) > 0 && (strlen(trim($budget_amount)) > 20 || floatval(trim($budget_amount)) == 0)){
			throw new BadInputException('budget_amount not valid (float with . as decimal)');
		}
		if(strlen(trim($budget_time)) > 0 && (strlen(trim($budget_time)) > 20 || floatval(trim($budget_time)) == 0)){
			throw new BadInputException('budget_time not valid (float with . as decimal)');
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

		$encoding = mb_detect_encoding($name);
		$this->nom = (trim($name) != '' ? trim(($encoding == 'UTF-8' ? utf8_decode($name) : $name)) : NULL);
		
		$this->createur_id = $owner_id;
		$this->statut = $status_id;
		$this->budget_montant = (trim($budget_amount) != '' ? floatval(trim($budget_amount)) : NULL);
		$this->budget_temps = (trim($budget_time) != '' ? floatval(trim($budget_time)) : NULL);
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

	public function updateBudgets(){
		// cas de l'installation
		if(!defined("CONFIG_DAYS_INCLUDED")){
			return true;
		}
		$DAYS_INCLUDED = explode(',', CONFIG_DAYS_INCLUDED);
		global $joursFeries;
		if(!is_array($joursFeries)){
			$joursFeries = getJoursFeries();
		}

		$taches = new GCollection('Periode');
		$sql = "SELECT planning_periode.*, put.tarif_horaire, pu.tarif_horaire_defaut
				FROM planning_periode
				LEFT JOIN planning_projet_user_tarif AS put ON put.user_id = planning_periode.user_id AND put.projet_id = " . val2sql($this->projet_id) . "
				INNER JOIN planning_user AS pu ON pu.user_id = planning_periode.user_id
				WHERE planning_periode.projet_id = " . val2sql($this->projet_id) . "
				ORDER BY date_debut
				";
		$taches->db_loadSql($sql);
		$totalHeures = 0;
		$totalEuros = 0;
		while($tache = $taches->fetch()){
			$date1 = new DateTime();
			$date1->setDate((int)substr($tache->date_debut,0,4), (int)substr($tache->date_debut,5,2), (int)substr($tache->date_debut,8,2));

			// on additionne les jours de travail
			if(!is_null($tache->date_fin)) {
				$date2 = new DateTime();
				$date2->setDate((int)substr($tache->date_fin,0,4), (int)substr($tache->date_fin,5,2), (int)substr($tache->date_fin,8,2));
				while ($date1 <= $date2) {
					// on ne compte pas le jour si c'est WE ou jour férié
					if (in_array($date1->format('w'), $DAYS_INCLUDED) && !array_key_exists($date1->format('Y-m-d'), $joursFeries)) 
					{
						$totalHeures = ajouterDuree($totalHeures, CONFIG_DURATION_DAY);
					}
					$date1->modify('+1 day');
				}
			} else {
				//$dureeSansPause = retirerDureePause($infos_tache['duree'], $infos_tache['pause']);
				//$totalHeures = ajouterDuree($totalHeures, $dureeSansPause);
				$totalHeures = ajouterDuree($totalHeures, $tache->duree);
				$totalEuros += decimalHours($tache->duree) * ($tache->tarif_horaire > 0 ? $tache->tarif_horaire : $tache->tarif_horaire_defaut);
			}
		}
		$this->temps_consomme = decimalHours($totalHeures);
		$this->montant_consomme = $totalEuros;

		if($this->budget_montant > 0){
			$this->montant_restant = $this->budget_montant - $this->montant_consomme;		
		}
		if($this->budget_temps > 0){
			$this->temps_restant = $this->budget_temps - $this->temps_consomme;		
		}

		$this->db_save();
		return true;		
	}
}

?>