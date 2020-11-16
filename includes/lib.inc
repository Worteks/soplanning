<?php

function date_diff2($date1, $date2) {
    return round(($date2->format('U') - $date1->format('U')) / (60*60*24));
}

function xajaxFormat($string) {
	return str_replace(array('"', "\r\n", "\n", "\'", "\\\\"), array('\"', "", "", "\\\'", "\\\\\\\\"), trim($string));
}

function afficherErreur($objResponse, $message) {
	$objResponse->addScript('win.destroy();');
	$objResponse->addScript('win = new Window({className: "charte1", title: "Message", width:430, height:200, destroyOnClose: true, recenterAuto:true});');
	$objResponse->addScript('win.setHTMLContent("' . xajaxFormat($message) . '");');
	$objResponse->addScript('win.showCenter();');
	return $objResponse->getXML();
}

// dates au format YYYY-MM-DD
function getNbJours($date_debut, $date_fin, $skipDaysoff = true) {
	global $lang;
	$DAYS_INCLUDED = explode(',', CONFIG_DAYS_INCLUDED);
	$joursFeriesTmp = getjoursFeries();
	$joursFeries = array();
	foreach($joursFeriesTmp as $b){
		$joursFeries[$b]=$b;
	}
	$nbJours = 1;
	$dateDebut = new DateTime();
	$dateDebut->setDate(substr($date_debut,0,4), substr($date_debut,5,2), substr($date_debut,8,2));
	while($dateDebut->format('Y-m-d') < $date_fin) {
		$dateDebut->modify('+1 days');
		if (!$skipDaysoff || (in_array($dateDebut->format('w'), $DAYS_INCLUDED) && !isset($joursFeries[$dateDebut->format('Y-m-d')]))) {
			$nbJours++;
		}
	}
	return $nbJours;
}

// dates au format YYYY-MM-DD
function getNbJoursFull($date_debut, $date_fin) {
	$nbJours = 1;
	$dateDebut = new DateTime();
	$dateDebut->setDate(substr($date_debut,0,4), substr($date_debut,5,2), substr($date_debut,8,2));
	while($dateDebut->format('Y-m-d') < $date_fin) {
		$dateDebut->modify('+1 days');
		$nbJours++;
	}
	return $nbJours;
}

function calculerDateFin($date_debut, $nbJours) {
	global $lang;
	$DAYS_INCLUDED = explode(',', CONFIG_DAYS_INCLUDED);
	$joursFeries = getjoursFeries();

	$dateFin = new DateTime();
	$dateFin->setDate(substr($date_debut,0,4), substr($date_debut,5,2), substr($date_debut,8,2));
	$i = 1;
	while($i < $nbJours) {
		$dateFin->modify('+1 days');
		if (in_array($dateFin->format('w'), $DAYS_INCLUDED) && !in_array($dateFin->format('Y-m-d'), $joursFeries)) {
			$i++;
		}
	}
	return $dateFin->format('Y-m-d');
}

//modif ajout date debut
function calculerDateDebut($date_fin, $nbJours) {
	global $lang;
	$DAYS_INCLUDED = explode(',', CONFIG_DAYS_INCLUDED);
	$joursFeries = getjoursFeries();

	$dateDebut = new DateTime();
	$dateDebut->setDate(substr($date_fin,0,4), substr($date_fin,5,2), substr($date_fin,8,2));
	$i = 1;
	while($i < $nbJours) {
		$dateDebut->modify('-1 days');
		if (in_array($dateDebut->format('w'), $DAYS_INCLUDED) && !in_array($dateDebut->format('Y-m-d'), $joursFeries)) {
			$i++;
		}
	}
	return $dateDebut->format('Y-m-d');
}

// format hh:mm
function ajouterDuree($total, $ajout) {
	$dataTotal = explode(':', $total);
	$dataAjout = explode(':', $ajout);
	$heures = $dataTotal[0] + $dataAjout[0];
	$minutes = $dataTotal[1] + $dataAjout[1];
	if($minutes >= 60) {
		
		$heures += 1;
		$minutes -= 60;
		
	}
	$final = (strlen($heures) == 1 ? '0' . $heures : $heures) . ':' . (strlen($minutes) == 1 ? '0' . $minutes : $minutes);
	return $final;
}

// calculer diff entre 2 heures (la 1ere est la plus tot). format hh:mm
function soustraireDuree($heure1, $heure2) {
	$h1 = strtotime($heure1);
	$h2 = strtotime($heure2);
	$final = gmdate('H:i:s',$h2-$h1);
	return $final;
}

function getJoursFeries() {
	$joursFeries = array();
	$feries = new GCollection('Ferie');
	$feries->db_load(array());
	while($ferie = $feries->fetch()) {
		$joursFeries[$ferie->date_ferie] = array('date'=>$ferie->date_ferie,'couleur'=>$ferie->couleur);
	}
	return $joursFeries;
}

// 2004-01-15 => 15/01/2004
function sqldate2userdate($s, $noYear = false){
	if ($s == '') {
		return '';
	}
	return (!$noYear ? date(CONFIG_DATE_LONG, strtotime($s)) : date(CONFIG_DATE_SHORT2, strtotime($s)));
}

// 2004-01-15 => 15/01/04
function sqldate2userdateShort($s){
	if ($s == '') {
		return '';
	}

	return date(CONFIG_DATE_SHORT, strtotime($s));
}

// 15/01/2004 => 2004-01-15
function userdate2sqldate($s){
	if ($s == '') {
		return '';
	}
	switch(CONFIG_DATE_FORMAT)	{
		case 'fr':
			return substr($s,6,4).'-'.substr($s,3,2).'-'.substr($s,0,2);
			break;
		case 'us':
			return substr($s,6,4).'-'.substr($s,0,2).'-'.substr($s,3,2);
			break;
		case 'jp':
			return substr($s,0,4).'-'.substr($s,5,2).'-'.substr($s,8,2);
			break;
	}

}

// 02:35:00 => 02:35
function sqltime2usertime($s) {
    return substr($s,0,5);
}
//modif ajout heures decimales
function decimalHours($time)
{
    $hms = explode(":", $time);
    return ($hms[0] + ($hms[1]/60));
}

// 2004-01-15 12:10:00 => 15/01/2004 12:10:00
function sqldatetime2userdatetime($s){
	if ($s == '') {
		return '';
	}
	$data = explode(' ', $s);
	if(count($data) != 2) {
		return '';
	}
    return sqldate2userdate($data[0]) . ' ' . $data[1];
}

// 2:35 => 02:35
// 2 => 02:00
// 2.5 => 02:30
function usertime2sqltime($s, $formatComplet = 'ok') {
	if(strpos($s, '.')) {
		$data = explode('.', $s);
		// on replace la partie decimale par son equivalent en minute
		if(isset($data[1]) && strlen($data[1]) > 0) {
			$s = $data[0] . ':' . ($data[1] * 6);
		} else {
			$s = $data[0];
		}
	}
	if(strlen($s) == 0) {
		$s = '00:00';
	}
	if(strlen($s) == 1) {
		$s = '0' . $s . ':00';
	}
	if(strlen($s) == 2) {
		$s = $s . ':00';
	}
	if(strlen($s) == 4) {
		$s = '0' . $s;
	}
	if($formatComplet == 'ok' && strlen($s)!= 8) {
		$s .= ':00';
	}
	return $s;
}

// valide une date aux différents formats
function controlDate($date) {
	switch($_SESSION['preferences']['dateformat'])
	{
	case 'fr':
		if ($date != '' && !preg_match("/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/", $date)) {
			return false;
		}
		break;
	case 'us':
		if ($date != '' && !preg_match("/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/", $date)) {
			return false;
		}
		break;
	case 'jp':
		if ($date != '' && !preg_match("/([0-9]{4})\-([0-9]{2})\-([0-9]{2})/", $date)) {
			return false;
		}
		break;
	}
	return true;
}

