<?php

// default port for mysql
// not in database.inc for compliance with existing installs who won't update database.inc
$cfgDBPort = '3306';

function db_connect($cfgHostname, $cfgUsername, $cfgPassword, $cfgDatabase, $cfgSqlType, $cfgDBPort)
{
	global $cfgConnection;
	switch ($cfgSqlType) {
		case 'mysql':
			try { 
				$cfgConnection = @mysqli_connect($cfgHostname, $cfgUsername, $cfgPassword, '', $cfgDBPort);
			} catch (Exception $e) {
				return false;
			}
			break;
	}
	if (!$cfgConnection) {
		//die("<br /><font color=\"RED\"><b>Error</b> : Unable to connect to database. Please verify parameters.</font><br /><br />\n");
		return false;
	}
	mysqli_set_charset($cfgConnection, "latin1");
	try { 
		mysqli_select_db($cfgConnection, $cfgDatabase);
	} catch (Exception $e) {
		return false;
	}
	return $cfgConnection;
}

                                       
function db_close()
{
	global $cfgConnection, $cfgSqlType;
	if (isset($cfgConnection)) {
		switch ($cfgSqlType) {
			case 'mysql':
				mysqli_close($cfgConnection);
				break;
			case 'psql':
				pg_close($cfgConnection);
				break;
			default:
				break;
		}
		unset($GLOBALS['cfgConnection']);
	}
}


function db_query($query)
{
	global $cfgDatabase, $cfgConnection, $cfgSqlType, $cfgPrefix;
//	
	if($cfgPrefix != 'planning_') {
		$query = str_replace('planning_', $cfgPrefix, $query);
	}
	if(DEBUG) {
		//file_put_contents(BASE . '/../debug.txt', $query . "\r\n", FILE_APPEND);
	}
	switch ($cfgSqlType) {
		case 'mysql':
			$result = mysqli_query($cfgConnection, $query);
		/*
		echo $query;
		$row = db_fetch_array($result);
		echo '<pre>';
		print_r($row);
		echo '</pre>';
		die;
		*/
			break;
		case 'psql':			
			$result = pg_query($cfgConnection, $query);
			break;
		default:
			break;
	}
	if (!$result) {
		@file_put_contents(BASE . '/../debug.txt', $query . "\r\n", FILE_APPEND);
		if(DEBUG) {
			echo '<pre>';
			print_r(debug_backtrace());
			echo '</pre>';
			echo "<br /><font color=\"RED\"><b>Error</b> : querying database failed :</font><br />\n<font color=\"GRAY\">\n$query\n</font><br /><br />\n";
			echo '<br>Error : ' . mysqli_error($cfgConnection);
		} else {
			//echo ("<br /><font color=\"RED\"><b>Error</b> : querying database failed : refresh the page. If the problem persists, verify your database parameters or turn on DEBUG param in config file (set TRUE) to capture error and report to the support team support@soplanning.org</font><br />\n");
		}
	}

	return $result;
}


function db_num_rows($handle = 0)
{
	global $cfgSqlType;
	switch ($cfgSqlType) {
		case 'mysql':
			return ($handle) ? mysqli_num_rows($handle) : mysqli_num_rows();
			break;
		default:
			return ($handle);
			break;
	}
}

function db_result($handle, $row, $field)
{
	global $cfgSqlType;
	switch ($cfgSqlType) {
		case 'mysql':
			return mysqli_result($handle, $row, $field);
			break;
		case 'psql':
			return pg_fetch_result($handle, $row, $field);
			break;
		default:
			break;
	}
	return true;
}


function db_fetch_array($handle)
{
	global $cfgSqlType, $cfgConnection;
	switch ($cfgSqlType) {
		case 'mysql':
			$value = mysqli_fetch_array($handle, MYSQLI_ASSOC);
			return $value;
			break;
		case 'psql':
			$value =  pg_fetch_array($handle);
			return $value;
			break;
		default:
			return ($handle);
			break;
	}
}


function db_data_seek($handle, $row)
{
	global $cfgSqlType;
	switch ($cfgSqlType) {
		case 'mysql':
			return mysqli_data_seek($handle, $row);
			break;
		default:
			return True;				
			break;
	}
}


