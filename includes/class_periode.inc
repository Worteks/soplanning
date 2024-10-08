<?php

class Periode extends GObject
{
	protected $repetitionPossible = array('jour','mois','semaine');

	public function __construct()
	{
		$this->table = 'planning_periode';
		
		$this->fields['periode_id'] = new GInteger('planning_periode', 'periode_id', 11, FALSE, 0);
		$this->fields['parent_id'] = new GInteger('planning_periode', 'parent_id', 11, TRUE, NULL);
		$this->fields['projet_id'] = new GString('planning_periode', 'projet_id', 20, FALSE, '');
		$this->fields['user_id'] = new GString('planning_periode', 'user_id', 20, FALSE, '');
		$this->fields['link_id'] = new GString('planning_periode', 'link_id', 25, TRUE, NULL);
		$this->fields['date_debut'] = new GDate('planning_periode', 'date_debut', 10, FALSE, date('Y-m-d'));
		$this->fields['date_fin'] = new GDate('planning_periode', 'date_fin', 10, TRUE, NULL);
		$this->fields['duree'] = new Gtime('planning_periode', 'duree', 12, TRUE, NULL);
		$this->fields['duree_details'] = new GString('planning_periode', 'duree_details', 20, TRUE, NULL);
		$this->fields['titre'] = new GString('planning_periode', 'titre', 255, TRUE, NULL);
		$this->fields['notes'] = new GString('planning_periode', 'notes', 65000, TRUE, NULL);
		$this->fields['lien'] = new GString('planning_periode', 'lien', 65000, TRUE, NULL);
		$this->fields['statut_tache'] = new GString('planning_periode', 'statut_tache', 10, TRUE, NULL);
		$this->fields['lieu_id'] = new GString('planning_periode', 'lieu_id', 20, TRUE, NULL);
		$this->fields['ressource_id'] = new GString('planning_periode', 'ressource_id', 20, TRUE, NULL);
		$this->fields['livrable'] = new GEnum('planning_periode', 'livrable', array('oui','non'), FALSE, 'non');
		$this->fields['fichiers'] = new GString('planning_periode', 'fichiers', 65000, TRUE, NULL);
		$this->fields['createur_id'] = new GString('planning_periode', 'createur_id', 20, TRUE, NULL);
		$this->fields['date_creation'] = new GDatetime('planning_periode', 'date_creation', 19, TRUE, NULL);
		$this->fields['modifier_id'] = new GString('planning_periode', 'modifier_id', 20, TRUE, NULL);
		$this->fields['date_modif'] = new GDatetime('planning_periode', 'date_modif', 19, TRUE, NULL);
		$this->fields['custom'] = new GString('planning_periode', 'custom', 255, TRUE, NULL);
		$this->fields['pause'] = new Gtime('planning_periode', 'pause', 12, TRUE, NULL);
		$this->fields['duree_reelle'] = new GFloat('planning_periode', 'duree_reelle', 15, TRUE, NULL);

		$this->primaryKeys = array('periode_id');

		parent::__construct();
	}

	
	public function db_save($updateData = array(), $insertData = array(), $import=false)
	{
		if (!$this->saved && !$import) {
			$creation = true;
			if(defined("CONFIG_SEMAPHORE_ACTIVATED") && CONFIG_SEMAPHORE_ACTIVATED == '1'){
				$mutex = new Mutex();
				$mutex->init(abs(crc32($cfgDatabase . $this->table)), BASE . '/../mutex_file.txt');
				$mutex->acquire();
			}

			$result = db_query('SELECT MAX(' . $this->primaryKeys[0] . ') AS max FROM ' . $this->table);
			$new = 1;
			while ($var = db_fetch_array($result)) {
				$new = $var['max'] + 1;
			}
			$this->data[$this->primaryKeys[0]] = $new;

			if(isset($_SESSION['user_id'])) {
				$this->createur_id = $_SESSION['user_id'];
				$this->date_creation = date('Y-m-d H:i:s');
			}
		}else {
			if($this->date_creation != date('Y-m-d H:i:s')) {
				if(isset($_SESSION['user_id'])){
					$this->modifier_id = $_SESSION['user_id'];
					$this->date_modif = date('Y-m-d H:i:s');
				}
			}
		}
		if(defined('CONFIG_DEFAULT_PERIOD_LINK') && $this->lien == CONFIG_DEFAULT_PERIOD_LINK) {
			$this->lien = NULL;
		}
		$return = parent::db_save($updateData, $insertData);
		
		if (isset($creation) && defined("CONFIG_SEMAPHORE_ACTIVATED") && CONFIG_SEMAPHORE_ACTIVATED == '1') {
			$mutex->release();
		}
		$this->updateBudgetsProjet();

		return $return;
	}

	public function getSmartyData()
	{
		$data = parent::getSmartyData();
		$data['notes_xajax'] = str_replace(array("\r\n", "\n"), array("�", "�"), trim((string) $data['notes']));
		if(strlen((string)$this->duree_details) == 17) {
			$tmp = explode(';', $this->duree_details);
			$data['duree_details_heure_debut'] = $tmp[0];
			$data['duree_details_heure_fin'] = $tmp[1];
		}
		return $data;
	}

	public function getHeureDebutFin() {
		if(strlen($this->duree_details) == 17) {
			$tab = explode(';', $this->duree_details);
			return array('duree_details_heure_debut' => $tab[0], 'duree_details_heure_fin' => $tab[1]);
		}
		return NULL;
	}

