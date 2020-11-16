<?php

class User extends GObject
{	
	private $tabDroits = array();
	private $tabPreferences = array();
	public static $tabScrolls = array('H', 'HV', 'NONE');

	public function __construct()
	{
		$this->table = 'planning_user';

		$this->fields['user_id'] = new GString('planning_user', 'user_id', 20, FALSE, '');
		$this->fields['user_groupe_id'] = new GInteger('planning_user', 'user_groupe_id', 11, TRUE, NULL);
		$this->fields['nom'] = new GString('planning_user', 'nom', 50, FALSE, '');
		$this->fields['login'] = new GString('planning_user', 'login', 30, TRUE, NULL);
		$this->fields['password'] = new GString('planning_user', 'password', 50, TRUE, NULL);
		$this->fields['email'] = new GString('planning_user', 'email', 255, TRUE, NULL);
		$this->fields['visible_planning'] = new GEnum('planning_user', 'visible_planning', array('oui','non'), FALSE, 'oui');
		$this->fields['couleur'] = new GString('planning_user', 'couleur', 6, TRUE, 'FFFFFF');
		$this->fields['droits'] = new GString('planning_user', 'droits', 65000, TRUE, NULL);
		$this->fields['cle'] = new GString('planning_user', 'cle', 40, FALSE, md5(rand(0, 10000000)));
		$this->fields['notifications'] = new GEnum('planning_user', 'notifications', array('oui','non'), FALSE, 'oui');
		$this->fields['adresse'] = new GString('planning_user', 'adresse', 255, TRUE, NULL);
		$this->fields['telephone'] = new GString('planning_user', 'telephone', 20, TRUE, NULL);
		$this->fields['mobile'] = new GString('planning_user', 'mobile', 20, TRUE, NULL);
		$this->fields['metier'] = new GString('planning_user', 'metier', 50, TRUE, NULL);
		$this->fields['commentaire'] = new GString('planning_user', 'commentaire', 255, TRUE, NULL);
		$this->fields['date_dernier_login'] = new GDatetime('planning_user', 'date_dernier_login', '19', TRUE, NULL);
        $this->fields['preferences'] = new GString('planning_user', 'preferences', 65000, TRUE, NULL);
		$this->fields['login_actif'] = new GEnum('planning_user', 'login_actif', array('oui','non'), FALSE, 'oui');
		$this->fields['date_creation'] = new GDatetime('planning_user', 'date_creation', '19', false, date('Y-m-d H:i:s'));
		$this->fields['date_modif'] = new GDatetime('planning_user', 'date_modif', '19', TRUE, NULL);
		$this->primaryKeys = array('user_id');
		parent::__construct();
	}

	public function db_loadArray($row)
	{
		parent::db_loadArray($row);
		$this->decoderDroits();
		$this->decoderPreferences();
		return true;
	}

	public function chargerUserFromSession() {
		$messageErreur = 'Impossible de charger votre profil. Veuillez vous identifier à nouveau';
		if(!isset($_SESSION['user_id']) || $_SESSION['user_id'] == '') {
			return ($messageErreur);
		}
		if (!$this->db_load(array('user_id', '=', $_SESSION['user_id']))) {
			return ($messageErreur);
		}
		return TRUE;
	}

	public function check($excludePK = false) {
		$check = parent::check();
		if((preg_match("/^[a-zA-Z0-9]+$/", $this->user_id) == 0) || strlen($this->user_id) > 20) {
			return 'user_user_idManquant';
		}
		return $check;
	}

	public function mailChangerPwd() {
		if(is_null($this->email) ||is_null($this->login)) {
			return true;
		}
		$smarty = new MySmarty();
		$sujet = CONFIG_SOPLANNING_TITLE . ' - ' . $smarty->getConfigVars('mail_sujet_changerPwd');
		if (CONFIG_SOPLANNING_URL != '')
		{
			$smarty->assign('lien', CONFIG_SOPLANNING_URL . '/change_password.php?user_id=' . $this->user_id . '&date=' . date('Y-m-d') . '&hash=' . md5($this->user_id . '¤' . date('Y-m-d') . '¤' . $this->cle));
		}else 
		{
			$urlSuggeree = getUrlInfo();
			$smarty->assign('lien', $urlSuggeree['root'] . substr($urlSuggeree['currentDir'], 0, strlen($urlSuggeree['currentDir'])-8) . 'change_password.php?user_id=' . $this->user_id . '&date=' . date('Y-m-d') . '&hash=' . md5($this->user_id . '¤' . date('Y-m-d') . '¤' . $this->cle));
		}
		$corps = $smarty->getHtml('mail_changer_pwd.tpl');
		$mail = new Mailer($this->email, $sujet, $corps);
		$send = $mail->send();
		return $send;
	}

	public function checkDroit($droit) {
		return in_array($droit, $this->tabDroits);
	}

	public function setDroits($droits) {
		$this->tabDroits = $droits;
		$this->data['droits'] = json_encode($this->tabDroits);
		return true;
	}

	public function decoderDroits() {
		$this->tabDroits = json_decode(html_entity_decode($this->droits), true);
		if(!is_array($this->tabDroits)) {
			$this->tabDroits = array();
		}
	}

	private function decoderPreferences() {
		$this->tabPreferences = json_decode($this->preferences,true);
	}
	
	public function getSmartyData()
	{
		$data = parent::getSmartyData();
		$data['tabDroits'] = $this->tabDroits;
		$data['tabPreferences'] = $this->tabPreferences;
		return $data;
	}

