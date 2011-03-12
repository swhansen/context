<?php
//set the error level of the website
//error_reporting(E_ERROR | E_PARSE);


require_once("classes/cls.csv_import.php");
require_once("../../config/db_connect.php");
require_once("../../config/global_functions.php");		//For the purposes of getting username and password




//If this is an API call 
if(isset($_REQUEST['user'])) {

	authenticate_api($user_username, $user_password);
	
	$data = $_REQUEST['data'];
	//echo "Data:" . $data;		//TESTING IN
	
	$csv = new clsCSVImport();
	
	//Display XML
	header("Content-type: text/xml");
	//encoding may differ in your case
	echo('<?xml version="1.0" encoding="utf-8"?>'); 
	echo "\n<nakdreality>\n";
	
	
	if($csv->read_file(null, $data)) {
		echo "\t<status>success</status>\n";
		echo "\t<ids>";
		$first = true;
		foreach($csv->ids_imported as $id) {
			if($first == false) {
				echo ",";
			}
			echo $id;
			$first = false; 
		}
		echo "</ids>\n";
	} else {
		echo "\t<status>failure</status>\n";
	}
	
	echo "</nakdreality>\n";
	exit(0);
	   
} else {

	authenticate($user_username, $user_password);
}

//No this is an ordinary user interface upload
$csv = new clsCSVImport();

if($csv->read_file($_FILES[ 'file' ][ 'tmp_name' ])) {
	echo "File has been imported successfully.  Remember to refresh your Admin panel to see the new data.";
} else {
	echo "Sorry, there was an error importing that csv file.  Please ensure it is comma delimited.";
}


?>
