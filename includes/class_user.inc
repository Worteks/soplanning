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
		$this->fields['google_2fa'] = new GEnum('planning_user', 'google_2fa', array('setup','ok'), FALSE, 'setup');
		$this->fields['date_creation'] = new GDatetime('planning_user', 'date_creation', '19', false, date('Y-m-d H:i:s'));
		$this->fields['date_modif'] = new GDatetime('planning_user', 'date_modif', '19', TRUE, NULL);
		$this->fields['tutoriel'] = new GString('planning_user', 'tutoriel', 255, TRUE, NULL);
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
		$this->tabPreferences = json_decode((string) $this->preferences,true);
	}
	
	public function getSmartyData()
	{
		$data = parent::getSmartyData();
		$data['tabDroits'] = $this->tabDroits;
		$data['tabPreferences'] = $this->tabPreferences;
		return $data;
	}

	public function lienIcal($anciennete, $users = array(), $projets = array()) {
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
		$data = json_decode((string) $json, true);

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
		if (!isset($data['masquerLigneVide']) || $data['masquerLigneVide'] == '0') {
			$data['masquerLigneVide'] = '0';
		} else {
			$data['masquerLigneVide'] = '1';
		}

		if (!isset($data['afficherTableauRecap']) || $data['afficherTableauRecap'] == '1') {
			$data['afficherTableauRecap'] = '1';
		} else {
			$data['afficherTableauRecap'] = '0';
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
		if(!isset($_SESSION['preferences']['dateformat'])){
			$_SESSION['preferences']['dateformat'] = 'fr';
		}

		if(!isset($_SESSION['baseLigne'])){
			if(isset($_SESSION['preferences']['vueDefaut']) && $_SESSION['preferences']['vueDefaut'] == 'vueProjet'){
				$_SESSION['baseLigne'] = 'projets';
			} else{
				$_SESSION['baseLigne'] = 'users';
			}
			setcookie('baseLigne', $_SESSION['baseLigne'], time()+60*60*24*500, '/');
		}

		if(!isset($_SESSION['baseColonne'])){
			if (!isset($_SESSION['preferences']['vueJourMois'])||($_SESSION['preferences']['vueJourMois']=='vueMois')) {
				$_SESSION['baseColonne'] = 'jours';	
			}else
			{
				$_SESSION['baseColonne'] = 'heures';
			}
			setcookie('baseColonne', $_SESSION['baseColonne'], time()+60*60*24*500, '/');
		}

		if(!isset($_SESSION['dimensionCase'])){
			$_SESSION['dimensionCase'] = $_SESSION['preferences']['dimensionCase'];
		}

		if(isset($_SESSION['preferences']['afficherTableauRecap'])){
			setcookie('afficherTableauRecap', $_SESSION['preferences']['afficherTableauRecap'], time()+60*60*24*500, '/'); 
		}else {
			setcookie('afficherTableauRecap', '1', time()+60*60*24*500, '/'); 
		}
		
		if(isset($_SESSION['preferences']['masquerLigneVide'])){
			setcookie('masquerLigneVide', $_SESSION['preferences']['masquerLigneVide'], time()+60*60*24*500, '/');
		}else {
			setcookie('masquerLigneVide', '0', time()+60*60*24*500, '/');
		}

		if(!isset($_COOKIE['date_debut_affiche'])){
			// Position du planning à l'ouverture (dans le cas de la date du jour, on force les cookies)
			if(isset($_SESSION['preferences']['positionPlanning']) && $_SESSION['preferences']['positionPlanning'] == 'today'){
				$dateDebut = new Datetime();
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

	public function getAPIData(){
		$data = array();
		$groupe = new User_groupe();
		$data['id'] = valueAPI($this->user_id);
		$data['team_id'] = valueAPI($this->user_groupe_id);
		$data['name'] = valueAPI($this->nom);
		$data['login'] = valueAPI($this->login);
		$data['email'] = valueAPI($this->email);
		$data['visible'] = valueAPI(($this->visible_planning == 'oui' ? '1' : '0'));
		$data['color'] = valueAPI($this->couleur);
		$data['notification'] = valueAPI(($this->notifications == 'oui' ? '1' : '0'));
		$data['address'] = valueAPI($this->adresse);
		$data['phone'] = valueAPI($this->telephone);
		$data['mobile'] = valueAPI($this->mobile);
		$data['job'] = valueAPI($this->metier);
		$data['comment'] = valueAPI($this->commentaire);
		$data['active'] = valueAPI(($this->login_actif == 'oui' ? '1' : '0'));
		$data['rights'] = valueAPI('feature not yet available');
		$data['last_login'] = valueAPI($this->date_dernier_login);
		$data['creation_date'] = valueAPI($this->date_creation);
		$data['modification_date'] = valueAPI($this->date_modif);
		return $data;
	}

	public function putAPI($user_id, $name, $login = '', $email = '', $password = '', $visible = '', $color = '', $notification = '', $address = '', $phone = '', $mobile = '', $job = '', $comment = '', $active = '', $rights = '', $team_id = ''){
		if(strlen($user_id) > 20 || strlen($user_id) < 1){
			throw new BadInputException('userID length not valid (max 20)');
		}
		if(strlen(trim($name)) > 50 || strlen(trim($name)) <= 0){
			throw new BadInputException('Name length not valid (max 50)');
		}
		if(strlen(trim($email)) > 255 || !VerifierAdresseMail($email)){
			throw new BadInputException('Email not valid');
		}
		if(strlen(trim($password)) > 20){
			throw new BadInputException('Password length not valid (max 20)');
		}
		if($visible != '1' && $visible != '0'){
			throw new BadInputException('Visibility wrong value (1 or 0)');
		}
		if(strlen(trim($color)) > 0 && strlen(trim($color)) != 6){
			throw new BadInputException('Color wrong value (6 chars, HTML color)');
		}
		if($notification != '1' && $notification != '0'){
			throw new BadInputException('Notification wrong value (1 or 0)');
		}
		if(strlen(trim($address)) > 255){
			throw new BadInputException('Address length not valid (max 255)');
		}
		if(strlen(trim($phone)) > 20){
			throw new BadInputException('Phone length not valid (max 20)');
		}
		if(strlen(trim($mobile)) > 20){
			throw new BadInputException('Mobile length not valid (max 20)');
		}
		if(strlen(trim($job)) > 50){
			throw new BadInputException('Job length not valid (max 50)');
		}
		if(strlen(trim($comment)) > 255){
			throw new BadInputException('Comment length not valid (max 255)');
		}
		if($active != '1' && $active != '0'){
			throw new BadInputException('Active wrong value (1 or 0)');
		}
		if(strlen(trim($team_id)) > 0){
			$team = new User_groupe();
			if(!$team->db_load(array('user_groupe_id', '=', trim($team_id)))){
				throw new BadInputException('Not existing team_id');
			}
		}

		$this->db_load(array('user_id', '=', trim($user_id)));
		if($this->isSaved()){
			$this->date_modif = date('Y-m-d H:i:s');
		}
		$this->user_id = trim($user_id);
		$this->nom = trim($name);
		$this->login = (trim($login) != '' ? trim($login) : NULL);
		$check = new GCollection('User');
		$check->db_load(array('login', '=', $this->login, 'user_id', '<>', $this->user_id));
		if($check->getCount() > 0){
			throw new BadInputException('Existing login, cant be used twice');
		}
		if(trim($password) != ''){
			$this->password = $this->hashPassword(trim($password));
		}
		$this->visible_planning = (trim($visible) == 0 ? 'non' : 'oui');
		if(trim($color) != ''){
			$this->couleur = trim($color);
		}
		$this->notifications = (trim($notification) == 0 ? 'non' : 'oui');
		$this->adresse = (trim($address) != '' ? trim($address) : NULL);
		$this->telephone = (trim($phone) != '' ? trim($phone) : NULL);
		$this->mobile = (trim($mobile) != '' ? trim($mobile) : NULL);
		$this->metier = (trim($job) != '' ? trim($job) : NULL);
		$this->commentaire = (trim($comment) != '' ? trim($comment) : NULL);
		$this->login_actif = (trim($active) == 0 ? 'non' : 'oui');
		$this->user_groupe_id = (trim($team_id) > 0 ? trim($team_id) : NULL);
		if(!$this->isSaved()){
			// read only
			$this->droits = '["tasks_readonly","tasks_view_all_projects","tasks_view_all_users"]';
		}
		if(!$this->db_save()){
			throw new SaveErrorException('Unexpected error, please report maximum details to support@soplanning.org');
		}
		return true;

	}

	public function vueDefaut(){
		if (isset($_SESSION['preferences']['vuePlanning']) && ($_SESSION['preferences']['vuePlanning']=="vueTaches") && (CONFIG_SOPLANNING_OPTION_TACHES == 1)) {
			$redirect = 'taches.php';
		} else {
			$redirect = 'planning.php';
		}
		return $redirect;
	}

	public function hashPassword($password){
		return sha1("¤" . $password . "¤");
	}
	
	public function peutGererTache($objTache){
		$projet = new Projet();
		$projet->db_load(array('projet_id', '=', $objTache->projet_id));

		if ($this->checkDroit('tasks_modify_own_project') && $projet->createur_id != $this->user_id) {
			return false;
		}
        if ($this->checkDroit('tasks_modify_own_task') && $projet->createur_id != $this->user_id && $objTache->user_id != $this->user_id) {
			return false;
        }
        if ($this->checkDroit('tasks_view_only_own') && $objTache->user_id != $this->user_id) {
			return false;
        }

		return true;
	}

	public function get_valeur_tutoriel($cle) {
		$data = json_decode($this->tutoriel, true);
		if(is_null($data)){
			return NULL;
		}
		return (array_key_exists($cle,$data) ? $data[$cle] : NULL);
	}

	public function set_valeur_tutoriel($cle, $valeur) {
		$data = json_decode($this->tutoriel, true);
		$data[$cle] = $valeur;
		$this->tutoriel = json_encode($data);
		//echo $this->tutoriel; die;
		return $this->db_save();
	}

}

?>