function val2sql($val)
{
	global $cfgConnection;
	$val = trim($val);
	if (($val == '') || ($val == "\0")) {
	    return 'NULL';
	} else {
		return '\'' . mysqli_real_escape_string($cfgConnection, $val) . '\'';
	}
}


// calcule la couleur (noir ou blanc) qui tranche le mieux avec celle passée en parametre
function buttonFontColor($bgcolor) {
	$values_array = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E", "F");

	if($bgcolor == '#black') {
		return "#FFFFFF";
	}

	if (strlen($bgcolor) != 7) {
		return false;
	}
	if (substr($bgcolor, 0, 1) != "#") {
		return false;
	}
	//$cpt = 0;
	$total = 0;
	for ($i = 1 ; $i < 7 ; $i++) {
		$mySubStr = strtoupper(substr($bgcolor, $i /*+ $cpt*/, 1));
		if (!in_array($mySubStr, $values_array)) {
			return false;
		} elseif ($i == 1) { // red
			if     ($mySubStr == "A") {$total += 10;}
			elseif ($mySubStr == "B") {$total += 11;}
			elseif ($mySubStr == "C") {$total += 12;}
			elseif ($mySubStr == "D") {$total += 13;}
			elseif ($mySubStr == "E") {$total += 14;}
			elseif ($mySubStr == "F") {$total += 15;}
			else {
				$total += $mySubStr;
			}
		} elseif ($i == 3 ) { // green big ponderation
			$pond = 9;
			if     ($mySubStr == "A") {$total += (10 );}
			elseif ($mySubStr == "B") {$total += (11 );}
			elseif ($mySubStr == "C") {$total += (12 + ($pond * 2));}
			elseif ($mySubStr == "D") {$total += (13 + ($pond * 2));}
			elseif ($mySubStr == "E") {$total += (14 + ($pond * 4));}
			elseif ($mySubStr == "F") {$total += (15 + ($pond * 4));}
			else {
				$total += $mySubStr;
			}
		} elseif ($i == 5) { // blue medium ponderation
			$pond = 5;
			if     ($mySubStr == "A") {$total += (10 );}
			elseif ($mySubStr == "B") {$total += (11 );}
			elseif ($mySubStr == "C") {$total += (12 + $pond);}
			elseif ($mySubStr == "D") {$total += (13 + $pond);}
			elseif ($mySubStr == "E") {$total += (14 + $pond);}
			elseif ($mySubStr == "F") {$total += (15 + $pond);}
			else {
				$total += $mySubStr;
			}
		}
	}
	if ($total < 36) {
		$fontColor = "#FFFFFF";
	} else {
		$fontColor = "#000000";
	}
	return $fontColor;
}

// r?cup?re le message ? afficher, en concaténant un eventuel code d'erreur
function formatMessage($smartyTexte) {
	global $lang;
	$smartyTemp = new MySmarty('fr');

	$temp = explode(';', $smartyTexte);
	if(count($temp) == 1) {
		$texte = $smartyTemp->getConfigVars($smartyTexte);
		if($texte == '') {
			$texte = $smartyTexte;
		}
	} else {
		$texte = $smartyTemp->getConfigVars($temp[0]) . $smartyTemp->getConfigVars('code_technique') . $temp[1];
	}
	return $texte;
}


function detectLanguage($default) {
	global $cfgAvailableLanguages;
	$language = '';
	if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		$HTTP_ACCEPT_LANGUAGE = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
	if (!empty($_SERVER['HTTP_USER_AGENT']))
		$HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];

	if (empty($language) && !empty($HTTP_ACCEPT_LANGUAGE)) {
		$accepted    = explode(',', $HTTP_ACCEPT_LANGUAGE);
		$acceptedCnt = count($accepted);
		reset($accepted);
		for ($i = 0; $i < $acceptedCnt && empty($language); $i++) {
			$tmpLang = $accepted[$i];

			// specific case for firefox
			$data = explode('-', $accepted[$i]);
			if(count($data) > 1) {
				$tmpLang = $data[0];
			}
			$data = explode(';', $accepted[$i]);
			if(count($data) > 1) {
				$tmpLang = $data[0];
			}

			if (array_key_exists($tmpLang, $cfgAvailableLanguages)) {
				$language = $tmpLang;
			}
		}
	}

	// 2. try to findout users language by checking it's HTTP_USER_AGENT variable
	if (empty($language) && !empty($HTTP_USER_AGENT)) {
		$language = isAvailableLanguage($HTTP_USER_AGENT, 2);
	}

	// 3. Didn't catch any valid lang : we use the default settings
	if (empty($language) || is_null($language)) {
		$language = $default;
	}
	return $language;
}

function isAvailableLanguage($str = '', $envType = '')
{
	global $cfgAvailableLanguages;

	$language = '';
	reset($cfgAvailableLanguages);
	foreach ($cfgAvailableLanguages as $key => $value){
		if (($envType == 1 && preg_match('/^(' . $value[0] . ')(;q=[0-9]\\.[0-9])?$/i', $str)) ||
		($envType == 2 && preg_match('/(\(|\[|;[[:space:]])(' . $value[0] . ')(;|\]|\))/i', $str))) {
			$language     = $key;
			break;
		}
	}
	return $language;
}

function getUrlInfo()
{
	$root = 'http';
	if ( isset ($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
		$root .= "s";
	}
	$root .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$root .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
	} else {
		$root .= $_SERVER["SERVER_NAME"];
	}

	$fileName = substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/")+1);
	$currentDir = substr($_SERVER["SCRIPT_NAME"], 0, strrpos($_SERVER["SCRIPT_NAME"], "/")+1);
	$result['root'] = $root;
	$result['currentDir'] = $currentDir;
	$result['fileName'] = $fileName;
	$result['currentUrl'] = $root.$currentDir.$fileName;
	$result['currentUrlWithArg'] = $root.$_SERVER['REQUEST_URI'];
	return $result;
}

function is__writable($path) {
//will work in despite of Windows ACLs bug
//NOTE: use a trailing slash for folders!!!
    if ($path{strlen($path)-1}=='/') // recursively return a temporary file path
        return is__writable($path.uniqid(mt_rand()).'.tmp');
    else if (is_dir($path))
        return is__writable($path.'/'.uniqid(mt_rand()).'.tmp');
    // check tmp file for read/write capabilities
    $rm = file_exists($path);
    $f = @fopen($path, 'a');
    if ($f===false)
        return false;
    fclose($f);
    if (!$rm)
        unlink($path);
    return true;
}


function VerifierAdresseMail($adresse) {
	$Syntaxe='#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,15}$#';
	if(preg_match($Syntaxe,$adresse))
		return true;
	else
		return false;
}


function xss_protect($data, $strip_tags = false, $allowed_tags = "") {
	if(is_null($data) || is_bool($data) || is_array($data)) {
		return $data;
	}
	if($strip_tags) {
        $data = strip_tags($data, $allowed_tags . "<b>");
    }
    if(stripos($data, "script") !== false) {
        $result = str_replace("script","scr<b></b>ipt", htmlentities($data, ENT_QUOTES, "ISO-8859-1"));
    } else {
        $result = htmlentities($data, ENT_QUOTES, "ISO-8859-1");
    }
    return $result;
}

function detectionMobile() {
	$detect = new Mobile_Detect();
	if($detect->isMobile() || $detect->isTablet()) {
		return true;
	}
	return false;
}

