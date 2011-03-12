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
	See http://www.lightrod.org/  'Getting Started' section for the most up-to-date instructions.

For assistance:
	peter AT lightrod.org
	or the LightRod forums
*/

require("../oar-server/cls.basic_geosearch.php");
require("../config/db_connect.php");

$bg = new clsBasicGeosearch();


$sql = "SELECT int_point_id, dec_latitude, dec_longitude FROM tbl_layar_poi";

$result = mysql_query($sql) or die("Unable to execute query $sql " . mysql_error());


while ($row = mysql_fetch_assoc($result)) {
	//Loop through the list
	$peano1 = $bg->generate_peano1($row['dec_latitude'], $row['dec_longitude']);		//Lat/lon of point in table
	$peano2 = $bg->generate_peano2($row['dec_latitude'], $row['dec_longitude']);
	$peano1iv = $bg->generate_peano_iv($peano1);
	$peano2iv = $bg->generate_peano_iv($peano2);
	
	$sql = "UPDATE tbl_layar_poi SET int_peano1 = '" . $peano1 ."',
					int_peano2 = '" . $peano2 . "',
					int_peano1iv = '" . $peano1iv ."',
					int_peano2iv = '" . $peano2iv . "' WHERE int_point_id = " . $row['int_point_id'];
	//echo $sql;
	mysql_query($sql) or die("Unable to execute query $sql " . mysql_error());
				
}




?>
