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
	See http://www.lightrod.org/  'LightRod OAR Browser' section for the most up-to-date instructions.

For assistance:
	peter AT lightrod.org
	or the LightRod forums
*/


require('../../config/db_connect.php');



function XMLClean($strin) {
        $strout = null;

        for ($i = 0; $i < strlen($strin); $i++) {
                $ord = ord($strin[$i]);

                if (($ord > 0 && $ord < 32) || ($ord >= 127)) {
                        $strout .= "&amp;#{$ord};";
                }
                else {
                        switch ($strin[$i]) {
                                case '<':
                                        $strout .= '&lt;';
                                        break;
                                case '>':
                                        $strout .= '&gt;';
                                        break;
                                case '&':
                                        $strout .= '&amp;';
                                        break;
                                case '"':
                                        $strout .= '&quot;';
                                        break;
                                default:
                                        $strout .= $strin[$i];
                        }
                }
        }

        return $strout;
}




//include XML Header (as response will be in xml format)
header("Content-type: text/xml");
//encoding may differ in your case
echo('<?xml version="1.0" encoding="iso-8859-1"?>'); 

/* Export XML in this format: <?xml version="1.0" encoding="UTF-8"?>
    <rows>
        <row id="27">
            <cell>1</cell>
            <cell>100</cell>
            <cell>100</cell>
            <cell>399</cell>
            <cell>399</cell>
            <cell>100</cell>
            <cell>399</cell>
            <cell>100</cell>
            <cell>399</cell>
            <cell>100</cell>
            <cell>399</cell>
        </row>
    </rows>  
    
    int_point_id,var_type,var_title,var_line_2,var_line_3,
			var_line_4,var_image,dec_latitude, dec_longitude, var_actions_label_1, var_actions_uri_1
    
    */

echo "<rows>\n";
    
$sql = "SELECT * FROM tbl_layar_poi LIMIT 500";		//The limit just makes it possible to have larger numbers
								//of records, even though they're not visible.
$res = mysql_query($sql) or die("Unable to execute query $sql " . mysql_error());

while($row = mysql_fetch_array($res)) {

	echo "\t<row id='" . $row['int_point_id'] . "'>\n";
	
	echo "\t\t<cell style='color:blue;' bgcolor='gray'>" . $row['int_point_id'] . "</cell>\n";
	echo "\t\t<cell>" . XMLClean($row['var_type']) . "</cell>\n";
	echo "\t\t<cell>" . XMLClean($row['var_title']) . "</cell>\n";
	echo "\t\t<cell>" . XMLClean($row['var_line_2']) . "</cell>\n";
	echo "\t\t<cell>" . XMLClean($row['var_line_3']) . "</cell>\n";
	echo "\t\t<cell>" . XMLClean($row['var_line_4']) . "</cell>\n";
	echo "\t\t<cell>" . XMLClean($row['var_image']) . "</cell>\n";
	echo "\t\t<cell>" . XMLClean($row['dec_latitude']) . "</cell>\n";
	echo "\t\t<cell>" . XMLClean($row['dec_longitude']) . "</cell>\n";
	echo "\t\t<cell>" . XMLClean($row['var_actions_label_1']) . "</cell>\n";
	echo "\t\t<cell>" . XMLClean($row['var_actions_uri_1']) . "</cell>\n";
	
	echo "\t</row>";
}

echo "</rows>";
