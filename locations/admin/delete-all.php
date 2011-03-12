<?php
//set the error level of the website
//error_reporting(E_ERROR | E_PARSE);


require_once("classes/cls.csv_import.php");
require_once("../../config/db_connect.php");


$csv = new clsCSVImport();

$csv->delete_all();

header("location: index.php");

?>
