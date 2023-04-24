<?php

require 'base.inc';
require BASE . '/../config.inc';

class FailedAuthException extends Exception{}
class BadInputException extends Exception{}
class RessourceNotFoundException extends Exception{}
class SaveErrorException extends Exception{}

function checkAuth(){
	$keyname = str_replace('-', '_', 'HTTP_' . CONFIG_SOPLANNING_API_KEY_NAME);
	if(!isset($_SERVER[$keyname])){
		throw new FailedAuthException('API key not present');
	}
	if($_SERVER[$keyname] != CONFIG_SOPLANNING_API_KEY_VALUE){
		throw new FailedAuthException('API auth failed');
	}
	return;
}

// doc : https://github.com/marcj/php-rest-service

use RestService\Server;

$server = Server::create('/')
	->addGetRoute('test', function(){
		checkAuth();
		return 'working!';
	})

	///////////////// USERS /////////////////////////////

	->addGetRoute('users', function(){
		checkAuth();
		$users = new GCollection('User');
		$users->db_load(array('user_id', '<>', 'publicspl'), array('user_id' => 'ASC'));
		$data = array();
		while($userTmp = $users->fetch()){
			$data[] = $userTmp->getAPIData();
		}
		return $data;
	})

	->addPutRoute('users/([a-zA-Z0-9]+)', function($user_id, $name, $login = '', $email = '', $password = '', $visible = '', $color = '', $notification = '', $address = '', $phone = '', $mobile = '', $job = '', $comment = '', $active = '', $rights = '', $team_id = ''){
		checkAuth();

		$user = new User();
		try {
			call_user_func_array([$user, 'putAPI'], func_get_args());
		}
		catch (Exception $e) {
			$errorCommand = get_class($e);
			throw new $errorCommand($e->getMessage());
		}
		return $user->getAPIData();
	})

	->addGetRoute('users/([a-za-zA-Z0-9]+)', function(){
		checkAuth();
		$args = func_get_args();
		$user_id = $args[0];
		$user = new User();
		if(trim($user_id) == '' || !$user->db_load(array('user_id', '=', trim($user_id)))){
			throw new BadInputException('userID not found');
		}
		return $user->getAPIData();
	})

	->addDeleteRoute('users/([a-zA-Z0-9]+)', function(){
		checkAuth();
		$args = func_get_args();
		$user_id = $args[0];
		$user = new User();
		if(trim($user_id) == '' || !$user->db_load(array('user_id', '=', trim($user_id)))){
			throw new BadInputException('userID not found');
		}
		$user->db_delete();
		return $user->getAPIData();
	})

	///////////////// PROJECTS /////////////////////////////
	->addGetRoute('projects', function(){
		checkAuth();
		$projets = new GCollection('Projet');
		$projets->db_load(array(), array('projet_id' => 'ASC'));
		$data = array();
		while($projet = $projets->fetch()){
			$data[] = $projet->getAPIData();
		}
		return $data;
	})

	->addPutRoute('projects/([a-za-zA-Z0-9]+)', function($project_id, $name, $owner_id, $status_id = '', $charge = '', $delivery = '', $color = '', $link = '', $comment = '', $group_id = ''){
		checkAuth();
		$projet = new Projet();
		try {
			call_user_func_array([$projet, 'putAPI'], func_get_args());
		}
		catch (Exception $e) {
			$errorCommand = get_class($e);
			throw new $errorCommand($e->getMessage());
		}
		return $projet->getAPIData();
	})

	->addGetRoute('projects/([a-zA-Z0-9]+)', function($project_id){
		checkAuth();
		$projet = new Projet();
		if(!$projet->db_load(array('projet_id', '=', $project_id))){
			throw new RessourceNotFoundException('Not existing project ID');
		}
		return $projet->getAPIData();
	})

	->addDeleteRoute('projects/([a-zA-Z0-9]+)', function(){
		checkAuth();
		$args = func_get_args();
		$projet_id = $args[0];
		$projet = new Projet();
		if(trim($user_id) == '' || !$user->db_load(array('user_id', '=', trim($user_id)))){
			throw new BadInputException('userID not found');
		}
		$user->db_delete();
		return $user->getAPIData();
	})

	///////////////// TASKS /////////////////////////////

	->addGetRoute('tasks', function($user_id = '', $project_id = '', $start_date = '', $end_date = '', $sort_by = '', $sort_order = ''){
		checkAuth();

		$criterias = array();

		$userTmp = new User();
		if($user_id != ''){
			if(!$userTmp->db_load(array('user_id', '=', $user_id))){
				throw new Exception('user_id unknown');
			} else{
				$criterias[] = 'user_id';
				$criterias[] = '=';
				$criterias[] = $user_id;
			}
		}

		$projet = new Projet();
		if($project_id != ''){
			if(!$projet->db_load(array('projet_id', '=', $project_id))){
				throw new Exception('project_id unknown');
			} else{
				$criterias[] = 'projet_id';
				$criterias[] = '=';
				$criterias[] = $project_id;
			}
		}

		if($start_date != ''){
			if (!controlDateSql($start_date)) {
				throw new Exception('Start date not valid');
			} else{
				$criterias[] = 'date_debut';
				$criterias[] = '>=';
				$criterias[] = $start_date;
			}
		}

		if($end_date != ''){
			if (!controlDateSql($end_date)) {
				throw new Exception('End date not valid');
			} else{
				$criterias[] = 'date_debut';
				$criterias[] = '<=';
				$criterias[] = $end_date;
			}
		}

		$tabOrdre = array('periode_id' => 'ASC');
		if($sort_by != ''){
			$tab_sort_by = array('task_id' => 'periode_id', 'user_id' => 'user_id', 'project_id' => 'projet_id', 'start_date' => 'date_debut');
			if (!array_key_exists($sort_by, $tab_sort_by)) {
				throw new Exception('Error on sort_by. Possible values : task_id, user_id, project_id, start_date, or leave empty for default value (task_id)');
			} else{
				if($sort_order != ''){
					if (!in_array($sort_order, array('asc', 'desc'))) {
						throw new Exception('Error on sort_order. Possible values : asc, desc, or leave empty for default value (asc)');
					} else{
						$tabOrdre = array($tab_sort_by[$sort_by] => strtoupper($sort_order));
					}
				} else{
					$tabOrdre = array($tab_sort_by[$sort_by] => 'ASC');
				}
			}
		}

		$taches = new GCollection('Periode');
		$taches->db_load($criterias, $tabOrdre);
		$data = array();
		while($tache = $taches->fetch()){
			$data[] = $tache->getAPIData();
		}
		return $data;
	})

	->addPostRoute('tasks', function($task_id = '', $user_id, $project_id, $link_id = '', $start_date, $end_date = '', $start_time = '', $end_time = '', $duration = '', $status_id, $title = '', $comment = '', $link = '', $resource_id = '', $place_id = '', $milestone = '', $custom_field = '', $creator_id = '') {
		checkAuth();
		$periode = new Periode();
		try {
			call_user_func_array([$periode, 'putAPI'], func_get_args());
		}
		catch (Exception $e) {
			$errorCommand = get_class($e);
			throw new $errorCommand($e->getMessage());
		}
		return $periode->getAPIData();
	})

	->addGetRoute('tasks/([0-9]+)', function(){
		checkAuth();
		$args = func_get_args();
		$periode_id = $args[0];
		$periode = new Periode();
		if(trim($periode_id) == '' || !$periode->db_load(array('periode_id', '=', trim($periode_id)))){
			throw new BadInputException('taskID not found');
		}
		return $periode->getAPIData();
	})

	->addDeleteRoute('tasks/([0-9]+)', function(){
		checkAuth();
		$args = func_get_args();
		$periode_id = $args[0];
		$periode = new Periode();
		if(trim($periode_id) == '' || !$periode->db_load(array('periode_id', '=', trim($periode_id)))){
			throw new BadInputException('taskID not found');
		}
		$periode->db_delete();
		return $periode->getAPIData();
	})


	///////////////// RESOURCES /////////////////////////////

	->addGetRoute('resources', function(){
		checkAuth();
		$ressources = new GCollection('Ressource');
		$ressources->db_load(array(), array('ressource_id' => 'ASC'));
		$data = array();
		while($ressource = $ressources->fetch()){
			$data[] = $ressource->getAPIData();
		}
		return $data;
	})

	->addPutRoute('resources/([a-za-zA-Z0-9]+)', function($resource_id, $name, $comment = '', $exclusive = ''){
		checkAuth();
		$ressource = new Ressource();
		try {
			call_user_func_array([$ressource, 'putAPI'], func_get_args());
		}
		catch (Exception $e) {
			$errorCommand = get_class($e);
			throw new $errorCommand($e->getMessage());
		}
		return $ressource->getAPIData();
	})

	->addGetRoute('resources/([a-zA-Z0-9]+)', function(){
		checkAuth();
		$args = func_get_args();
		$ressource_id = $args[0];
		$ressource = new Ressource();
		if(trim($ressource_id) == '' || !$ressource->db_load(array('ressource_id', '=', trim($ressource_id)))){
			throw new BadInputException('ressourceID not found');
		}
		return $ressource->getAPIData();
	})

	->addDeleteRoute('resources/([a-zA-Z0-9]+)', function(){
		checkAuth();
		$args = func_get_args();
		$ressource_id = $args[0];
		$ressource = new Ressource();
		if(trim($ressource_id) == '' || !$ressource->db_load(array('ressource_id', '=', trim($ressource_id)))){
			throw new BadInputException('ressourceID not found');
		}
		$ressource->db_delete();
		return $ressource->getAPIData();
	})

	///////////////// PLACES /////////////////////////////

	->addGetRoute('places', function(){
		checkAuth();
		$lieux = new GCollection('Lieu');
		$lieux->db_load(array(), array('lieu_id' => 'ASC'));
		$data = array();
		while($lieu = $lieux->fetch()){
			$data[] = $lieu->getAPIData();
		}
		return $data;
	})

	->addPutRoute('places/([a-za-zA-Z0-9]+)', function($place_id, $name, $comment = '', $exclusive = ''){
		checkAuth();
		$lieu = new Lieu();
		try {
			call_user_func_array([$lieu, 'putAPI'], func_get_args());
		}
		catch (Exception $e) {
			$errorCommand = get_class($e);
			throw new $errorCommand($e->getMessage());
		}
		return $lieu->getAPIData();
	})

	->addGetRoute('places/([a-zA-Z0-9]+)', function(){
		checkAuth();
		$args = func_get_args();
		$lieu_id = $args[0];
		$lieu = new Lieu();
		if(trim($lieu_id) == '' || !$lieu->db_load(array('lieu_id', '=', trim($lieu_id)))){
			throw new BadInputException('placeID not found');
		}
		return $lieu->getAPIData();
	})

	->addDeleteRoute('places/([a-zA-Z0-9]+)', function(){
		checkAuth();
		$args = func_get_args();
		$lieu_id = $args[0];
		$lieu = new Lieu();
		if(trim($lieu_id) == '' || !$lieu->db_load(array('lieu_id', '=', trim($lieu_id)))){
			throw new BadInputException('placeID not found');
		}
		$lieu->db_delete();
		return $lieu->getAPIData();
	})


	///////////////// TEAMS /////////////////////////////

	->addGetRoute('teams', function(){
		checkAuth();
		$teams = new GCollection('User_groupe');
		$teams->db_load(array(), array('user_groupe_id' => 'ASC'));
		$data = array();
		while($team = $teams->fetch()){
			$data[] = $team->getAPIData();
		}
		return $data;
	})

	->addPostRoute('teams', function($team_id, $name){
		checkAuth();
		$user_groupe = new User_groupe();
		try {
			call_user_func_array([$user_groupe, 'putAPI'], func_get_args());
		}
		catch (Exception $e) {
			$errorCommand = get_class($e);
			throw new $errorCommand($e->getMessage());
		}
		return $user_groupe->getAPIData();
	})

	->addGetRoute('teams/([0-9]+)', function(){
		checkAuth();
		$args = func_get_args();
		$user_groupe_id = $args[0];
		$user_groupe = new User_groupe();
		if(trim($user_groupe_id) == '' || !$user_groupe->db_load(array('user_groupe_id', '=', trim($user_groupe_id)))){
			throw new BadInputException('teamID not found');
		}
		return $user_groupe->getAPIData();
	})

	->addDeleteRoute('teams/([0-9]+)', function(){
		checkAuth();
		$args = func_get_args();
		$user_groupe_id = $args[0];
		$user_groupe = new User_groupe();
		if(trim($user_groupe_id) == '' || !$user_groupe->db_load(array('user_groupe_id', '=', trim($user_groupe_id)))){
			throw new BadInputException('teamID not found');
		}
		$user_groupe->db_delete();
		return $user_groupe->getAPIData();
	})

	///////////////// GROUPS /////////////////////////////

	->addGetRoute('groups', function(){
		checkAuth();
		$groupes = new GCollection('Groupe');
		$groupes->db_load(array(), array('groupe_id' => 'ASC'));
		$data = array();
		while($groupe = $groupes->fetch()){
			$data[] = $groupe->getAPIData();
		}
		return $data;
	})

	->addPostRoute('groups', function($group_id, $name){
		checkAuth();
		$user_groupe = new User_groupe();
		try {
			call_user_func_array([$user_groupe, 'putAPI'], func_get_args());
		}
		catch (Exception $e) {
			$errorCommand = get_class($e);
			throw new $errorCommand($e->getMessage());
		}
		return $user_groupe->getAPIData();
	})

	->addGetRoute('groups/([0-9]+)', function(){
		checkAuth();
		$args = func_get_args();
		$groupe_id = $args[0];
		$groupe = new Groupe();
		if(trim($groupe_id) == '' || !$groupe->db_load(array('groupe_id', '=', trim($groupe_id)))){
			throw new BadInputException('groupID not found');
		}
		return $groupe->getAPIData();
	})

	->addDeleteRoute('groups/([0-9]+)', function(){
		checkAuth();
		$args = func_get_args();
		$groupe_id = $args[0];
		$groupe = new Groupe();
		if(trim($groupe_id) == '' || !$groupe->db_load(array('groupe_id', '=', trim($groupe_id)))){
			throw new BadInputException('GroupID not found');
		}
		$groupe->db_delete();
		return $groupe->getAPIData();
	})
	;

// ERROR MANAGEMENT
$server->setExceptionHandler(function(\Exception $e) use ($server) {
    if ($e instanceof BadInputException) {
        $server->getClient()->sendResponse('400', array(
            'error' => get_class($e),
            'message' => $e->getMessage()
        ));
    }
    if ($e instanceof FailedAuthException) {
        $server->getClient()->sendResponse('401', array(
            'error' => get_class($e),
            'message' => $e->getMessage()
        ));
    }
    if ($e instanceof RessourceNotFoundException) {
        $server->getClient()->sendResponse('404', array(
            'error' => get_class($e),
            'message' => $e->getMessage()
        ));
    }
    if ($e instanceof SaveErrorException) {
        $server->getClient()->sendResponse('500', array(
            'error' => get_class($e),
            'message' => $e->getMessage()
        ));
    }
});

$server->run();