	// repete la planif selon la p�riode demand�e : jour, semaine, mois
	// on repete la tache pendant 7 ans
	public function repeter($repetition, $dateFinRepetition, $dureeDetails, $nbRepetition, $jourSemaine, $exceptionRepetition) {
		@set_time_limit(180);
		$DAYS_INCLUDED1 = explode(',', CONFIG_DAYS_INCLUDED);
		foreach($DAYS_INCLUDED1 as $a){$DAYS_INCLUDED[$a]=$a;}
		$joursFeries = getjoursFeries();
		if(!in_array($repetition, $this->repetitionPossible)) {
			return false;
		}

		// Creation des objets date (plus facile � manipuler)
		$tmpDate = new DateTime();
		$tmpDate->setDate((int)substr($this->date_debut,0,4), (int)substr($this->date_debut,5,2), (int)substr($this->date_debut,8,2));
		$dateFin = new DateTime();
		$dateFin->setDate((int)substr($dateFinRepetition,0,4), (int)substr($dateFinRepetition,5,2), (int)substr($dateFinRepetition,8,2));
		// S'il existe une date de fin de p�riode
		if ($this->date_fin<>"")
		{
			$dateFinPeriode = new DateTime();
			$dateFinPeriode->setDate((int)substr($this->date_fin,0,4), (int)substr($this->date_fin,5,2), (int)substr($this->date_fin,8,2));
		}
		
		// Calcul du nombre de jours
		if($dateFinRepetition > $this->date_debut){
			if ($this->date_fin<>"")
			{
			 $nbJours = getNbJours($this->date_debut,$dateFinPeriode->format('Y-m-d'));
			}else $nbJours = getNbJours($this->date_debut,$dateFin->format('Y-m-d'));
		}else{
			$nbJours = getNbJours($dateFin->format('Y-m-d'),$this->date_debut );
		}

		// R�cup�ration des donnees
		$data = $this->getData();
		$idPeriode = $data['periode_id'];
		$idUser = $data['user_id'];
		$data['periode_id'] = 0;
		$data['saved'] = 0;
		$compteurJoursSautes = 0;
		$periode_tosave=array();
		
		// Si la date de fin est sup�rieure � la date de d�but
		if($dateFin > $tmpDate) {
			while ($tmpDate < $dateFin) {
				// Gestion du d�calage arri�re en cas de jour f�ri� ou non travaill�
				if ($repetition != 'jour') {
					// Au cas o� on d�cale et que l'on tombe sur la semaine suivante (pour �viter la superposition de t�che)
					if ($repetition == 'semaine' && $compteurJoursSautes >= 7 && $nbRepetition==1)
					{
					$compteurJoursSautes=0;
					}else
					{
					while ($compteurJoursSautes > 0) {
						// on redecale la date dans le pass� si la precedente iteration tombait sur un f�ri� ou non travaill�
						// � ne pas faire pour repetition quotidienne
						$tmpDate->modify("-1 day");
						$compteurJoursSautes--;
					}
					}
				}

				// Gestion de la r�p�tition
				if ($repetition == 'jour') {
					$tmpDate->modify("+$nbRepetition day");
				} elseif ($repetition == 'semaine') {
				    // positionnement sur le jour de la semaine souhait�
					if ($tmpDate->format('w') != $jourSemaine)
					{
					 while ($tmpDate->format('w') != $jourSemaine)
					 {
					  $tmpDate->modify("+1 day");
					 }
					}	
					$tmpDate->modify("+$nbRepetition week");
				} elseif ($repetition == 'mois') {
					$tmpDate->modify("+$nbRepetition month");
				}

				// Si on est en r�p�tition hebdo, on recherche le prochain jour de la semaine souhait�
				// Cas o� si on croise une exception, on d�cale jusqu'� la prochaine date valide
				if ($exceptionRepetition == 1)
				{
				   // Si c'est une r�p�tition par semaine, on filtre aussi sur le jour de semaine
				   if ($repetition == 'semaine') 
				    {
					 while (($tmpDate->format('w') != $jourSemaine) || !isset($DAYS_INCLUDED[$tmpDate->format('w')]) || array_key_exists($tmpDate->format('Y-m-d'), $joursFeries)) {
					 $compteurJoursSautes++;
				 	 $tmpDate->modify("+1 day");
				     }
					}else
				    {
				     // on decale pour eviter les jours non-inclus (Weekend) et jours f�ri�s, mais on stocke le nombre de jours saut�s pour repartir sur le bon calcul ensuite
				      while (!isset($DAYS_INCLUDED[$tmpDate->format('w')]) || array_key_exists($tmpDate->format('Y-m-d'), $joursFeries)) {
					  $compteurJoursSautes++;
					  $tmpDate->modify("+1 day");
				      }
				    }
                // Cas o� si on croise une exception, on n'ajoute pas
				}elseif ($exceptionRepetition == 2)
                {
				   // Si c'est une r�p�tition par semaine, on filtre aussi sur le jour de semaine
				   if ($repetition == 'semaine') 
				    {				
					 while ($tmpDate->format('w') != $jourSemaine || !isset($DAYS_INCLUDED[$tmpDate->format('w')]) || array_key_exists($tmpDate->format('Y-m-d'), $joursFeries)) {
				 	 $tmpDate->modify("+1 day");
				     }
					}elseif ($repetition == 'jour')
				    {
				     // on decale pour eviter les jours non-inclus (Weekend) et jours f�ri�s, mais on stocke le nombre de jours saut�s pour repartir sur le bon calcul ensuite
				      while (!isset($DAYS_INCLUDED[$tmpDate->format('w')]) || array_key_exists($tmpDate->format('Y-m-d'), $joursFeries)) {
					  $tmpDate->modify("+$nbRepetition day");
				      }
				    }else
					{
				     // on decale pour eviter les jours non-inclus (Weekend) et jours f�ri�s, mais on stocke le nombre de jours saut�s pour repartir sur le bon calcul ensuite
				      while (!isset($DAYS_INCLUDED[$tmpDate->format('w')]) || array_key_exists($tmpDate->format('Y-m-d'), $joursFeries)) {
					  $tmpDate->modify("+$nbRepetition month");
				      }					
					}
				// Cas o� si on croise une exception, on ajoute quand m�me
				}else
				{
				   if ($repetition == 'semaine') 
				    {				
					 // On cherche le prochain jour de semaine
					 while ($tmpDate->format('w') != $jourSemaine) {
					 $compteurJoursSautes++;
				 	 $tmpDate->modify("+1 day");
				     }
					}else
				    {
					 // on ne fait rien
				    }
				}
			
				// Enregistrement temporaire de la p�riode trouv�e
				$debut=$tmpDate->format('Y-m-d');
				if (!is_null($this->date_fin)) {
					$fin = calculerDateFin($debut, $nbJours);
				} else{
					$fin = NULL;
				}	
				if ($this->parent_id) {
					$parent = $this->parent_id;
				} else {
					$parent = $this->periode_id;
				}
				if ($tmpDate < $dateFin)
				{
				 $periode_tosave[]=array('debut'=>$debut,'fin'=>$fin,'parent'=>$parent);
				}
				}
		}else{
			while ($dateFin < $tmpDate) {
				
				// Gestion du d�calage arri�re en cas de jour f�ri� ou non travaill�
				if ($repetition != 'jour') {
					// Au cas o� on d�cale et que l'on tombe sur la semaine suivante (pour �viter la superposition de t�che)
					if ($repetition == 'semaine' && $compteurJoursSautes >= 7 && $nbRepetition==1)
					{
					$compteurJoursSautes=0;
					}else
					{
					while ($compteurJoursSautes > 0) {
						// on redecale la date dans le pass� si la precedente iteration tombait sur un f�ri� ou non travaill�
						// � ne pas faire pour repetition quotidienne
						$tmpDate->modify("+1 day");
						$compteurJoursSautes--;
					}
					}
				}
			
				// Gestion de la r�p�tition
				if ($repetition == 'jour') {
					$tmpDate->modify("-$nbRepetition day");
				} elseif ($repetition == 'semaine') {
					$tmpDate->modify("-$nbRepetition week");
				} elseif ($repetition == 'mois') {
					$tmpDate->modify("-$nbRepetition month");
				}
				
				// Si on est en r�p�tition hebdo, on recherche le prochain jour de la semaine souhait�
				// Cas o� si on croise une exception, on d�cale jusqu'� la prochaine date valide
				if ($exceptionRepetition == 1)
				{
				   // Si c'est une r�p�tition par semaine, on filtre aussi sur le jour de semaine
				   if ($repetition == 'semaine') 
				    {				
					 while ( ($tmpDate->format('w') != $jourSemaine) || !isset($DAYS_INCLUDED[$tmpDate->format('w')]) || array_key_exists($tmpDate->format('Y-m-d'), $joursFeries)) {
					 $compteurJoursSautes++;
				 	 $tmpDate->modify("-1 day");
				     }
					}else
				    {
				     // on decale pour eviter les jours non-inclus (Weekend) et jours f�ri�s, mais on stocke le nombre de jours saut�s pour repartir sur le bon calcul ensuite
				      while (!isset($DAYS_INCLUDED[$tmpDate->format('w')]) || array_key_exists($tmpDate->format('Y-m-d'), $joursFeries)) {
					  $compteurJoursSautes++;
					  $tmpDate->modify("-1 day");
				      }
				    }
                // Cas o� si on croise une exception, on n'ajoute pas
				}elseif ($exceptionRepetition == 2)
                {
				   // Si c'est une r�p�tition par semaine, on filtre aussi sur le jour de semaine
				   if ($repetition == 'semaine') 
				    {				
					 while ( ($tmpDate->format('w') != $jourSemaine) || !isset($DAYS_INCLUDED[$tmpDate->format('w')]) || array_key_exists($tmpDate->format('Y-m-d'), $joursFeries)) {
				 	 $tmpDate->modify("-1 day");
				     }
					}elseif ($repetition == 'jour')
				    {
				     // on decale pour eviter les jours non-inclus (Weekend) et jours f�ri�s, mais on stocke le nombre de jours saut�s pour repartir sur le bon calcul ensuite
				      while (!isset($DAYS_INCLUDED[$tmpDate->format('w')]) || array_key_exists($tmpDate->format('Y-m-d'), $joursFeries)) {
					  $tmpDate->modify("-$nbRepetition day");
				      }
				    }else
					{
				     // on decale pour eviter les jours non-inclus (Weekend) et jours f�ri�s, mais on stocke le nombre de jours saut�s pour repartir sur le bon calcul ensuite
				      while (!isset($DAYS_INCLUDED[$tmpDate->format('w')]) || array_key_exists($tmpDate->format('Y-m-d'), $joursFeries)) {
					  $tmpDate->modify("-$nbRepetition month");
				      }					
					}
				// Cas o� si on croise une exception, on ajoute quand m�me
				}else
				{
				   if ($repetition == 'semaine') 
				    {				
					 // On cherche le prochain jour de semaine
					 while ($tmpDate->format('w') != $jourSemaine) {
					 $compteurJoursSautes++;
				 	 $tmpDate->modify("-1 day");
				     }
					}else
				    {
					 // on ne fait rien
				    }
				}
				
				// Enregistrement temporaire de la p�riode trouv�e
				$debut=$tmpDate->format('Y-m-d');
				if (!is_null($this->date_fin)) {
					$fin = calculerDateFin($debut, $nbJours);
				} else{
					$fin = NULL;
				}				
				if ($this->parent_id) {
					$parent = $this->parent_id;
				} else {
					$parent = $this->periode_id;
				}
				$periode_tosave[]=array('debut'=>$debut,'fin'=>$fin,'parent'=>$parent);				
			}
		}

		// Post-traitement, v�rification et enregistrement de toutes les p�riodes trouv�es
		// V�rification de l'utilisation de la ressource
		if (CONFIG_SOPLANNING_OPTION_RESSOURCES == 1 && (!is_null($data['ressource_id']))) {
			$dates_erreurs=array();
			foreach ($periode_tosave as $p) {
				if(!checkConflitRessource($data['ressource_id'],$p['debut'],$p['fin'],$dureeDetails,$idUser,$idPeriode,$data['link_id'])) {
					$dates_erreurs[]=sqldate2userdate($p['debut']);
				}
			}
			if (!empty($dates_erreurs)) {
				$retour="";
				$sep="";
				foreach($dates_erreurs as $d) {
					if (!empty($retour)) $sep=", ";
					$retour.=$sep.$d;
				}
				return $retour;
			}
		}
		// Post-traitement, v�rification et enregistrement de toutes les p�riodes trouv�es
		// V�rification de l'utilisation du lieu
		if (CONFIG_SOPLANNING_OPTION_LIEUX == 1 && (!is_null($data['lieu_id']))) {
			$dates_erreurs=array();
			foreach ($periode_tosave as $p) {		
				if(!checkConflitLieu($data['lieu_id'],$p['debut'],$p['fin'],$dureeDetails,$idUser,$idPeriode,$data['link_id'])) {
					$dates_erreurs[]=sqldate2userdate($p['debut']);
				}			
			}
			if (!empty($dates_erreurs)) {
				$retour="";
				$sep="";
				foreach($dates_erreurs as $d) {
					if (!empty($retour)) $sep=", ";
					$retour.=$sep.$d;
				}
				return $retour;
			}
		}	
		// D�doublonnage de la liste des p�riodes
		$periode_tosave = array_map("unserialize", array_unique(array_map("serialize", $periode_tosave)));

		// Enregistrement
		foreach ($periode_tosave as $p)
		{
				$newPeriode = new Periode();
				$newPeriode->setData($data);
				$newPeriode->parent_id = $p['parent'];
				$newPeriode->date_debut = $p['debut'];
				$newPeriode->date_fin = $p['fin'];

				// on cherche une tache pour le m�me jour avec le m�me parent pour r�cup�rer le link_id en cas d'assignation multiple
				$autreParent = new Periode();
				$sql = "SELECT *
						FROM planning_periode
						WHERE parent_id <> " . val2sql($this->parent_id) . " AND
						link_id = " . val2sql($this->link_id) . "
						LIMIT 1
				";

				$autreParent->db_loadSQL($sql);

				if($autreParent->isSaved()){
					$recherche = new Periode();
					$sql = "SELECT *
							FROM planning_periode
							WHERE parent_id = " . val2sql($autreParent->periode_id) . " AND
							date_debut = " . val2sql($newPeriode->date_debut) . "
							LIMIT 1
					";
					// @file_put_contents(BASE . '/../debug.txt', $sql . "\r\n", FILE_APPEND);
					$recherche->db_loadSQL($sql);

					if($recherche->isSaved()){
						$newPeriode->link_id = $recherche->link_id;
					} else{					
						$newPeriode->link_id = uniqid(mt_rand());
					}
				} else{
					$newPeriode->link_id = uniqid(mt_rand());
				}

				$newPeriode->db_save();
		}
		return true;
	}

