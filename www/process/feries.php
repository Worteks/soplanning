<?php

require 'base.inc';
require BASE . '/../config.inc';

require BASE . '/../includes/header.inc';

if(!$user->checkDroit('parameters_all')) {
	$_SESSION['erreur'] = 'droitsInsuffisants';
	header('Location: ../index.php');
	exit;
}


if(isset($_GET['fichier']) && is_file(BASE . '/../holidays/' . basename($_GET['fichier']))) {

	$v = new vcalendar();
	//$config = array("directory" => "calendar", "filename" => $_GET['fichier'], 'dirfile' => BASE . '/../holidays/');
	$config = array("unique_id" => "SOPlanning", "directory" => BASE . '/../holidays/', "filename" => basename($_GET['fichier']));
	//$config = array("unique_id" => "SOPlanning", "url" => 'http://www.google.com/calendar/ical/fr.french%23holiday%40group.v.calendar.google.com/public/basic.ics');
	$v->setConfig($config);
	$v->parse();
	//$v->sort();

	$dateCourante = new DateTime();
	$dateFinale = clone $dateCourante;
	$dateFinale->modify('+5 years');
	$eventArray = $v->selectComponents($dateCourante->format('Y'), $dateCourante->format('n'), $dateCourante->format('j'), $dateFinale->format('Y'), $dateFinale->format('n'), $dateFinale->format('j'));
	// select components occuring today
	// (including components with recurrence pattern)
	if(count($eventArray) == 0) {
		$_SESSION['message'] = 'changeOK';
		header('Location: ../feries.php');
		exit;
	}
	foreach($eventArray as $year => $yearArray) {
		foreach( $yearArray as $month => $monthArray ) {
			foreach( $monthArray as $day => $dailyEventsArray ) {
				foreach( $dailyEventsArray as $vevent ) {
					$currddate = $vevent->getProperty( "x-current-dtstart" );
					$summary = $vevent->getProperty("summary");
					$ferie = new Ferie();
						//echo $currddate[1] . '<br>';
					if(!$ferie->db_load(array('date_ferie', '=', $currddate[1]))) {
						$ferie->date_ferie = $currddate[1];
						$ferie->libelle = utf8_decode($summary);
						if(!$ferie->db_save()) {
							echo 'problem while saving : ' . print_r($ferie->getData());
							continue;
						}
					}
				}
			}
		}
	}

}

$_SESSION['message'] = 'changeOK';
header('Location: ../feries.php');
exit;

?>