// fonction pour affichage par jour
function couvreTranche($dureeDetails, $trancheHoraire) {
	if($dureeDetails == 'AM') {
		// on demarre la couverture sur les premieres heures autorisées dans la config, jusqu'à couvrir le nombre d'heures indiqué pour une matinée (config également)
		$tabTranchesHoraires = explode(',', CONFIG_HOURS_DISPLAYED);
		$tabTranchesHoraires = explode(',', CONFIG_HOURS_DISPLAYED);
		for($i=1; $i<=CONFIG_DURATION_AM; $i++) {
			if(array_shift($tabTranchesHoraires) == $trancheHoraire) {
				return true;
			}
		}
		return false;

	} elseif ($dureeDetails == 'PM') {
		// on demarre la couverture après que les heures du matin soient déduites
		$tabTranchesHoraires = explode(',', CONFIG_HOURS_DISPLAYED);
		for($i=1; $i<=CONFIG_DURATION_AM; $i++) {
			$tmp = array_shift($tabTranchesHoraires);
		}
		for($i=1; $i<=CONFIG_DURATION_PM; $i++) {
			if(array_shift($tabTranchesHoraires) == $trancheHoraire) {
				return true;
			}
		}
		return false;

	} elseif ($dureeDetails == 'duree') {
		// on couvre selon la durée, sur les heures selectionnées
		$tabTranchesHoraires = explode(',', CONFIG_HOURS_DISPLAYED);
		for($i=1; $i<=CONFIG_DURATION_DAY; $i++) {
			if(array_shift($tabTranchesHoraires) == $trancheHoraire) {
				return true;
			}
		}

	} elseif (strlen($dureeDetails) == 17) {
		// on calcule si l'horaire de début et de fin couvre.
		$data = explode(';', $dureeDetails);
		$heureDebut = $data[0];
		$heureFin = $data[1];
		$debutTranche = usertime2sqltime($trancheHoraire);
		$finTranche = usertime2sqltime($trancheHoraire + 1);
		if($heureDebut >= $debutTranche && $heureDebut < $finTranche) {
			return true;
		}
		if($heureFin > $debutTranche && $heureFin < $finTranche) {
			return true;
		}
		if($heureDebut < $debutTranche && $heureFin >= $finTranche) {
			return true;
		}
		return false;

	} else {
		// dans tous les autres cas on considère que ça couvre
		return true;
	}
}


function cleanInput($input) {

  $search = array(
    '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
    '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
    '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
    '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
  );

    $output = preg_replace($search, '', $input);
    return $output;
}

function sanitize($input) {
	global $cfgConnection;
    if (is_array($input)) {
        foreach($input as $var=>$val) {
            $output[$var] = sanitize($val);
        }
		if(!isset($output)) {
			$output = array();
		}
    }
    else {
        $input  = cleanInput($input);
        $output = mysqli_real_escape_string($cfgConnection, $input);
    }
    return $output;
}


function checkConflitRessource($ressource,$date_debut,$date_fin,$duree_details,$user_id,$periode_id,$link_id) {
		// V?rification de l'exclusivité de la ressource
		if($ressource != '')
		{
	      $nressource = new Ressource();
		  $nressource->db_load(array('ressource_id', '=', $ressource));
		}else $nressource->exclusif=0;
		if ($nressource->exclusif==1)
		{
		  $texte = '';
		  $smarty = new MySmarty();
		  $str_user="(";
		  if (is_array($user_id))
		  {
			foreach($user_id as $current_user_id) {
			if ($str_user!="(") {$str_user.=",";}
			$str_user.="'".$current_user_id."'";
		  }
		  }else $str_user.="'".$user_id."'";
		  $str_user.=")";
		  // si pas de date de fin, c'est sur un jour seulement
		if (is_null($date_fin)) $date_fin=$date_debut;
		 if (is_null($date_fin) or empty($date_fin)) $date_fin=$date_debut;
		  if ($date_debut == $date_fin)
	 	  {
            $sql = "SELECT *, planning_user.nom AS nom_user";
            $sql .= " FROM planning_periode ";
            $sql .= " INNER JOIN planning_user ON planning_periode.user_id = planning_user.user_id ";
            $sql .= " WHERE ((date_fin IS NOT NULL AND date_debut <= " . val2sql($date_debut) . " AND date_fin >= " . val2sql($date_fin) . ")";
            $sql .= " OR (date_fin IS NULL AND date_debut = " . val2sql($date_debut) . "))";
            $sql .= " AND planning_periode.ressource_id = ".val2sql($ressource);
		  }else
		  {
            $sql = "SELECT *, planning_user.nom AS nom_user";
            $sql .= " FROM planning_periode ";
            $sql .= " INNER JOIN planning_user ON planning_periode.user_id = planning_user.user_id ";
            $sql .= " WHERE ((date_fin IS NOT NULL AND date_debut <= " . val2sql($date_debut) . " AND date_fin >= " . val2sql($date_debut) . ")";
            $sql .= " OR (date_fin IS NOT NULL AND date_debut <= " . val2sql($date_fin) . " AND date_debut >= " . val2sql($date_debut) . ")";
			$sql .= " OR (date_fin IS NULL AND date_debut >= " . val2sql($date_debut) . " AND date_debut <= " . val2sql($date_fin) . "))";
            $sql .= " AND planning_periode.ressource_id = ".val2sql($ressource);
		  }
		  if (!empty($link_id)) $sql .= " AND link_id<>'$link_id'";
	      if (!is_null($periode_id)) $sql.= " AND planning_periode.periode_id<> $periode_id ";
	      $result = db_query($sql);
	      if (db_num_rows($result)>0)
	      {
	       	// Au premier conflit horaire, on quitte
			while ($var = db_fetch_array($result)) {
				if (!checkConflitHoraire($var['duree_details'],$duree_details))
				{
					return false;
				}
			}
	      	return true;
		  }else return true;
		}else return true;
	}

function checkConflitLieu($lieu,$date_debut,$date_fin,$duree_details,$user_id,$periode_id,$link_id) {
	// V?rification de l'exclusivité du lieu
	if($lieu != '')
	{
	  $nlieu = new Lieu();
	  $nlieu->db_load(array('lieu_id', '=', $lieu));
	}else $nlieu->exclusif=0;
	if ($nlieu->exclusif==1)
	{
	 $texte = '';
	 $smarty = new MySmarty();
	 $str_user="(";
	 if (is_array($user_id))
	 {
		foreach($user_id as $current_user_id)
		{
		 if ($str_user!="(") {$str_user.=",";}
		 $str_user.="'".$current_user_id."'";
		}
	 }else $str_user.="'".$user_id."'";
	 $str_user.=")";
	 // si pas de date de fin, c'est sur un jour seulement
	 if (is_null($date_fin) or empty($date_fin)) $date_fin=$date_debut;
	 if ($date_debut == $date_fin)
	 {
		$sql = "SELECT *, planning_user.nom AS nom_user";
		$sql .= " FROM planning_periode ";
		$sql .= " INNER JOIN planning_user ON planning_periode.user_id = planning_user.user_id ";
		$sql .= " WHERE	((date_fin IS NOT NULL AND date_debut <= " . val2sql($date_debut) . " AND date_fin >= " . val2sql($date_fin) . ")";
		$sql .= " OR (date_fin IS NULL AND date_debut = " . val2sql($date_debut) . "))";
		$sql .= " AND planning_periode.lieu_id = ".val2sql($lieu);
	 }else
	 {
		$sql = "SELECT *, planning_user.nom AS nom_user";
		$sql .= " FROM planning_periode ";
		$sql .= " INNER JOIN planning_user ON planning_periode.user_id = planning_user.user_id ";
		$sql .= " WHERE	((date_fin IS NOT NULL AND date_debut >= " . val2sql($date_debut) . " AND date_fin <= " . val2sql($date_fin) . ")";
		$sql .= " OR (date_fin IS NOT NULL AND date_debut <= " . val2sql($date_fin) . " AND date_debut >= " . val2sql($date_debut) . ")";
		$sql .= " OR (date_fin IS NULL AND date_debut >= " . val2sql($date_debut) . " AND date_debut <= " . val2sql($date_fin) . "))";
		$sql .= " AND planning_periode.lieu_id = ".val2sql($lieu);
	 }
	 if (!empty($link_id)) $sql .= " AND link_id<>'$link_id'";
	 if (!is_null($periode_id)) $sql.= " AND planning_periode.periode_id<> $periode_id ";
	 $result = db_query($sql);
	 if (db_num_rows($result)>0)
	 {
		// Au premier conflit horaire, on quitte
		while ($var = db_fetch_array($result)) {
			if (!checkConflitHoraire($var['duree_details'],$duree_details))
			{
				return false;
			}
		}	
		return true;
	}else return true;
  }else return true;
}