	// renvoie vrai si la tache est parente (a des filles), ou est la fille d'une tache
	public function estFilleOuParente() {
		if(!is_null($this->parent_id)) {
			return true;
		}
		$periodes = new GCollection('Periode');
		$periodes->db_load(array('parent_id', '=', $this->periode_id));
		if($periodes->getCount() > 0) {
			return true;
		}
		return false;
	}

	public function updateMultiUserOccurences($link_id,$old_link_id,$users)
	{
		// Recherche des users dans le cas des t�ches li�es pour conna�tre les nouveaux users
		$users_periode=array();
		$users_periode_new=array();
		$user_periode_remove=array();
		
		// Si on transforme une t�che monouser en multiuser, on r�enregistre toutes les t�ches (m�me les r�p�titives) pour prendre en compte l'ajout du link_id
		if (is_null($old_link_id))
		{
			$periodes_a_transformer = new Gcollection('Periode');
			$periodes_a_transformer->db_loadSQL('SELECT * FROM planning_periode WHERE ( link_id = '. val2sql($link_id).' or parent_id="'.$this->parent_id.'")');
			while($pat = $periodes_a_transformer->fetch()) {
				$data = $pat->getData();
				$data['saved']=1;
				$new_periode = new Periode();
				$new_periode->setData($data);
				$new_periode->link_id=$link_id;
				$new_periode->db_save();
			}
		}
		if (!is_null($link_id))
		{
			$periodes_users = new Gcollection('Periode');
			$periodes_users->db_loadSQL('SELECT distinct(user_id) FROM planning_periode WHERE ( link_id = '. val2sql($link_id).')');
			while($pu = $periodes_users->fetch()) {
				$users_periode[]=$pu->user_id;
			}
			// Recherche des users ajout�s
			foreach($users as $user_add) {
				if (!in_array($user_add,$users_periode))
				{
					$user_periode_new[]=$user_add;
				}else $last_user=$user_add;
			}
			// Recherche des users supprim�s
			foreach($users_periode as $user_remove) {
				if (!in_array($user_remove,$users))
				{
					$user_periode_remove[]=$user_remove;
				}
			}
		// Insertion des nouveaux users
		if (count($user_periode_new)>0 && !empty($last_user))
		{
			$periodes_a_cloner = new Gcollection('Periode');
			$periodes_a_cloner->db_loadSQL('SELECT * FROM planning_periode WHERE ( link_id = '. val2sql($link_id).' and user_id="'.$last_user.'")');
			while($pac = $periodes_a_cloner->fetch()) {
				foreach($user_periode_new as $new_user)
				{
					$data = $pac->getData();
					$data['saved']=0;
					$new_periode = new Periode();
					$new_periode->setData($data);
					$new_periode->user_id=$new_user;
					$new_periode->periode_id=NULL;
					$new_periode->db_save();
				}
			}
		}
		// Suppression des p�riodes des users supprim�s
		if (count($user_periode_remove)>0)
		{
			foreach($user_periode_remove as $user_remove)
			{
				$periode_a_supprimer = new Gcollection('Periode');
				$periode_a_supprimer->db_loadSQL('SELECT * FROM planning_periode WHERE ( link_id = '. val2sql($link_id).' and user_id="'.$user_remove.'")');
				$periode_a_supprimer->db_delete();
			}
		}
		}
	}
	
