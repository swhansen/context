<?php

//This file runs through all the existing databases specified in the config and carries out any table additions


//Connect up to the database
require_once("config/db_connect.php");
require_once("config/global_functions.php");		//For the purposes of getting username and password


authenticate($user_username, $user_password);


function upgrade_tables()
{
	$fields = array();
	$sql = "SHOW COLUMNS FROM tbl_layar_poi";
	$result = mysql_query($sql) or die("Unable to execute query $sql " . mysql_error());
	while($row = mysql_fetch_assoc($result)) {
		$fields[$row['Field']] = $row;
	}

	//print_r($fields);

	$upgraded = false;

	//Only alter table if we haven't
	if(!$fields['var_address']) {
		$sql = "ALTER TABLE tbl_layar_poi ADD var_address VARCHAR(1000) NULL";
		mysql_query($sql) or die("Unable to execute query $sql " . mysql_error());

		$upgraded = true;		//Switch on to updated

	}

	if($upgraded == true) {
		echo " Successful<br>";
	} else {
		echo " Already up-to-date<br>";
	}


}


echo "Upgrading " . $db_name . ":";
upgrade_tables();

mysql_close();


//Loop through each of the databases in turn, and apply the same database changes to that table
foreach($sources as $dir_source => $values)
{
	//If not one of the standard starting configs
	if(($dir_source != "")&&
		($dir_source != "my_source1")&&
		($dir_source != "my_source2")) {

		$db_host = $values["db_host"];
		$db_username = $values["db_username"];
		$db_password = $values["db_password"];
		$db_name = $values["db_name"];

		$db = mysql_connect($db_host, $db_username, $db_password);
		mysql_select_db($db_name);

		echo "Upgrading " . $db_name . ":";
		upgrade_tables();

		mysql_close();

	}
}

echo "All databases have been upgraded successfully.";

?>
