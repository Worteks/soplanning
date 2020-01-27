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

		require BASE . '/../includes/smarty_data.inc';

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