	public function lienIcal($users = array(), $projets = array(), $anciennete) {
		$urlSuggeree = getUrlInfo();
		$lienIcal = $urlSuggeree['root'] . substr($urlSuggeree['currentDir'], 0, strlen($urlSuggeree['currentDir'])-8) . 'export_ical.php?login=' . $this->login . '&hash=' . md5($this->login . '¤¤' . $this->password . '¤¤' . CONFIG_SECURE_KEY) . "&nocache";
		if(count($users) > 0) {
			$lienIcal .= '&users=' . implode('-', $users);
		}
		if(count($projets) > 0) {
			$lienIcal .= '&projets=' . implode('-', $projets);
		}
		$lienIcal .= '&age=' .$anciennete;
		return $lienIcal;
	}

	public function getPreferences() {
		$all = $this->getSmartyData();
		$json = $all['preferences'];
		$data = json_decode($json, true);

		// Préférence de vue large/réduit
		if (!isset($data['scrolls'])) {
			$data['scrolls'] = 'H';
		}

		// Préférence de vue par défaut
		if (!isset($data['vueDefaut']) || $data['vueDefaut'] == 'vueProjet') {
			$data['inverserUsersProjets'] = true;
		} else {
			$data['inverserUsersProjets'] = false;
		}

		if (!isset($data['vueLargeReduit']) || $data['vueLargeReduit'] == 'vueReduit') {
			$data['dimensionCase'] = 'reduit';
		} else {
			$data['dimensionCase'] = 'large';
		}
		return $data;
	}

	// get lists of users that the current user can view in the planning (if right tasks_view_specific_users activated)
	public function getRightsOnUsers()
	{
		$list = new GCollection('Right_on_user');
		$sql = 'SELECT * 
				FROM planning_right_on_user
				INNER JOIN planning_user ON planning_user.user_id = planning_right_on_user.allowed_id
				WHERE owner_id = ' . val2sql($this->user_id);
		$list->db_loadSQL($sql);
		$final = $list->get('user_id');
		return $final;
	}

	public function updateRightsOnUsers($rights) {
		db_query("DELETE FROM planning_right_on_user WHERE owner_id = " . val2sql($this->user_id));
		foreach($rights as $right) {
			$droit = new Right_on_user();
			$droit->owner_id = $this->user_id;
			$droit->allowed_id = $right;
			$droit->db_save();
		}
	}

	public function setSessionPref(){
		// Recherche des préférences utilisateurs pour affichage du planning par défaut
		$_SESSION['preferences'] = $this->getPreferences();
		if(isset($_SESSION['preferences']['vueDefaut']) && $_SESSION['preferences']['vueDefaut'] == 'vueProjet'){
			$_SESSION['baseLigne'] = 'projets';
		} else{
			$_SESSION['baseLigne'] = 'users';
		}
		setcookie('baseLigne', $_SESSION['baseLigne'], time()+60*60*24*500, '/');

		if (!isset($_SESSION['preferences']['vueJourMois'])||($_SESSION['preferences']['vueJourMois']=='vueMois')) {
			$_SESSION['baseColonne'] = 'jours';	
		}else
		{
			$_SESSION['baseColonne'] = 'heures';
		}
		setcookie('baseColonne', $_SESSION['baseColonne'], time()+60*60*24*500, '/');
		$_SESSION['dimensionCase'] = $_SESSION['preferences']['dimensionCase'];

		// Position du planning à l'ouverture (dans le cas de la date du jour, on force les cookies)
		if(isset($_SESSION['preferences']['positionPlanning']) && $_SESSION['preferences']['positionPlanning'] == 'today'){
			$dateDebut = new Datetime();
			setcookie('date_debut', '', time()+60*60*24*500, '/');
			setcookie('date_debut_affiche', '', time()+60*60*24*500, '/');
			setcookie('date_debut_affiche_tache', '', time()+60*60*24*500, '/');
			
			if(isset($_COOKIE['date_fin_affiche'])) {
				$dateFin = clone $dateDebut;
				if ($_SESSION['baseColonne'] == 'heures')
				{
					$dateFin->modify('+' . CONFIG_DEFAULT_NB_DAYS_DISPLAYED . ' days');
					$_SESSION['date_fin_affiche'] = $dateFin->format(CONFIG_DATE_LONG);
				}else
				{
					$dateFin->modify('+' . CONFIG_DEFAULT_NB_MONTHS_DISPLAYED . ' months');
					$_SESSION['date_fin_affiche'] = $dateFin->format(CONFIG_DATE_LONG);			
				}
				setcookie('date_fin', $_SESSION['date_fin_affiche'], time()+60*60*24*500, '/');
				setcookie('date_fin_affiche', $_SESSION['date_fin_affiche'], time()+60*60*24*500, '/');
				setcookie('date_fin_affiche_tache', $_SESSION['date_fin_affiche'], time()+60*60*24*500, '/');
			}
		}
		return true;
	}

	public function initPostLogin(){
		// Préférence de sélection par défaut
		chargerSessionStatutsDefaut();

		$_SESSION['user_id'] = $this->user_id;
		$_SESSION['user_groupe_id']= $this->user_groupe_id;
		$this->date_dernier_login = date('Y-m-d H:i:s');
		$this->db_save();

		// Audit
		if (CONFIG_SOPLANNING_OPTION_AUDIT == 1 && CONFIG_SOPLANNING_OPTION_AUDIT_CONNEXIONS == 1) {
			logAction('C');
		}
			
		$this->setSessionPref();
		return true;
	}
}

?>