function checkConflitHoraire($horaire1,$horaire2) {
	$tabTranchesHoraires = explode(',', CONFIG_HOURS_DISPLAYED);
	$premiereTranche=$tabTranchesHoraires[0].':00';
	$derniereTranche=end($tabTranchesHoraires).':00';
	if (is_null($horaire1) || $horaire1=="duree")
	{
		$debut_horaire1=convertHourToDecimal($premiereTranche);
		$fin_horaire1=convertHourToDecimal($derniereTranche);
	}
	if (is_null($horaire2) || $horaire2=="duree")
	{
		$debut_horaire2=convertHourToDecimal($premiereTranche);
		$fin_horaire2=convertHourToDecimal($derniereTranche);
	}
	if ($horaire1=="AM")
	{
		$debut_horaire1=convertHourToDecimal($premiereTranche);
		$dureeAM=convertHourToDecimal(CONFIG_DURATION_AM);
		$fin_horaire1=$debut_horaire1 + $dureeAM;
	}
	if ($horaire2=="AM")
	{
		$debut_horaire2=convertHourToDecimal($premiereTranche);
		$dureeAM=convertHourToDecimal(CONFIG_DURATION_AM);
		$fin_horaire2=$debut_horaire2 + $dureeAM;
	}
	if ($horaire1=="PM")
	{
		$fin_horaire1=convertHourToDecimal($derniereTranche);
		$dureePM=convertHourToDecimal(CONFIG_DURATION_PM);
		$debut_horaire1=$fin_horaire1 - $dureePM;
	}
	if ($horaire2=="PM")
	{
		$fin_horaire2=convertHourToDecimal($derniereTranche);
		$dureePM=convertHourToDecimal(CONFIG_DURATION_PM);
		$debut_horaire2=$fin_horaire2 - $dureePM;
	}
	if (strpos("$horaire1",';'))
	{
		$heureExploded=explode(';',$horaire1);
		$debut_horaire1=convertHourToDecimal($heureExploded[0]);
		$fin_horaire1=convertHourToDecimal($heureExploded[1]);
	}
	if (strpos("$horaire2",';'))
	{
		$heureExploded=explode(';',$horaire2);
		$debut_horaire2=convertHourToDecimal($heureExploded[0]);
		$fin_horaire2=convertHourToDecimal($heureExploded[1]);
	}
	
	if (($debut_horaire2>=$debut_horaire1) && ($debut_horaire2<$fin_horaire1)) return 
	false;
	
	if (($fin_horaire2>=$debut_horaire1) && ($fin_horaire2<$fin_horaire1)) return false;
	
	return true;
}

function upload_image($target,$fichier)
{
  $smarty = new MySmarty();
  // Constantes
  define('MAX_SIZE', 100000);    // Taille max en octets du fichier
  define('WIDTH_MAX', 400);    // Largeur max de l'image en pixels
  define('HEIGHT_MAX', 40);    // Hauteur max de l'image en pixels
  // Tableaux de donnees
  $tabExt = array('jpg','gif','png','jpeg');    // Extensions autorisees
  $infosImg = array();
  // Variables
  $extension = '';
  $message = '';
  $nomImage = '';
  if (!extension_loaded('gd'))
  {
	// Sinon erreur sur le type de l'image
	$_SESSION['erreur'] = $smarty->getConfigVars('erreur_extension_gd');
	return 'KO';
  }
  if( !is_dir($target) ) {
    if( !mkdir($target, 0755) ) {
	$_SESSION['erreur'] = $smarty->getConfigVars('erreur_creation_repertoire_logo');
	return 'KO';
    }
  }
    // Recuperation de l'extension du fichier
    $extension  = pathinfo($fichier['name'], PATHINFO_EXTENSION);

    // On verifie l'extension du fichier
    if(in_array(strtolower($extension),$tabExt))
    {
      // On recupere les dimensions du fichier
      $infosImg = getimagesize($fichier['tmp_name']);

      // On verifie le type de l'image
      if($infosImg[2] >= 1 && $infosImg[2] <= 14)
      {
        // On verifie les dimensions et taille de l'image
        if(($infosImg[0] <= WIDTH_MAX) && ($infosImg[1] <= HEIGHT_MAX))
        {
          if (filesize($fichier['tmp_name']) < MAX_SIZE)
		  {
				// Parcours du tableau d'erreurs
				if(isset($fichier['error']) && UPLOAD_ERR_OK === $fichier['error'])
				{
					$new=$target.'/'.$fichier['name'];
					// Si c'est OK, on teste l'upload
					if(move_uploaded_file($fichier['tmp_name'], $new))
					{
					 $message = '';
					}else
					{
					// Sinon erreur sur le type de l'image
					$_SESSION['erreur'] = $smarty->getConfigVars('erreur_upload_fichier');
					return 'KO';
					}
				}else
				{
					// Sinon erreur sur le type de l'image
					$_SESSION['erreur'] = $smarty->getConfigVars('erreur_upload_fichier');
					return 'KO';
				}
				}
			}else
			{
				$NouvelleLargeur = 40; //Largeur choisie à 40 px mais modifiable
				$NouvelleHauteur = ( ($infosImg[1] * (($NouvelleLargeur)/$infosImg[0])) );
				$NouvelleImage = imagecreatetruecolor($NouvelleLargeur , $NouvelleHauteur) or die ("Erreur");

				$gdinfo=gd_info();
				// Sinon erreur sur les dimensions et taille de l'image
				if (preg_match('/jpg|jpeg/i',$extension))
				{
					if ($gdinfo["JPEG Support"]==true)
					{
						$ImageChoisie=imagecreatefromjpeg($fichier['tmp_name']);
						imagecopyresampled($NouvelleImage , $ImageChoisie  , 0,0, 0,0, $NouvelleLargeur, $NouvelleHauteur, $infosImg[0],$infosImg[1]);
					}else
					{
						// Sinon erreur sur le type de l'image
						$_SESSION['erreur'] = $smarty->getConfigVars('erreur_support_jpeg');
						return 'KO';
					}
				}else if (preg_match('/png/i',$extension))
				{
					if ($gdinfo["PNG Support"]==true)
					{
						$ImageChoisie=imagecreatefrompng($fichier['tmp_name']);
						imagecopyresampled($NouvelleImage , $ImageChoisie  , 0,0, 0,0, $NouvelleLargeur, $NouvelleHauteur, $infosImg[0],$infosImg[1]);
					}else
					{
						// Sinon erreur sur le type de l'image
						$_SESSION['erreur'] = $smarty->getConfigVars('erreur_support_png');
						return 'KO';
					}
				}else if (preg_match('/gif/i',$extension))
				{
					if ($gdinfo["GIF Read Support"]==true)
					{
						$ImageChoisie=imagecreatefromgif($fichier['tmp_name']);
						imagecopyresampled($NouvelleImage , $ImageChoisie  , 0,0, 0,0, $NouvelleLargeur, $NouvelleHauteur, $infosImg[0],$infosImg[1]);
					}else
					{
						// Sinon erreur sur le type de l'image
						$_SESSION['erreur'] = $smarty->getConfigVars('erreur_support_png');
						return 'KO';
					}
				}else return 1;
				imagedestroy($ImageChoisie);
				$NomImageExploitable = $fichier['name'];
				imagejpeg($NouvelleImage , $target.'/'.$NomImageExploitable, 100);
			}
		}else
		{
			// Sinon erreur sur le type de l'image
			$_SESSION['erreur'] = $smarty->getConfigVars('erreur_fichier_not_image');
			return 'KO';
		}
    }
    else
    {
      // Sinon on affiche une erreur pour l'extension
	  $_SESSION['erreur'] = $smarty->getConfigVars('erreur_image_extension');
	  return 'KO';
    }
	return $message;
}

