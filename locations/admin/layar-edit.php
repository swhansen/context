<?php
/*
    LightRod OAR Server

    Copyright (C) 2001 - 2010  High Country Software Ltd.

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see http://www.gnu.org/licenses/.



Usage:
	See http://www.lightrod.org/  'LightRod Layar' section for the most up-to-date instructions.

For assistance:
	peter AT lightrod.org
	or the LightRod forums
*/
require_once("../../config/db_connect.php");		//For the purposes of getting username and password
require_once("../../config/global_functions.php");		//For the purposes of getting username and password


authenticate($user_username, $user_password);

?>
<?php
//code below is simplified - in real app you will want to have some kins session based autorization and input value checking
//error_reporting(E_ALL ^ E_NOTICE);

//include db connection settings
//require_once('../../common/config.php');
//require_once('../../common/config_dp.php');


require("../../oar-server/cls.basic_geosearch.php");




function clean_data($string) {
	  //Use for cleaning input data before addition to database
	  if (get_magic_quotes_gpc()) {
	    $string = stripslashes($string);
	  }
	  $string = strip_tags($string);
	  return mysql_real_escape_string($string);
	}


function add_row($rowId){
	global $newId;

	$sql = 	"INSERT INTO tbl_layar_poi(int_point_id,var_type,var_title,var_line_2,var_line_3,
			var_line_4,var_image,dec_latitude, dec_longitude, var_actions_label_1, var_actions_uri_1)
			VALUES (0,
					'".clean_data($_REQUEST["c1"])."',
					'".clean_data($_REQUEST["c2"])."',
					'".clean_data($_REQUEST["c3"])."',
					'".clean_data($_REQUEST["c4"])."',
					'".clean_data($_REQUEST["c5"])."',
					'".clean_data($_REQUEST["c6"])."',
					'".clean_data($_REQUEST["c7"])."',
					'".clean_data($_REQUEST["c8"])."',
					'".clean_data($_REQUEST["c9"])."',
					'".clean_data($_REQUEST["c10"])."')";

	//echo $sql;
	$res = mysql_query($sql) or die("Unable to execute query $sql " . mysql_error());
	//set value to use in response
	$newId = mysql_insert_id();
	return "insert";
}

function update_row($rowId){

	$bg = new clsBasicGeosearch();
	$latitude = $_REQUEST["c7"];
	$longitude = $_REQUEST["c8"];
	$peano1 = $bg->generate_peano1($latitude, $longitude);		//Lat/lon of point in table
	$peano2 = $bg->generate_peano2($latitude, $longitude);
	$peano1iv = $bg->generate_peano_iv($peano1);
	$peano2iv = $bg->generate_peano_iv($peano2);

	//echo "Peano1: ".$peano1;

	$sql = 	"UPDATE tbl_layar_poi SET  var_type='".clean_data($_REQUEST["c1"])."',
				var_title=	'".clean_data($_REQUEST["c2"])."',
				var_line_2=	'".clean_data($_REQUEST["c3"])."',
				var_line_3=	'".clean_data($_REQUEST["c4"])."',
				var_line_4=	'".clean_data($_REQUEST["c5"])."',
				var_image=	'".clean_data($_REQUEST["c6"])."',
				dec_latitude=	'".clean_data($latitude)."',
				dec_longitude=	'".clean_data($longitude)."',
				int_peano1 = " . $peano1 .",
				int_peano2 = " . $peano2 . ",
				int_peano1iv = " . $peano1iv .",
				int_peano2iv = " . $peano2iv . ",
				var_actions_label_1=	'".clean_data($_REQUEST["c9"])."',
				var_actions_uri_1=	'".clean_data($_REQUEST["c10"])."'
			WHERE int_point_id=".$rowId;
	$res = mysql_query($sql) or die("Unable to execute query $sql " . mysql_error());;

	return "update";
}

function delete_row($rowId){

	$d_sql = "DELETE FROM tbl_layar_poi WHERE int_point_id=".$rowId;
	$resDel = mysql_query($d_sql) or die("Unable to execute query $sql " . mysql_error());;
	return "delete";
}


//include XML Header (as response will be in xml format)
header("Content-type: text/xml");
//encoding may differ in your case
echo('<?xml version="1.0" encoding="iso-8859-1"?>');


$mode = $_GET["!nativeeditor_status"]; //get request mode
$rowId = $_GET["gr_id"]; //id or row which was updated
$newId = $_GET["gr_id"]; //will be used for insert operation

switch($mode){
	case "inserted":
		//row adding request
		$action = add_row($rowId);
	break;
	case "deleted":
		//row deleting request
		$action = delete_row($rowId);
	break;
	default:
		//row updating request
		$action = update_row($rowId);
	break;
}


//output update results
echo "<data>";
echo "<action type='".$action."' sid='".$rowId."' tid='".$newId."'/>";
echo "</data>";

?>
