<?php
// Database Parameters => please modify the content of database.inc file, or use the installer
require BASE . '/../database.inc';

// Active Directory vars
$ADLogin = false;
$ADServer = "ldap://domaincontroller.yourcomapany.local";
$ADDomain = "YOURCOMPANY";

// LDAP vars
$ldapLogin = false;
$ldapUrl = "ldap://ldap.yourCompany.com"; // address or IP of LDAP serveur
$ldap_domain = 'DOMAINENAME'; // LDAP domain
$ldapBase = "ou=people,dc=society,dc=com";
$ldapFilter = "(&(objectClass=OpenLDAPperson)(uid={login}))";
$ldap_use_tls = false;
$ldapBindUser;
$ldapBindPassword = '';

// custom variables
define('COMPIL_DIR', BASE . '/../smarty/templates_c/'); // templates compil directory, must have read/write access
define('UPLOAD_DIR', BASE . '/../www/upload/files/'); // upload file directory, must have read/write access
define('SAVE_DIR', BASE . '/../www/upload/backup/'); // backup directory, must have read/write access

// number of results per page
define('NB_RESULT_PER_PAGE', 30);

// define how many items to display in the filter menus in the planning
define('FILTER_NB_USERS_PER_COLUMN', '18');
define('FILTER_NB_PROJECTS_PER_COLUMN', '18');
define('FILTER_NB_AERA_PER_COLUMN', '15');
define('FILTER_NB_RESSOURCES_PER_COLUMN', '15');
define('MIN_CELL_SIZE', '25');
define('MAX_CELL_SIZE', '200');
define('MIN_CODE_SIZE', '3');
define('MAX_CODE_SIZE', '40');
define('MAX_SIZE_UPLOAD', '20971520');
define('DEBUG', false);  // put TRUE for debug
define('DEV_MAIL', 'yourmail@domain.com');

// Redirection
define('HOME_REDIRECTION','Location: ' . BASE);

// ----------------------------------------------------------------------------
// DO NOT MODIFY ANYTHING AFTER THIS LINE
// ----------------------------------------------------------------------------

if(DEBUG) {
	error_reporting(E_ALL | E_ERROR | E_WARNING | E_PARSE | E_NOTICE | E_DEPRECATED | E_STRICT);
} else {
	error_reporting(E_ALL ^ (E_NOTICE | E_WARNING | E_DEPRECATED));
}

define('CRLF', "\n");

require_once BASE . '/../smarty/libs/Smarty.class.php';
function autoloadSOPlanning($nomClasse) {
	if($nomClasse == 'PHPMailer') {
		require BASE . '/../phpmailer/class.phpmailer.php';
	}elseif($nomClasse == 'SMTP') {
		require BASE . '/../phpmailer/class.smtp.php';
	}elseif($nomClasse == 'HTML2PDF_locale') {
		require BASE . '/../html2pdf/_class/locale.class.php';
	}elseif($nomClasse == 'HTML2PDF_myPdf') {
		require BASE . '/../html2pdf/_class/myPdf.class.php';
	}elseif($nomClasse == 'HTML2PDF_parsingCss') {
		require BASE . '/../html2pdf/_class/parsingCss.class.php';		
	}elseif($nomClasse == 'HTML2PDF_parsingHtml') {
		require BASE . '/../html2pdf/_class/parsingHtml.class.php';		
	}elseif($nomClasse == 'TCPDF') {
		require BASE . '/../html2pdf/vendor/tecnickcom/tcpdf/tcpdf.php';
	} elseif (is_file(BASE . '/../includes/class_' . strtolower($nomClasse) . '.inc')) {
		require BASE . '/../includes/class_' . strtolower($nomClasse) . '.inc';
	}
}
spl_autoload_register("autoloadSOPlanning");
require_once BASE . '/../vendor/autoload.php';

// redirection possible vers installer / upgrade
$version = new Version();
if($version->checkPhpVersion() !== TRUE) {
	if(strpos($_SERVER['REQUEST_URI'], '/install/') === FALSE) {
		echo "<script>top.location='" . BASE . "/install/'</script>";
		exit;
	}
}