	// applique les param�tres au parent et toutes les soeurs
	public function updateOcurrences($nbJoursDecalDest = '',$copie='false') {
		$periodes = new Gcollection('Periode');
		if(is_null($this->parent_id)) {
			// si c'est le parent alors on s�lectionne tout les enfants
			$periodes->db_loadSQL('SELECT * FROM planning_periode WHERE parent_id = ' . val2sql($this->periode_id) . ' OR link_id = ' . val2sql($this->link_id)); // modif suppression:  . ' AND date_debut > ' . val2sql($this->date_debut);
		}else{
			$periodes->db_loadSQL('SELECT * FROM planning_periode WHERE ( link_id = '. val2sql($this->link_id).' OR (parent_id IS NOT NULL AND parent_id = ' . val2sql($this->parent_id) . ' OR periode_id = ' . val2sql($this->parent_id) . ' OR parent_id = ' . val2sql($this->periode_id) . ') )  AND periode_id <> ' . val2sql($this->periode_id));
		}
		//si la periode appellante vient d'�tre supprim�e, on verifie qu'il existe plusieurs p�riode r�currentes, si non, on met � jour le parent_id � NULL de la p�riode restante
		if($periodes->getCount() == 1 && !$this->saved ) {
			if($periode = $periodes->fetch()) {
				$periode->parent_id = NULL;
				$periode->db_save();
			}
			return;
		}
		//on gere le cas des deleteAfter et deleteAvant sur periode recurrente restante
		if($periodes->getCount() == 0 && $this->isSaved()) {
			$this->parent_id = NULL;
			$this->db_save();
			return;
		}
		$nbJoursDecalNeg = $nbJoursDecalDest*-1; //on inverse le signe si negatif car le calcul de date ne prend que des nombres positifs
		
		while($periode = $periodes->fetch()) {
		// modif calcul du nombre de jour de la p�riode pour report sur la nouvelle date
		if (!is_null($periode->date_fin)) {
			if ($nbJoursDecalDest>0){
				$periode->date_debut = calculerDateFin($periode->date_debut, $nbJoursDecalDest);
				$periode->date_fin = calculerDateFin($periode->date_fin, $nbJoursDecalDest);
			} elseif ($nbJoursDecalDest<0) {
				$periode->date_debut = calculerDateDebut($periode->date_debut, $nbJoursDecalNeg);
				$periode->date_fin = calculerDateDebut($periode->date_fin, $nbJoursDecalNeg);
			}
		} else{
			if ($nbJoursDecalDest>0){
				$periode->date_debut = calculerDateFin($periode->date_debut, $nbJoursDecalDest);
			}elseif ($nbJoursDecalDest<0) {
				$periode->date_debut = calculerDateDebut($periode->date_debut, $nbJoursDecalNeg);
			}
		}
			if ($copie=='true')
			{
				$periode->user_id = $this->user_id ;
			}
			$periode->projet_id = $this->projet_id;
			$periode->titre = $this->titre;
			//$periode->link_id = $this->link_id;
			$periode->statut_tache = $this->statut_tache;
			$periode->notes = $this->notes;
			$periode->lien = $this->lien;
			$periode->fichiers = $this->fichiers;			
			$periode->lieu_id = $this->lieu_id;
			$periode->ressource_id = $this->ressource_id;
			$periode->custom = $this->custom;
			$periode->modifier_id = $_SESSION['user_id'];
			$periode->date_modif = date('Y-m-d H:i:s');
			$periode->db_save();
		}

	}