// Force the User Date Format
function forceUserDateFormat($date,$force=0)
{
	// Si je suis en date ISO
	if (!empty($date) && ($_SESSION['isMobileOrTablet'] == 1 || $force==1))
	{
		switch(CONFIG_DATE_FORMAT)	{
		case 'fr':
			return substr($date,8,2).'/'.substr($date,5,2).'/'.substr($date,0,4);
			break;
		case 'us':
			return substr($date,5,2).'/'.substr($date,8,2).'/'.substr($date,0,4);
			break;
		case 'jp':
			return $date;
			break;
		}
	}else return $date;
}

// Force the ISO Date Format
function forceISODateFormat($date)
{
	if(preg_match('/\d{2}-\d{2}-\d{4}/',$date))
	{
		return substr($date,6,4).'-'.substr($date,0,2).'-'.substr($date,3,2);
	}elseif(preg_match('/\d{2}\/\d{2}\/\d{4}/',$date))
	{
		return substr($date,6,4).'-'.substr($date,3,2).'-'.substr($date,0,2);
	}else return $date;
}

function convertHourToDecimal($heure) {
	$data = explode(':', $heure);
	if(count($data) == 1) {
		// we cast if necessary
		return (round($data[0]) + 0);
	}
	return $data[0] + ($data[1]*5/3/100);
}

// Init datetime object
function initDateTime($date)
{
	$object = new Datetime();
	switch(CONFIG_DATE_FORMAT)	{
		case 'fr':
			if (!($object->setDate(substr($date,6,4), substr($date,3,2), substr($date,0,2))))
			{
				$object=false;
			}
			break;
		case 'us':
			$ret=$object->setDate(substr($date,6,4), substr($date,0,2), substr($date,3,2));
			if (!$ret)
			{
				$object=false;
			}
			break;
		case 'jp':
			if (!($object->setDate(substr($date,0,4), substr($date,5,2), substr($date,8,2))))
			{
				$object=false;
			}
			break;
	}
	return $object;
}

// check valid time
function is_valid_time($val)
{
  return ((!is_null($val) && preg_match("/([0-9]{2}):([0-9]{2}):([0-9]{2})/", $val)));
}

// creation de tooltip
function create_tooltip($tableau)
{
	global $smarty;
	$cooltip="";
	
	// Titre
	if($tableau['titre'] != '') 
	{
		$cooltip .= '<strong>' . $smarty->getConfigVars('winPeriode_titre') . '</strong> : ' . (str_replace(array("\r\n", "\n", "\""), array("<br>", "<br>", "'"), $tableau['titre'])).'<br />';
	}
	
	// Nom du projet
	$cooltip .=	'<strong>' . $smarty->getConfigVars('tab_projet') . '</strong> : ' . str_replace(array("\r\n", "\n", "\""), array("<br>", "<br>", "'"), $tableau['projet_nom']) .' (' . $tableau['projet_id'] . ')<br/>'. '<strong>' . $smarty->getConfigVars('tab_personne') . '</strong> : ' . $tableau['user_nom'] . ' (' . $tableau['user_id'] . ')';
	
	// Livrable
	if($tableau['livrable'] == 'oui') 
	{
		$cooltip .= '<br/><strong>' . $smarty->getConfigVars('winPeriode_livrable') . '</strong> : ' . (str_replace(array("\r\n", "\n", "\""), array("<br>", "<br>", "'"), $smarty->getConfigVars('oui')));
	}
	
	// Statut de la tâche
	if($tableau['statut_tache'] != '') 
	{
		$cooltip .= '<br/><strong>' . $smarty->getConfigVars('winPeriode_statut') . '</strong> : ' . (str_replace(array("\r\n", "\n", "\""), array("<br>", "<br>", "'"), $tableau['status']));
	}
	
	// Date de début
	$cooltip .= '<br/><strong>' . $smarty->getConfigVars('tab_dateDebut') . '</strong> : ' . sqldate2userdate($tableau['date_debut']);
	
	// Date de fin
	if($tableau['date_fin'] != '') 
	{
		$cooltip .= '<br/><strong>'	. $smarty->getConfigVars('tab_dateFin') .	'</strong> : ' . sqldate2userdate($tableau['date_fin']);
	} else {
		$cooltip .= '<br/><strong>' . $smarty->getConfigVars('tab_duree') . '</strong> : ' . sqltime2usertime($tableau['duree']);
		if(isset($tableau['duree_details_heure_debut'])) 
		{
			$cooltip .= ' (' . sqltime2usertime($tableau['duree_details_heure_debut']) . ' => ' . sqltime2usertime($tableau['duree_details_heure_fin']) . ')';
		}
		if($tableau['duree_details'] == 'AM') 
		{
			$cooltip .= ' (' . $smarty->getConfigVars('tab_matin') . ')';
		}
		if($tableau['duree_details'] == 'PM') 
		{
			$cooltip .= ' (' . $smarty->getConfigVars('tab_apresmidi') . ')';
		}
	}
	
	// Lieux
	if (CONFIG_SOPLANNING_OPTION_LIEUX == 1)
	{
		if($tableau['lieu'] != '') 
		{
			$cooltip .= '<br/><strong>' . $smarty->getConfigVars('winPeriode_lieu') . '</strong> : ' . str_replace(array("\r\n", "\n", "\""), array("<br>", "<br>", "'"), $tableau['lieu_nom']);
		}
	}
	
	// Ressources
	if (CONFIG_SOPLANNING_OPTION_RESSOURCES == 1)
	{
		if($tableau['ressource'] != '') 
		{
			$cooltip .= '<br/><strong>' . $smarty->getConfigVars('winPeriode_ressource') . '</strong> : ' . str_replace(array("\r\n", "\n", "\""), array("<br>", "<br>", "'"), $tableau['ressource_nom']);
		}
	}
	
	// Notes
	if($tableau['notes'] != '') 
	{
		$cooltip .= '<br/><strong>' . $smarty->getConfigVars('tab_commentaires') . '</strong> : ' . (str_replace(array("\r\n", "\n", "\""), array("<br>", "<br>", "'"), $tableau['notes']));
	}
	
	// Champ personnalisé
	if($tableau['custom'] != '') 
	{
		$cooltip .= '<br/><strong>' . $smarty->getConfigVars('winPeriode_custom') . '</strong> : ' . (str_replace(array("\r\n", "\n", "\""), array("<br>", "<br>", "'"), $tableau['custom']));
	}
	
	// Lien
	if($tableau['lien'] != '') 
	{
		$cooltip .= '<br/><strong>' . $smarty->getConfigVars('tab_lien') . '</strong> : ' . (str_replace(array("\r\n", "\n", "\""), array("<br>", "<br>", "'"), $tableau['lien']));
	}
	
	// Créateur
	$cooltip.="<hr />";
	if($tableau['nom_createur'] != '') 
	{
		$cooltip .= $smarty->getConfigVars('periode_creation').' '.$smarty->getConfigVars('periode_modifier_par').' '.($tableau['nom_createur'])." ".$smarty->getConfigVars('periode_le')." ".sqldatetime2userdatetime($tableau['date_creation']);
	}
	// Modification
	if($tableau['nom_modifier'] != '') 
	{
		$cooltip .= "<br />".$smarty->getConfigVars('periode_modification').' '.$smarty->getConfigVars('periode_modifier_par').' ' . ($tableau['nom_modifier'])." ".$smarty->getConfigVars('periode_le')." ".sqldatetime2userdatetime($tableau['date_modif']);
	}
	return $cooltip;
}


