<?php

class MySmarty extends Smarty {

	/**
     * This is the constructor required to create an object
	 * @global	array	$cfgAvailableLanguages	an array containing all available languages
	 */
    public function __construct($lang = 'fr', $module = array()) {
		global $cfgAvailableLanguages;

		if(isset($_SESSION['language'])){
			$lang = $_SESSION['language'];
		}
		
		parent::__construct();

		$this->error_reporting = E_ALL;

		$this->template_dir = BASE . '/../templates/';
		$this->config_dir = BASE . '/../templates/languages/';
		$this->compile_dir = COMPIL_DIR;
		$this->cache_dir = BASE . '/../smarty/cache/';

		$this->assign('BASE', BASE);
		$this->assign('lang', $lang);

		// no boolean in the config file
		$this->config_booleanize = FALSE;
		$this->loadFilter('output', 'trimwhitespace');
		
		// we load modules from the config file NOW
		if (count($module) > 0) {
			foreach ($module as $key => $value){
				$this->configLoad($cfgAvailableLanguages[$lang][1] . '.txt', $value);
			}
		} else {
			$this->configLoad($cfgAvailableLanguages[$lang][1] . '.txt');
		}

		$this->registerPlugin("modifier","xss_protect", "xss_protect");
		$this->registerPlugin("modifier","urlencode", "urlencode");
		$this->registerPlugin("modifier","sqldate2userdate", "sqldate2userdate");
		$this->registerPlugin("modifier","forceISODateFormat", "forceISODateFormat");
		$this->registerPlugin("modifier","explode", "explode");
		$this->registerPlugin("modifier","sqltime2usertime", "sqltime2usertime");
		$this->registerPlugin("modifier","str_replace", "str_replace");
		$this->registerPlugin("modifier","formatMessage", "formatMessage");
		$this->registerPlugin("modifier","delete_session_value", "delete_session_value");
		$this->registerPlugin("modifier","json_encode", "json_encode");
		$this->registerPlugin("modifier","substr", "substr");
		$this->registerPlugin("modifier","buttonFontColor", "buttonFontColor");
		$this->registerPlugin("modifier","sqldatetime2userdatetime", "sqldatetime2userdatetime");
		$this->registerPlugin("modifier","strpos", "strpos");
		$this->registerPlugin("modifier","formaterNombreDecimal", "formaterNombreDecimal");
		$this->registerPlugin("modifier","strtoupper", "strtoupper");
		$this->registerPlugin("modifier","basename", "basename");
		

	}

	/**
	 * returns the content of Smarty's output buffer instead of displaying it
	 * @param	string	$resourceName	the name of the template
	 * @param	integer	$cacheId		l'id du cache
	 * @param	integer	$compileId		l'id de la compil
	 * @return	string					some html code
	 */
    public function getHtml($resourceName, $cacheId = NULL, $compileId = NULL)
    {
        return $this->fetch($resourceName, $cacheId, $compileId, FALSE);
    }
}

?>