	// supprime la tache, sa mere et ses soeurs
	public function db_deleteAll() {
		if(is_null($this->parent_id)) {
			db_query('DELETE FROM planning_periode where parent_id = ' . val2sql($this->periode_id));
		}
		else {
			db_query('DELETE FROM planning_periode where parent_id = ' . val2sql($this->parent_id));
			db_query('DELETE FROM planning_periode where periode_id = ' . val2sql($this->parent_id));
		}
		if(!is_null($this->link_id)) {
			db_query('DELETE FROM planning_periode where link_id = ' . val2sql($this->link_id));
		}
		return true;
	}

	// modif ajout supprime la tache et ses soeurs apres une date
	public function db_deleteAllApres() {
		if(is_null($this->parent_id)) {
			db_query('DELETE FROM planning_periode where parent_id = ' . val2sql($this->periode_id) . ' AND date_debut > ' . val2sql($this->date_debut)) ;
		} else {
			db_query('DELETE FROM planning_periode where ( periode_id = ' . val2sql($this->parent_id) . ' OR  parent_id = ' . val2sql($this->parent_id) . ') AND date_debut > ' . val2sql($this->date_debut)) ;
		}
		return true;
	}
	// modif ajout supprime la tache et ses soeurs avant une date
	public function db_deleteAllAvant() {
		if(is_null($this->parent_id)) {
			db_query('DELETE FROM planning_periode where parent_id = ' . val2sql($this->periode_id) . ' AND date_debut < ' . val2sql($this->date_debut)) ;
		} else {
			db_query('DELETE FROM planning_periode where ( periode_id = ' . val2sql($this->parent_id) . ' OR  parent_id = ' . val2sql($this->parent_id) . ') AND date_debut < ' . val2sql($this->date_debut)) ;
		}
		return true;
	}