// Generation du cellProject
function createCellProject($infos)
{
	global $user,$base_ligne,$base_colonne,$dimensionCase,$weekend,$ferie,$current_date2,$totauxJourUsers,$TotalMaxJour,$ligne,$nbitems,$largeuritems,$planning,$totalHeuresUsers,$tabPadding,$max, $heureDebutMatin, $heureDebutAprem, $idCase;
	$cellule="";
	$classCell="cellProject";

	// Detection biseau
	$dureecell=convertHourToDecimal($infos['duree']);
	$dureejour=convertHourToDecimal(CONFIG_DURATION_DAY);
	if (CONFIG_PLANNING_DIFFERENCIE_TACHE_PARTIELLE == 1 and $base_ligne<>'heures' && $dureecell > 0 && $dureecell < $dureejour)
	{
		if($infos['duree_details'] == 'AM'){
			$biseau = true;
			$classCell="cellProjectBiseau1";
		} elseif($infos['duree_details'] == 'PM'){
			$biseau = true;
			$classCell="cellProjectBiseau2";
		} elseif($infos['duree'] > "04:00:00"){
			$biseau = false;
			$classCell = "cellProject";
		} elseif($infos['duree'] <= "04:00:00"){
			$biseau = true;
			$classCell="cellProjectBiseau1";
		} else{
			if($infos['duree_details_heure_debut'] < "13:00:00"){
				$biseau = true;
				$classCell="cellProjectBiseau1";
			} else{
				$biseau = true;
				$classCell="cellProjectBiseau2";
			}
		}
	}else {
		$biseau = false;
		$classCell = "cellProject";
	}

	// Cellule en lecture seule si equipe différente et droit de modification de son équipe seulement
	if($user->checkDroit('tasks_modify_team') && ( $infos['team_id'] != $_SESSION['user_groupe_id'])) {
	 $classCell.=" read-only";
	 $dragndropzone = '';
	}else $dragndropzone = 'draggable="true" ondrop="drop(event)" ondragleave="leaveDropZone(event);"';
	
	// Texte de la cellule
	$L0=(substr($infos['nom_cellule'], 0, CONFIG_PLANNING_CODE_WIDTH));
	
	// Calcul de la couleur du texte dans la case, selon la couleur de fond de la case
	if (CONFIG_PLANNING_COULEUR_TACHE == 0)
	{
		$couleurFond = $infos['couleur'];
	}else
	{
		$couleurFond = $infos['statut_couleur'];
	}
	if($couleurFond =='FFFFFF')
	{
		$couleurFond = 'fafafa';
	}
	$couleurTexte = buttonFontColor('#' . $couleurFond);

	// Définition des classes de la cellule
	// Masque les tâches du week-end si l'option est activée (uniquement pour le planning jour)
	if ($base_colonne=="heures" || $base_colonne=="users"){
		$classHidden = "";
	} else {
		if (CONFIG_PLANNING_DIFFERENCIE_WEEKEND == 0 && ($weekend==true || $ferie!=false)) {
			$classHidden = " hidden";
		} else {
			$classHidden = "";
		}
	}
	
	// Si une tache répétitive, on ajoute la classe rep
	if (!empty($infos['parent_id'])) {
		$classHidden .= " tacherepetitive";
	}
	
	// Définition de la case avec le code du projet avec définition d'un style personalisé
	$cellule .= '<div id="c_' . $infos['periode_id'] . '_' . $current_date2 . '" '.$dragndropzone.' class="'.$classCell.' tooltipster '.$classHidden.'" data-content="'.$L0.'" ondragstart="drag(event)"  title="'.$infos['tooltip'].'"	style="color:'.$couleurTexte.';background-color:#' . $couleurFond . ';';
	// Cas affichage heures
	if ($base_ligne=='heures')
	{
	 if (CONFIG_PLANNING_LINE_HEIGHT != '') {
		$hauteurcellule = CONFIG_PLANNING_LINE_HEIGHT + 0.8;
	 }else $hauteurcellule = 30;
	 
	 // calcul de l'offset
	 $padding=5;
	 $ligneId=$ligne['nom'];
	 if (isset($planning['taches_horaires_users'][$infos['user_id']][$ligneId]))
	 {
		foreach($planning['taches_horaires_users'][$infos['user_id']][$ligneId] as $u)
		{
			if (is_array($u))
			{ 
				$cle=$u[0];
			}else $cle=$u;
			
			if ($cle == $infos['periode_id'])
			{
				$largeur=strlen($infos['nom_cellule'])*3+22;
				$tabPadding[$infos['periode_id']]=$padding+$largeur;
				break;
			}else 
			{
				if($dimensionCase=='large') 
				{
					$largeur=130+20;
				}else $largeur=strlen($planning['periodes'][$cle]['nom_cellule'])*3+22;
				$largeur=$tabPadding[$cle];
				$largeur_element=strlen($planning['periodes'][$cle]['nom_cellule']);
				$padding=$largeur;
			}
		}
	 }
	 
	 // calcule de la hauteur de cellule
	 $hauteur = round($infos['dureeHeures']*2*$hauteurcellule,0)-5;
	 // On gère la demie-heure
	 $minutes_debut=$infos['heure_debut']-floor($infos['heure_debut']);
	 $minutes_fin=$infos['heure_fin']-floor($infos['heure_fin']);
	 
	 if ($minutes_debut > 0 and $minutes_debut < 0.5)
	 {
		$pourcentage=1-((0.5-$minutes_debut)/0.5);
		$offset_start=($hauteurcellule*$pourcentage);	
	 }
	 if ($minutes_debut > 0.5 and $minutes_debut < 1)
	 {
		$pourcentage=1-(($minutes_debut-0.5)/0.5);
		$offset_start=($hauteurcellule*$pourcentage);	
	 }
	 if ($minutes_fin > 0 and $minutes_fin < 0.5)
	 {
		$pourcentage=((0.5-$minutes_fin)/0.5);
		$offset_end=($hauteurcellule*$pourcentage);	
	 }
	 if ($minutes_fin > 0.5 and $minutes_fin < 1)
	 {
		$pourcentage=(($minutes_fin-0.5)/0.5);
		$offset_end=($hauteurcellule*$pourcentage);	
	 }
	 
	 //if ($minutes_fin > 0.5)
	 //{
	 // $pourcentage=((1-$minutes_fin)/0.5);
	 // $hauteur=$hauteur-($hauteurcellule*$pourcentage);
	 //}
	 //if ($minutes_fin < 0.5)
	 //{
	 // $pourcentage=((1-$minutes_fin)/0.5);
	 // $hauteur=$hauteur-($hauteurcellule*$pourcentage);
	  //$hauteur=$hauteur-($hauteurcellule*$pourcentage)-$hauteurcellule;
	 //}
	 $hauteur=$hauteur-$offset_start-$offset_end;
	 $cellule .= 'position:absolute;float:left;height:'.($hauteur + 1).'px;margin-top:'.$offset_start.'px;margin-left:'.$padding.'px';
	 
	 if($dimensionCase=='large') {
			$cellule .= ';min-width:130px;';
		}
	}
	
	// Cas des demies journées (affichage d'une case tronquée)
	if($dimensionCase=='reduit') {
		if ($biseau==true)
		{
			if($classCell == "cellProjectBiseau1"){
				$cellule .= 'background: linear-gradient(33deg, #' . $couleurFond . ' 0%, #' . $couleurFond . ' 50%, transparent 26%);';		
			} elseif($classCell == "cellProjectBiseau2"){
				$cellule .= 'background: linear-gradient(33deg, transparent 45%, #' . $couleurFond . ' 30%, #' . $couleurFond . ' 0%);';
			}
		}
	}

	// On barre le texte de la tâche
	if($infos['statut_barre'] == '1') {
			$cellule .= 'text-decoration:line-through;';
	}
	// On met le texte de la tâche en gras
	if($infos['statut_gras'] == '1') {
			$cellule .= 'font-weight:bold;';
	}
	// On met le texte de la tâche en italique
	if($infos['statut_italique'] == '1') {
			$cellule .= 'font-style:italic;';
	}
	// On met le texte de la tâche en souligne
	if($infos['statut_souligne'] == '1') {
			$cellule .= 'text-decoration:underline;';
	}
	
	$cellule .= '"';
				
	// Script d'ouverture du lien dans une nouvelle fenêtre (en cas de lien)
	if (!$user->checkDroit('tasks_modify_all') && !($user->checkDroit('tasks_modify_own_project') && $infos['projet_createur_id'] == $user->user_id) && !($user->checkDroit('tasks_modify_own_task') && ($infos['user_id'] == $user->user_id || $infos['projet_createur_id'] == $user->user_id)) && $infos['lien'] != '') {
		$cellule .=" onclick=\"javascript:window.open('".$infos['lien']."')\"";
	}
    if (!empty($idCase))
    {
       $cellule .=" data-parent='$idCase'";
    }
	$cellule .=" >";

	// Calcul du picto charge journaliere (case large)
	if($dimensionCase=='large') {
		$current_date2_explode=explode('_',$current_date2);
		$current_date2=$current_date2_explode[0];
		if ($base_ligne=="heures"){
			$ratioCharge = round(decimalHours($totauxJourUsers[$current_date2][$infos['user_id']]) / $TotalMaxJour, 1);
		} else {
			$ratioCharge = round(decimalHours($totauxJourUsers[$infos['user_id']][$current_date2]) / $TotalMaxJour, 1);
		}
		$ratio = round($ratioCharge * 10);
		if ($ratio > 10) {
			$ratio = 11;
		}
		if ($ratio == 0) {
			if ($infos['lien'] != '') {
				$cellule .= '<div class="jauge0"></div>';
				$cellule .= '<img src="assets/img/pictos/link.gif" class="picto-link" alt="" />';
			} else {
				$cellule .= '<div class="jauge0"></div>';
			}
		} else {
				if ($infos['lien'] != '') {
					$cellule .= '<div class="jauge0">';
					$cellule .= '<div class="jauge' . $ratio . '">';
					if ($ratio == 10) {
						$cellule .= '100';
					}
					$cellule .= '</div></div>';
					if (CONFIG_PLANNING_DIFFERENCIE_TACHE_LIEN==1) {
						$cellule .= '<i class="fa fa-share picto-link justify-content-end" aria-hidden="true"></i>';
					}
				} else {
					$cellule .= '<div class="jauge0">';
					$cellule .= '<div class="jauge' . $ratio . '">';
					if ($ratio == 10) {
						$cellule .= '100';
					}
					$cellule .= '</div></div>';
				}
			}
		}
				
		// Modif affichage simple ou complet
		$L1='';
		$L1=$infos['nom_cellule'];
		$L2=$infos['titre'];

		// Calcul de l'info du début
		if (isset($infos['duree_details_heure_debut'])) {
			$infoDebut=sqltime2usertime($infos['duree_details_heure_debut']);
		}elseif ($infos['duree_details'] == 'AM') {
			$infoDebut = $heureDebutMatin;
		}elseif ($infos['duree_details'] == 'PM') {
			$infoDebut = $heureDebutAprem;
		} else{
			$infoDebut = $heureDebutMatin;
		}
				
		// Calcul de la durée
		if (isset($infos['duree'])) {
			$affichageDuree=$infos['duree'];
		}else {
			$affichageDuree= CONFIG_DURATION_DAY . ':00:00';
		}
		if ($L2=='') {
			$L2='...';
		}
					
		// Si on est une case large, on affiche des informations détaillées
		if($dimensionCase=='large')
		{
			//mb_internal_encoding("UTF-8");
			$nom = xss_protect(substr($L1, 0, CONFIG_PLANNING_CODE_WIDTH_LARGE));
			$nom.= '<div class="largeCode">'.xss_protect(substr($L2,0,CONFIG_PLANNING_CODE_WIDTH_LARGE)).'</div>';
			$nom.= '<div class="startDateCell">'.$infoDebut.'h</div>';
			if ($base_ligne=="heures")
			{
			$nom.= '<div class="percentCell">'.round(decimalHours($affichageDuree),1).'h/'.round(decimalHours($totauxJourUsers[$current_date2][$infos['user_id']]),1).'h</div>';
			$nom.= '<div class="percentCell">'.round(decimalHours($affichageDuree),1).'h/'.round(decimalHours($totauxJourUsers[$current_date2][$infos['user_id']]),1).'h</div>';

			}else
			{
			$nom.= '<div class="percentCell">'.round(decimalHours($affichageDuree),1).'h/'.round(decimalHours($totauxJourUsers[$infos['user_id']][$current_date2]),1).'h</div>';
			$nom.= '<div class="percentCell">'.round(decimalHours($affichageDuree),1).'h/'.round(decimalHours($totauxJourUsers[$infos['user_id']][$current_date2]),1).'h</div>';
			}
			
			// Dans le format réduit, c'est le seul code
		}else {
			$nom = xss_protect(substr($L1, 0, CONFIG_PLANNING_CODE_WIDTH));
			if (CONFIG_PLANNING_DIFFERENCIE_TACHE_LIEN==1 and $infos['lien'] != '') 
			{
				$cellule .= '<i class="fa fa-share picto-link justify-content-end" aria-hidden="true"></i>';
			}
			if (CONFIG_PLANNING_DIFFERENCIE_TACHE_COMMENTAIRE==1 and $infos['notes'] != '') 
			{
				$cellule .= '<i class="fa fa-info-circle picto-comment justify-content-end" aria-hidden="true"></i>';
			}
			
		}

		// Affichage du nom ou du pictogramme si c'est un livrable
		if($infos['livrable'] == 'non') 
		{
			$cellule .= $nom;
		}else 
		{
			$cellule .= '<img src="assets/img/pictos/milestone.png" class="picto-milestone-'.$dimensionCase.'" alt="milestone" />' . $nom;
		}
		$cellule .= '</div>';
					
return $cellule;
}