// Librairies
require BASE . '/../includes/lib.inc';
require BASE . '/../includes/xajax_common.inc';
require BASE . '/../includes/db_wrapper.inc';

if($version->checkDBAccess() !== TRUE || $version->checkDatabaseVersion() !== TRUE) {
	if(strpos($_SERVER['REQUEST_URI'], '/install/') === FALSE) {
		echo "<script>top.location='" . BASE . "/install/'</script>";
		exit;
	}
} else {
	// chargement des données de config
	$configs = db_query('SELECT * FROM planning_config');
	while($configTemp = db_fetch_array($configs)) {
		define('CONFIG_' . $configTemp['cle'], $configTemp['valeur']);
	}
	// Ouverture de la session
	session_name($cfgDatabase . $cfgPrefix);
}

if(defined("CONFIG_TIMEZONE")){
	date_default_timezone_set(CONFIG_TIMEZONE);	
} else{
	date_default_timezone_set('Europe/Paris');	
}
header('Content-Type: text/html; charset=iso-8859-1');
header("X-Frame-Options: SAMEORIGIN");
session_start();

// Préférence format date
// on garde le format dd/mm/yyyy partout
if (isset($_SESSION['preferences']['dateformat']) && 1 == 0)
{
	switch($_SESSION['preferences']['dateformat'])	{
		case 'fr':
			define ('CONFIG_DATE_LONG','d/m/Y');
			define ('CONFIG_DATE_SHORT','d/m/y');	
			define ('CONFIG_DATE_SHORT2','d/m');
			define ('CONFIG_DATE_FIRST_DAY_MONTH','01/m/Y');		
			define ('CONFIG_DATE_DATEPICKER','dd/mm/yyyy');
			define ('CONFIG_DATE_FORMAT','fr');
			break;
		case 'us':
			define ('CONFIG_DATE_LONG','m-d-Y');
			define ('CONFIG_DATE_SHORT','m-d-y');		
			define ('CONFIG_DATE_SHORT2','m-d');
			define ('CONFIG_DATE_FIRST_DAY_MONTH','m-01-Y');		
			define ('CONFIG_DATE_DATEPICKER','mm-dd-yyyy');
			define ('CONFIG_DATE_FORMAT','us');
			break;
		case 'jp':
			define ('CONFIG_DATE_LONG','Y-m-d');
			define ('CONFIG_DATE_SHORT','y-m-d');		
			define ('CONFIG_DATE_SHORT2','m-d');
			define ('CONFIG_DATE_FIRST_DAY_MONTH','Y-m-01');		
			define ('CONFIG_DATE_DATEPICKER','yyyy-mm-dd');
			define ('CONFIG_DATE_FORMAT','jp');
			break;
		default	 :
			$_SESSION['preferences']['dateformat']='fr';
			define ('CONFIG_DATE_LONG','d/m/Y');
			define ('CONFIG_DATE_SHORT','d/m/y');
			define ('CONFIG_DATE_SHORT2','d/m');
			define ('CONFIG_DATE_FIRST_DAY_MONTH','01/m/Y');	
			define ('CONFIG_DATE_DATEPICKER','dd/mm/yyyy');
			define ('CONFIG_DATE_FORMAT','fr');
			break;
	}
}else {
		$_SESSION['preferences']['dateformat']='fr';
		define ('CONFIG_DATE_LONG','d/m/Y');
		define ('CONFIG_DATE_SHORT','d/m/y');
		define ('CONFIG_DATE_SHORT2','d/m');
		define ('CONFIG_DATE_FIRST_DAY_MONTH','01/m/Y');	
		define ('CONFIG_DATE_DATEPICKER','dd/mm/yyyy');
		define ('CONFIG_DATE_FORMAT','fr');	
	}
	define ('CONFIG_DATE_MYSQL','Y-m-d');
	