	public function prochaineOccurence() {
		$sql = 'SELECT * FROM planning_periode WHERE (parent_id = ' . val2sql($this->periode_id) . ' OR (parent_id IS NOT NULL AND parent_id = ' . val2sql($this->parent_id) . ' AND periode_id <> ' . val2sql($this->periode_id) . ')) AND date_debut > ' . val2sql($this->date_debut) . ' ORDER BY date_debut LIMIT 1';
		$periode = new Periode();
		if(!$periode->db_loadSQL($sql)) {
			return false;
		}
		return $periode->date_debut;
	}

	public function derniereOccurence() {
		$sql = 'SELECT * FROM planning_periode WHERE (parent_id = ' . val2sql($this->periode_id) . ' OR (parent_id IS NOT NULL AND parent_id = ' . val2sql($this->parent_id) . ' AND periode_id <> ' . val2sql($this->periode_id) . ')) AND date_debut > ' . val2sql($this->date_debut) . ' ORDER BY date_debut DESC LIMIT 1';
		$periode = new Periode();
		if(!$periode->db_loadSQL($sql)) {
			return false;
		}
		return $periode->date_debut;
	}
	
	// envoi un mail en cas de creation/modification de tache
	public function envoiNotification($action = false, $repetition = false) {

		$userAssigne = new User();
		if(!$userAssigne->db_load(array('user_id', '=', $this->user_id))) {
			return false;
		}
		$projet = new Projet();
		if(!$projet->db_load(array('projet_id', '=', $this->projet_id))) {
			return false;
		}
		if($userAssigne->notifications == 'oui' && !is_null($userAssigne->email)) {
			$smarty = new MySmarty();
			
			$status = new GCollection('Status');
		    $status->db_load(array('affichage', 'IN', array('t', 'tp'),'status_id','=',$this->statut_tache));
			$nom_status=$status->getSmartyData();
			$smarty->assign('status', $nom_status[0]['nom']);
			
			$smarty->assign('periode', $this->getData());
			$smarty->assign('projet', $projet->getData());

			$lieu = new Lieu();
			if(!is_null($this->lieu_id)){
				$lieu->db_load(array('lieu_id', '=', $this->lieu_id));
				$smarty->assign('lieu', $lieu->getData());
			}

			$ressource = new Ressource();
			if(!is_null($this->ressource_id)){
				$ressource->db_load(array('ressource_id', '=', $this->ressource_id));
				$smarty->assign('ressource', $ressource->getData());
			}
			
			if(strlen($this->duree_details) == 17) {
				$tmp = explode(';', $this->duree_details);
				$smarty->assign('heure_debut',$tmp[0]);
				$smarty->assign('heure_fin',$tmp[1]);
			}
			
			if (!is_null($this->fichiers)) {
					$fichiers = explode(";", $this->fichiers);
					$smarty->assign('fichiers', $fichiers);
			} else {
					$smarty->assign('fichiers', null);
			}

			if(CONFIG_SOPLANNING_URL != "") {
				$smarty->assign('lienTache', $this->getTaskLink());
				$smarty->assign('base', CONFIG_SOPLANNING_URL);
			}
			
			$createur = new User();
			$createur->db_load(array('user_id', '=', $this->createur_id));
			$smarty->assign('createur', $createur->getData());
			if($action == 'delete') {
				$sujet = CONFIG_SOPLANNING_TITLE . ' - ' . $smarty->getConfigVars('mail_supprTache_sujet') . ($this->titre != '' ? ' - ' . $this->titre : '');
				$corps = $smarty->getHtml('mail_suppr_tache.tpl');
			} else {
				if($repetition !== FALSE && $repetition !== "" && !in_array($repetition, $this->repetitionPossible)) {
					return false;
				}
				$smarty->assign('repetition', $repetition);

				if($action == 'creation') {
					$sujet = CONFIG_SOPLANNING_TITLE . ' - ' . $smarty->getConfigVars('mail_creationTache_sujet') . ($this->titre != '' ? ' - ' . $this->titre : '');
					$corps = $smarty->getHtml('mail_creation_tache.tpl');
				} else {
					$oldPeriode = new Periode();
					$oldPeriode->db_load(array('periode_id', '=', $this->periode_id));
					if(strlen($oldPeriode->duree_details) == 17) {
						$tmp = explode(';', $oldPeriode->duree_details);
						$smarty->assign('heure_debut_old',$tmp[0]);
						$smarty->assign('heure_fin_old',$tmp[1]);
					}
					$status = new GCollection('Status');
					$status->db_load(array('affichage', 'IN', array('t', 'tp'),'status_id','=',$oldPeriode->statut_tache));
					$nom_status_old=$status->getSmartyData();
					$smarty->assign('oldStatus', $nom_status_old[0]['nom']);
					$smarty->assign('oldPeriode', $oldPeriode->getData());
					$oldProjet = new Projet();
					$oldProjet->db_load(array('projet_id', '=', $oldPeriode->projet_id));
					$smarty->assign('oldProjet', $oldProjet->getData());

					$oldLieu = new Lieu();
					if(!is_null($oldPeriode->lieu_id)){
						$oldLieu->db_load(array('lieu_id', '=', $oldPeriode->lieu_id));
						$smarty->assign('oldLieu', $oldLieu->getData());
					}

					$oldRessource = new Ressource();
					if(!is_null($oldPeriode->ressource_id)){
						$oldRessource->db_load(array('ressource_id', '=', $oldPeriode->ressource_id));
						$smarty->assign('oldRessource', $oldRessource->getData());
					}

					$sujet = CONFIG_SOPLANNING_TITLE . ' - ' . $smarty->getConfigVars('mail_modifTache_sujet')  . ($this->titre != '' ? ' - ' . $this->titre : '');
					$corps = $smarty->getHtml('mail_modif_tache.tpl');
				}
			}
			$mail = new Mailer($userAssigne->email, $sujet, $corps, true);
			try {
				$result = $mail->send();
			} catch (phpmailerException $e) {
				echo 'error while sending the email :';
				print_r($e);
			}
			return $result;
		}
		return true;
	}