// multisort array
function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
    $sort_col = array();
    foreach ($arr as $key=> $row) {
        $sort_col[$key] = $row[$col];
    }

    array_multisort($sort_col, $dir, $arr);
}

// multisort array
// Utilisation : array_sort_by_columns($données, 'champ1', SORT_DESC, 'champ2', SORT_ASC);
function array_sort_by_columns() {
    $args = func_get_args();
    $data = array_shift($args);
    foreach ($args as $n => $field) {
        if (is_string($field)) {
            $tmp = array();
            foreach ($data as $key => $row)
                $tmp[$key] = $row[$field];
            $args[$n] = $tmp;
            }
    }
    $args[] = &$data;
    call_user_func_array('array_multisort', $args);
    return array_pop($args);
}


// calcul durée 
function calcul_duree_heures_non_masquees($heure_debut,$heure_fin) {
	global $planning;
	$nbheures=0;
	$minutes_fin=$heure_fin-floor($heure_fin);
	$minutes_debut=$heure_debut-floor($heure_debut);

	$tabTranchesHoraires = explode(',', CONFIG_HOURS_DISPLAYED);

	if ($minutes_debut >= 0.5) 
	{
		$start=floor($heure_debut)+0.5;
	}else $start=floor($heure_debut);
	if ($minutes_fin > 0.5) 
	{
		$end=floor($heure_fin)+1;
	}elseif ($minutes_fin > 0)
	{
		$end=floor($heure_fin)+0.5;
	}else $end=floor($heure_fin);

	for($h=$start;$h<$end;$h=$h+0.5)
	{
		if (in_array(round($h,0),$tabTranchesHoraires)) 
		{
			$nbheures=$nbheures+0.5;
		}elseif ($h == ($end-0.5)) $nbheures=$nbheures+0.5;
	}
	//exit;
	// Si la dernière heure est masquée
	
	//if ($minutes_fin>0.5) $nbheures=$nbheures+0.5;
	//if(!in_array($heure_fin,$tabTranchesHoraires)) $nbheures=$nbheures+0.5;

//exit;
	
	return $nbheures;
}

