<?php

define('CRON_TRACE', true);

require 'base.inc';
require BASE . '/../config.inc';


$sql = "
select link_id 
from planning_periode
group by link_id
having count(link_id) > 1
";
$req = db_query($sql);
echo db_num_rows($req) . '<br>';

while($row = db_fetch_array($req)){
	$col2 = new GCOllection('Periode');
	$sql = "SELECT * from planning_periode where link_id = '" . $row['link_id'] . "' ORDER BY date_debut";
	$col2->db_loadSQL($sql);
	$premier = true;
	while($obj2 = $col2->fetch()){
		if($premier === true){
			$premier = $obj2->date_debut;
		}
		if($obj2->date_debut != $premier){
			$obj2->link_id = uniqid(mt_rand());
			$obj2->db_save();
			echo 'change link_id ' . $obj2->periode_id . '<br>';
		}
	}
}

echo '<br>FIN';
?>