	public function getAPIData(){
		$data = array();
		$data['task_id'] = valueAPI($this->periode_id);
		$data['project_id'] = valueAPI($this->projet_id);
		$data['user_id'] = valueAPI($this->user_id);
		$data['link_id'] = valueAPI($this->link_id);
		$data['status_id'] = valueAPI($this->statut_tache);
		$data['start_date'] = valueAPI($this->date_debut);
		$data['end_date'] = valueAPI($this->date_fin);
		$heures = $this->getHeureDebutFin();
		$data['start_hour'] = valueAPI((!is_null($heures) ? $heures['duree_details_heure_debut'] : ''));
		$data['end_hour'] = valueAPI((!is_null($heures) ? $heures['duree_details_heure_fin'] : ''));
		$data['duration'] = valueAPI($this->duree);
		$data['title'] = valueAPI($this->titre);
		$data['comment'] = valueAPI($this->notes);
		$data['link'] = valueAPI($this->lien);
		$data['resource_id'] = valueAPI($this->ressource_id);
		$data['place_id'] = valueAPI($this->lieu_id);
		$data['milestone'] = valueAPI(($this->livrable == 'oui' ? 'yes' : 'non'));
		$data['creator_id'] = valueAPI($this->createur_id);
		$data['creation_date'] = valueAPI($this->date_creation);
		$data['last_modifier_id'] = valueAPI($this->modifier_id);
		$data['modification_date'] = valueAPI($this->date_modif);
		$data['custom_field'] = valueAPI($this->custom);
		$data['pause'] = valueAPI($this->pause);
		$data['real_duration'] = valueAPI($this->duree_reelle);
		return $data;
	}