function logAction($typeaction,$infos=null) {

	// Traces
		$trace = new Audit();
		$trace->audit_id = null;
		$trace->type = $typeaction;
		$trace->date_modif = date('Y-m-d H:i:s');
		$trace->user_modif = $_SESSION['user_id'];		
		
		if (isset($infos['user'])){$trace->user_id = $infos['user'];}else $trace->user_id = null;
		if (isset($infos['equipe'])){$trace->equipe_id = $infos['equipe'];}else $trace->equipe_id = null;
		if (isset($infos['projet'])){$trace->projet_id = $infos['projet'];}else $trace->projet_id = null;
		if (isset($infos['groupe'])){$trace->groupe_id = $infos['groupe'];}else $trace->groupe_id = null;
		if (isset($infos['periode'])){$trace->periode_id = $infos['periode'];}else $trace->periode_id = null;
		if (isset($infos['lieu'])){$trace->lieu_id = $infos['lieu_id'];}else $trace->lieu_id = null;
		if (isset($infos['ressource'])){$trace->ressource_id = $infos['ressource_id'];}else $trace->ressource_id = null;		
		if (isset($infos['statut'])){$trace->statut_id = $infos['statut'];}else $trace->statut_id = null;
		if (isset($infos['informations'])){$trace->informations = $infos['informations'];}else $trace->informations = null;

		$trace->nbmodifs = 0;
		if (($typeaction=='MT' or $typeaction=='MP' or $typeaction=='MU' or $typeaction=='ML' or $typeaction=='MR' or $typeaction=='MS' or $typeaction=='MG' or $typeaction=='ME') && isset($infos['old_data']))
		{
			$diff_value = array_diff_assoc($infos['old_data'],$infos['new_data']);
			unset($diff_value['date_modif']);
			foreach($diff_value as $cle => $valeur)
			{
				$old_values[$cle] = utf8_encode($infos['old_data'][$cle]);
				$new_values[$cle] = utf8_encode($infos['new_data'][$cle]);
			}
			$trace->anciennes_valeurs = json_encode($old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
			$trace->nouvelles_valeurs = json_encode($new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
			$trace->nbmodifs = count($new_values);
		}elseif (($typeaction=='DT' or $typeaction=='DP' or $typeaction=='DU' or $typeaction=='DL' or $typeaction=='DR' or $typeaction=='DS' or $typeaction=='DG' or $typeaction=='DE') && isset($infos['old_data']))
		{
			unset($infos['old_data']['saved']);
			unset($infos['old_data']['primaryKeys']);
			foreach($infos['old_data'] as $cle => $valeur)
			{
				$old_values[$cle] = utf8_encode($valeur);
				$new_values[$cle] = "-";
			}
			$trace->anciennes_valeurs = json_encode($old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
			$trace->nouvelles_valeurs = json_encode($new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
			$trace->nbmodifs = count($new_values);
		}else
		{
			if(!isset($infos['old_data'])) {
				$infos['old_data'] = array();
			}
			$trace->nouvelles_valeurs = null;
			$old_values = array_map('utf8_encode', $infos['old_data']);
			$trace->anciennes_valeurs = json_encode($old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );			
		}
		$trace->db_save();
}


function chargerSessionStatutsDefaut(){
	// Préférence de sélection par défaut
	$status = new GCollection('Status');
	$status->db_load(array('defaut', '=', 1, 'affichage', 'IN', array('t', 'tp')));
	foreach ($status->getSmartyData() as $s) {
		$_SESSION['status_taches_par_defaut'][] = $s['status_id'];
	}
	if(!isset($_SESSION['status_taches_par_defaut'])){
		$statuts = new GCollection('Status');
		$statuts->db_load(array('affichage', 'IN', array('t', 'tp')));
		if(count($_SESSION['status_taches_par_defaut']) >= $statuts->getCount()) {
			$_SESSION['status_taches_par_defaut'] = array();
		}
	} else{
		// si aucun statut par défaut, on prend le premier
		$status = new GCollection('Status');
		$status->db_load(array('affichage', 'IN', array('t', 'tp')));
		$_SESSION['status_taches_par_defaut'] = $status->get('status_id');
	}

	$_SESSION['status_projets_par_defaut'] = array();
	$status = new GCollection('Status');
	$status->db_load(array('defaut', '=', 1, 'affichage', 'IN', array('p', 'tp')));
	foreach ($status->getSmartyData() as $s) {
		$_SESSION['status_projets_par_defaut'][] = $s['status_id'];
	}
	if(!isset($_SESSION['status_projets_par_defaut'])){
		$statuts = new GCollection('Status');
		$statuts->db_load(array('affichage', 'IN', array('p', 'tp')));
		if(count($_SESSION['status_projets_par_defaut']) >= $statuts->getCount()) {
			$_SESSION['status_projets_par_defaut'] = array();
		}
	}
}

 // Suppression d'un repertoire
 function rrmdir($dir) {
   if (is_dir($dir)) {
     $objects = scandir($dir);
     foreach ($objects as $object) {
       if ($object != "." && $object != "..") {
         if (filetype($dir."/".$object) == "dir") rmdir($dir."/".$object); else unlink($dir."/".$object);
       }
     }
     reset($objects);
     rmdir($dir);
   }
 }

function cprdir($dir2copy,$dir_paste)
{    
	if (is_dir($dir2copy)) 
	{   
       if ($dh = opendir($dir2copy)) 
	   {                     
         while(($file = readdir($dh)) !== false) 
	     {                          
               if (!is_dir($dir_paste)) mkdir($dir_paste,777);                           
			   if (is_dir($dir2copy.$file) && $file != '..'  && $file != '.') 
					cprdir( $dir2copy.$file.'/' , $dir_paste.$file.'/');                                     
		       elseif($file != '..'  && $file != '.') copy ( $dir2copy.$file , $dir_paste.$file );
		 }
		 closedir($dh);
	   }               
	}
}

function replaceAccents($str) {
  $search = explode(",","ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,ø,Ø,Å,Á,À,Â,Ä,È,É,Ê,Ë,Í,Î,Ï,Ì,Ò,Ó,Ô,Ö,Ú,Ù,Û,Ü,Ÿ,Ç,Æ,Œ");
  $replace = explode(",","c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,o,O,A,A,A,A,A,E,E,E,E,I,I,I,I,O,O,O,O,U,U,U,U,Y,C,AE,OE");
  return str_replace($search, $replace, $str);
}