<?php


	function connect_merge($my_source, $sources)
	{	
		//Connect to a second database that we are merging results with
		global $db_host;
		global $db_username;
		global $db_password;
		global $db_name;
		global $db;
	
		//A secondary database connection, called if we're merging two layers at once
		$dir_source = $_REQUEST['dir_source'];
		$dir_source = $sources[$dir_source]["merge_with"];
	
		if($dir_source != "") {
			$db_host = $sources[$dir_source]["db_host"];
			$db_username = $sources[$dir_source]["db_username"];
			$db_password = $sources[$dir_source]["db_password"];
			$db_name = $sources[$dir_source]["db_name"];
		
			$db = mysql_connect($db_host, $db_username, $db_password);
			mysql_select_db($db_name);
		
			return true;
		}
		
		return false;			
	}


	function authenticate($db_username, $db_password) 
	{
		global $encrypted_pass;
		
		//Handle encrypted passwords
		if((isset($encrypted_pass))&&($encrypted_pass == true)) {
			$server_pass = md5($_SERVER['PHP_AUTH_PW']);
		} else {
			$server_pass = $_SERVER['PHP_AUTH_PW'];
		}
	
		if (!isset($_SERVER['PHP_AUTH_USER'])) {
		    header('WWW-Authenticate: Basic realm="Admin Panel"');
		    header('HTTP/1.0 401 Unauthorized');
		    echo 'Sorry only authorized access is allowed';
		    exit;
		} else {
			if (($_SERVER['PHP_AUTH_USER'] ==  $db_username) && ($server_pass == $db_password)) {
			    //print "Welcome to the private area!";
			} else {
			    header("WWW-Authenticate: Basic realm=\"Private Area\"");
			    header("HTTP/1.0 401 Unauthorized");
			    print "Sorry - you need valid credentials to be granted access!\n";
			    exit;
			}
		}
	}
	
	function authenticate_api($user_username, $user_password) 
	{
		global $encrypted_pass;
		
		//Handle encrypted passwords
		if((isset($encrypted_pass))&&($encrypted_pass == true)) {
			$user_entered_password = md5($_REQUEST['pass']);
		} else {
			$user_entered_password = $_REQUEST['pass'];
		}
		
		$user_entered_username = $_REQUEST['user'];

		//echo "User: " . 
		

		if(($user_entered_username != $user_username)||
		   ($user_entered_password != $user_password)) {
		   echo "Sorry, that is the wrong username or password.";
		   exit(0);
		}	
	}
	
	
	function layer_name($db_name)
	{
		//Take the database name and strip out any unusual characters such as '-' or '_'
		//Also lower case it.
		//This is particularly required for shared hosting environments, where they often
		//include this by default
		$layer_name = strtolower($db_name);
		$layer_name = preg_replace('/[^a-zA-Z0-9]/', '', $layer_name);
		
		if($layer_name == "") {
			$layer_name = "novalidchars";
		}
		
		return $layer_name;	
	}
	
	
	function database_name($db_name)
	{
		//Take the input database name and strip out any unusual characters such as '-', but not '_'
		//Also lower case it.
		//This is particularly required for shared hosting environments, where they often
		//include this by default
		$database_name = strtolower($db_name);
		$database_name = preg_replace('/[^a-zA-Z0-9_]/', '', $database_name);
		
		//echo "New database name=" . $database_name;
		
		if($database_name == "") {
			return false;
		}
		
		return $database_name;	
	}	
		
	
	


?>