	public function putAPI($task_id = '', $user_id = '', $project_id = '', $link_id = '', $start_date = '', $end_date = '', $start_time = '', $end_time = '', $duration = '', $status_id = '', $title = '', $comment = '', $link = '', $resource_id = '', $place_id = '', $milestone = '', $custom_field = '', $creator_id = '', $pause = '', $real_duration = ''){

		if(strlen(trim($task_id)) > 0 && (!is_numeric(trim($task_id)) || !$this->db_load(array('periode_id', '=', trim($task_id))))) {
			throw new BadInputException('task_id unknown in database');
		}
		$user = new User();
		if(trim($user_id) == '' || !$user->db_load(array('user_id', '=', trim($user_id)))){
			throw new BadInputException('user_id not existing in users list');
		}
		$this->user_id = trim($user_id);

		$projet = new Projet();
		if(trim($project_id) == '' || !$projet->db_load(array('projet_id', '=', trim($project_id)))){
			throw new BadInputException('project_id not existing in projects list');
		}
		$this->projet_id = trim($project_id);

		if(trim($link_id) == ''){
			if(!$this->isSaved()){
				$this->link_id = uniqid(mt_rand());
			}
		} else {
			$this->link_id = trim($link_id);
		}
		if(strlen(trim($start_date)) == '0' || !controlDateSql($start_date)){
			throw new BadInputException('Start date not valid : ' . $start_date . ' (yyyy-mm-dd required)');
		}
		$this->date_debut = trim($start_date);

		if(strlen(trim($end_date)) != '0'){
			if(!controlDateSql($end_date)){
				throw new BadInputException('End date not valid : ' . $end_date . ' (yyyy-mm-dd required)');
			}
			if($start_date > $end_date){
				throw new BadInputException('Start date cant be greater than end date');
			}
			$this->date_fin = trim($end_date);
			$this->duree = NULL;
			$this->duree_details = NULL;
		} elseif (strlen(trim($start_time)) != '0') {
			if(!controlTimeSql($start_time)){
				throw new BadInputException('Start time not valid : ' . $start_time . ' (hh:mm)');
			} elseif (strlen(trim($end_time)) == '0' || !controlTimeSql($end_time)) {
				throw new BadInputException('End time required and not valid : ' . $end_time . ' (hh:mm)');
			} elseif ($end_time < $start_time) {
				throw new BadInputException('Start time cant be greater than end time');
			}
			$this->duree_details = trim($start_time) . ':00;' . trim($end_time) . ':00';
			$this->duree = soustraireDuree($start_time, $end_time);
			$this->date_fin = NULL;
		} elseif (strlen(trim($duration)) != '0') {
			if(trim($duration) == 'AM'){
				$this->duree = CONFIG_DURATION_PM . ':00';
				if (strlen(CONFIG_DURATION_PM) < 8) {
					$this->duree = '0' . $this->duree;
				}
				$this->duree_details = 'AM';
				$this->date_fin = NULL;
			} elseif (trim($duration) == 'PM') {
				$this->duree = CONFIG_DURATION_AM . ':00';
				if (strlen(CONFIG_DURATION_AM) < 8) {
					$this->duree = '0' . $this->duree;
				}
				$this->duree_details = 'PM';
				$this->date_fin = NULL;
			} elseif (controlTimeSql($duration)) {
				$this->duree = $duration . ':00';
				$this->duree_details = 'duree';
				$this->date_fin = NULL;
			} else{
				throw new BadInputException('Duration not valid : "AM", "PM", or duration (hh:mm)');
			}

		} else{
			throw new BadInputException('Please fill at least one of those values : end date, start and end time, duration');
		}

		if($pause != '' && !controlTimeSql($pause)) {
			throw new BadInputException('Pauqe duration not valid (hh:mm)');
		}
		$this->pause = (trim($pause) != '' ? trim($pause) . ':00' : NULL);

		$this->duree_reelle = (trim($real_duration) != '' ? trim($real_duration) : NULL);

		$status = new Status();
		if(trim($status_id) == '' || !$status->db_load(array('status_id', '=', trim($status_id)))){
			throw new BadInputException('status_id not existing in statutes list');
		}
		$this->statut_tache = trim($status_id);

		if(strlen(trim($title)) > 255){
			throw new BadInputException('Title length not valid (max 255)');
		}
		$encoding = mb_detect_encoding($title);
		$this->titre = (trim($title) != '' ? trim(($encoding == 'UTF-8' ? utf8_decode($title) : $title)) : NULL);

		if(strlen(trim($comment)) > 65000){
			throw new BadInputException('Comment length not valid (max 65000)');
		}
		$encoding = mb_detect_encoding($comment);
		$this->notes = (trim($comment) != '' ? trim(($encoding == 'UTF-8' ? utf8_decode($comment) : $comment)) : NULL);

		if(strlen(trim($link)) > 65000){
			throw new BadInputException('Link length not valid (max 65000)');
		}
		$this->lien = (trim($link) != '' ? trim($link) : NULL);

		$resource = new Ressource();
		if(trim($resource_id) != '' && !$resource->db_load(array('ressource_id', '=', trim($resource_id)))){
			throw new BadInputException('resource_id not existing');
		}
		$this->ressource_id = (trim($resource_id) != '' ? trim($resource_id) : NULL);

		$place = new Lieu();
		if(trim($place_id) != '' && !$place->db_load(array('lieu_id', '=', trim($place_id)))){
			throw new BadInputException('place_id not existing');
		}
		$this->lieu_id = (trim($place_id) != '' ? trim($place_id) : NULL);

		$this->livrable = ($milestone == 'yes' ? 'oui' : 'non');

		if(strlen(trim($custom_field)) > 255){
			throw new BadInputException('Custom field length not valid (max 255)');
		}
		$encoding = mb_detect_encoding($custom_field);
		$this->custom = (trim($custom_field) != '' ? trim(($encoding == 'UTF-8' ? utf8_decode($custom_field) : $custom_field)) : NULL);

		if(!$this->isSaved()){
			$creator = new User();
			if(trim($creator_id) != '') {
				if (!$creator->db_load(array('user_id', '=', trim($creator_id)))){
					throw new BadInputException('creator_id not existing as a user');
				}
				$this->createur_id = $creator_id;
			} else{
				$creator = new User();
				$creator->db_loadSQL("SELECT * FROM planning_user WHERE LOCATE('users_manage_all', droits) > 0 ORDER BY date_creation LIMIT 1 ");
				$this->createur_id = $creator->user_id;
			}
			$this->date_creation = date('Y-m-d H:i:s');
		}
		$creator = new User();
		$this->modifier_id = $this->createur_id;
		$this->date_modif = date('Y-m-d H:i:s');

		if(!$this->db_save()){
			throw new SaveErrorException('Unexpected error, please report maximum details to support@soplanning.org');
		}
		return true;
	}

	public function getTaskLink(){
		if(CONFIG_SOPLANNING_URL != "") {
			return CONFIG_SOPLANNING_URL . '/process/login.php?direct_periode_id=' . $this->periode_id . '&date=' . date('Y-m-d') . '&hash=' . md5($this->periode_id . '!�' . date('Y-m-d') . '�' . CONFIG_SECURE_KEY);
		} else{
			return false;
		}
	}

	public function db_delete() {	
		$return = parent::db_delete();
		$this->updateBudgetsProjet();
		return $return;
	}

	public function updateBudgetsProjet(){
		$projet = new Projet();
		$projet->db_load(array('projet_id', '=', $this->projet_id));
		$projet->updateBudgets();
		return true;		
	}
}
?>