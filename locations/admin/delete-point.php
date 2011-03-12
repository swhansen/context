<?php
//set the error level of the website
//error_reporting(E_ERROR | E_PARSE);


require_once("classes/cls.csv_import.php");
require_once("../../config/db_connect.php");
require_once("../../config/global_functions.php");		//For the purposes of getting username and password

authenticate_api($user_username, $user_password);

$csv = new clsCSVImport();

$csv->delete_row($_REQUEST['point_id']);

echo "success";

?>
