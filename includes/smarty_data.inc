<?php
$smartyData = array();
if (isset($_SESSION['message'])) {
	$smartyData['message'] = $_SESSION['message'];
	unset($_SESSION['message']);
}
if (isset($_SESSION['erreur'])) {
	$smartyData['erreur'] = $_SESSION['erreur'];
	unset($_SESSION['erreur']);
}
$this->assign('smartyData', $smartyData);
?>