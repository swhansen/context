<?php
	//Simple install script for SQL file
	//Courtesy: http://forums.tizag.com/archive/index.php?t-3581.html

require("config/global_functions.php");

function create_db_connect($db_username, $db_password, $db_name, $google_maps_key)
{


	//Test whether the database usernames are correct first
	if(mysql_connect("72.32.213.122", $db_username, $db_password)) {
	
	
	
		//The browser config  	
		if($google_maps_key != "") {
			if(($my_browser_config = file_get_contents("config/browser.php.original", FILE_TEXT))==false) {
				echo "Sorry, we could not read the file config/browser.php.original";
				return false;
			}
	
			$my_browser_config = str_ireplace("MY KEY HERE", $google_maps_key, $my_browser_config);
		
			if(!file_put_contents("config/browser.php", $my_browser_config)) {
				echo "Sorry, I could not write to the configuration file.  Please change the permissions of the directory config/ to 'world-readable-writable'.  E.g. chmod o+rw config/";
				return false;
			} 
		}
	
	
	
		//The database connection config
		$my_config = file_get_contents("config/db_connect.php.original", FILE_TEXT);
	
		//echo "Location:" .strstr($my_config, "Master"); 
	
		$my_config = str_ireplace("ENTER USERNAME HERE", $db_username, $my_config);
		$my_config = str_ireplace("ENTER PASSWORD HERE", $db_password, $my_config);
		$my_config = str_ireplace("my_locations", $db_name, $my_config);
	
		if(!file_put_contents("config/db_connect.php", $my_config)) {
			echo "Sorry, I could not write to the configuration file.  Please change the permissions of the directory config/ to 'world-readable-writable'.  E.g. chmod o+rw config/";
			return false;
		} 
		
		
		
		
		
		
	} else {
		echo "Sorry we could not connect to the database with that username or password.  Please try again.";
		return false;
	}   
	
	return true;
}


if(!file_exists("config/db_connect.php")) {
	//Get parameters from user input, if not already set
	
	if(!isset($db_username)) {
		
		echo "Please enter your database's username:";
		$handle = fopen ("php://stdin","r");
		$db_username = fgets($handle);
		$db_username = str_replace("\n","", $db_username);
	
		echo "Please enter your database's password:";
		$handle = fopen ("php://stdin","r");
		$db_password = fgets($handle);
		$db_password = str_replace("\n","", $db_password);
		
		echo "Please enter your database's name:";
		$handle = fopen ("php://stdin","r");
		$db_name = fgets($handle);
		$db_name = str_replace("\n","", $db_name);
		
		echo "Please enter your google maps API key:";
		$handle = fopen ("php://stdin","r");
		$google_maps_key = fgets($handle);
		$google_maps_key = str_replace("\n","", $google_maps_key);
	
	}
	
	if(!($db_name = database_name($db_name))) {
		echo "Your database name should not include '-' or other special characters.";
		exit(0);
	}
	
	if(!create_db_connect($db_username, $db_password, database_name($db_name), $google_maps_key)) {
		exit(0);
	}
	

}

//Connect up to the database
require("config/db_connect.php");

//This for multiple users setup - install.  Run
//		 php install.php username password database_name
//assuming you already have a database user by that name
if($argv[1]) {
	$db_username = $argv[1];
	$db_password = $argv[2];
	$db_name = $argv[3];
	
	mysql_query("CREATE USER '".$db_username ."'@'".$db_host."' IDENTIFIED BY '".$db_password."'");
	mysql_query("CREATE DATABASE " . $db_name  . " DEFAULT CHARACTER SET utf8");		
	mysql_query("GRANT ALL PRIVILEGES ON " .$db_name.".* TO '".$db_username ."'@'".$db_host."'");
}

	

$db_name = database_name($db_name);
mysql_query("CREATE DATABASE " . $db_name . " DEFAULT CHARACTER SET utf8");		//If this fails it already exists anyway
mysql_select_db($db_name);
$file_to_install = "locations/layar_standard.sql";
$last_error;

function execute_file ($file) {
	
	global $last_error;	
	
	// executes the SQL commands from an external file.

	if (!file_exists($file)) {
		$last_error = "The file $file does not exist.\n";
		return false;
	}
	$str = file_get_contents($file);
	if (!$str) {
		$last_error = "Unable to read the contents of $file.\n";
		return false;
	}

	// split all the queries into an array
	$quote = '';
	$line = '';
	$sql = array();
	$ignoreNextChar = FALSE;
	for ($i = 0; $i < strlen($str); $i++) {
		$char = $str[$i];
		$line .= $char;
		if ( !$ignoreNextChar ) {
			if ($char == ';' && $quote == '') {
				$sql[] = $line;
				$line = '';
			} else if ( $char == '\\' ) {
				// Escape char; ignore the next char in the string
				$ignoreNextChar = TRUE;
			} else if ($char == '"' || $char == "'" || $char == '`') {
				if ( $quote == '' ) // Start of a new quoted string; ends with same quote char
					$quote = $char;
				else if ( $char == $quote ) // Current char matches quote char; quoted string ends
					$quote = '';
			}
		}
		else
			$ignoreNextChar = FALSE;
		}

		if ($quote != '') return false;

		foreach ($sql as $query) {
			if (!empty($query)) {
				$r = mysql_query($query);

				if (!$r) {
					$last_error = mysql_error();
					echo $last_error;
					return false;
				}
			}
		}
		return true;
	}


if(execute_file( $file_to_install ) == true)  {
	echo "\nInstall was successful.\n\nYou will need to change the permissions of /locations/pics directory to 777 to be able to upload pictures.  E.g. on the command line: chmod 777 locations/pics, or use your FTP package.\n";
} else {
	echo "\nInstall not completed.\n" . $last_error;
}

?>