// Mobile Detection
$detect = new Mobile_Detect;
if($detect->isMobile())
{
	$_SESSION['isMobileOrTablet'] = 1;
} else {
	$_SESSION['isMobileOrTablet'] = 0;
}
$cfgAvailableLanguages = array(
	'fr'	=> array('fr([-_][[:alpha:]]{2})?|french', 'fr'),
	'it'	=> array('it([-_][[:alpha:]]{2})?|italian', 'it'),
	'en'	=> array('en([-_][[:alpha:]]{2})?|english', 'en'),
	'nl'	=> array('nl([-_][[:alpha:]]{2})?|nederlands', 'nl'),
	'de'	=> array('de([-_][[:alpha:]]{2})?|german', 'de'),
	'es'	=> array('es([-_][[:alpha:]]{2})?|spanish', 'es'),
	'da'	=> array('da([-_][[:alpha:]]{2})?|danish', 'da'),
	'hu'	=> array('hu([-_][[:alpha:]]{2})?|hungarian', 'hu'),
	'pt'	=> array('pt([-_][[:alpha:]]{2})?|portuguese', 'pt'),
	'pl'	=> array('pl([-_][[:alpha:]]{2})?|polish', 'pl'),
	'br'	=> array('pt-br?|brazilian', 'br'),
	'id'	=> array('id([-_][[:alpha:]]{2})?|indonesian', 'id')
);

$langueDefaut = 'en';
$lang = $langueDefaut;
if(isset($_GET) && !empty($_GET['language']) && in_array($_GET['language'], array('fr', 'en', 'it', 'nl', 'de', 'pt', 'da', 'hu', 'es', 'pl', 'br','id'))){
	$_SESSION['language'] = $_GET['language'];
	$lang = $_GET['language'];
} elseif (isset($_SESSION['language']) && in_array($_SESSION['language'], array('fr', 'en', 'it', 'nl', 'de', 'pt', 'da', 'hu', 'es', 'pl', 'br','id'))) {
	$lang = $_SESSION['language'];
} else {
	$_SESSION['language'] = detectLanguage($langueDefaut);
	$lang = detectLanguage($langueDefaut);
}

setlocale(LC_ALL, $lang . '_' . strtoupper($lang), $lang . '_' . strtoupper($lang) . '.ISO8859-1', $lang . '_' . strtoupper($lang) . '.ISO-8859-1', $lang . '_' . strtoupper($lang) . '.UTF-8', $lang . '_' . strtoupper($lang) . '.UTF8', $lang);

// possible values for sorting option in the planning
$triPlanningPossibleUser = array('nom asc', 'nom desc', 'user_id asc', 'user_id desc', 'team_nom asc, nom asc', 'team_nom desc, nom desc', 'team_nom asc, user_id asc', 'team_nom desc, user_id desc');
$triPlanningPossibleProjet = array('nom asc', 'nom desc', 'projet_id asc', 'projet_id desc', 'groupe_nom asc, nom asc', 'groupe_nom desc, nom desc', 'groupe_nom asc, projet_id asc', 'groupe_nom desc, projet_id desc');
$triPlanningPossibleAutre = array('nom asc', 'nom desc');

$default_palette="['#aa2e25','#f44336','#f6685e','#a31545','#e91e63','#ed4b82','#6d1b7b','#9c27b0'],
['#af52bf','#482880','#673ab7','#8561c5','#2c387e','#3f51b5','#6573c3','#1769aa'],
['#2196f3','#4dabf5','#0276aa','#03a9f4','#35baf6','#008394','#00bcd4','#33c9dc'],
['#00695f','#009688','#33ab9f','#357a38','#4caf50','#6fbf73','#618833','#8bc34a'],
['#a2cf6e','#8f9a27','#cddc39','#d7e360','#b2a429','#ffeb3b','#ffef62','#b28704'],
['#ffc107','#ffcd38','#b26a00','#ff9800','#ffac33','#b23c17','#ff5722','#ff784e'],
['#ffffff']";
?>