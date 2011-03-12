<html>
<head>
<title>LightRod Installation</title>

</head>
<body>

<?php
//set the error level of the website
error_reporting(E_ERROR | E_PARSE);  //No warnings, there will be some otherwise when things aren't found.


if(file_exists("config/db_connect.php")) {
	require("config/db_connect.php");
	require("config/global_functions.php");

	//For multiple users on one server
	if(isset($_REQUEST['dir_source'])) {
		$redir = $_REQUEST['dir_source'] . "/lr/";
	} else {
		$redir = "";
	}

	$lightrod_path = "http://" . $server_name . str_ireplace("index.php", "", $_SERVER['PHP_SELF']) . $redir.  "locations/";

 ?>
You have a working install of LightRod at <?php echo $lightrod_path ?><br>  You can now use the <a href="oar-browser/?url=<?php echo $lightrod_path ?>">location browser</a> locally to show your locations, or set up your locations with the <a href="locations/admin/">admin panel</a> <br/><br/>

For augmented reality on mobile phones, your Layar POI URL is <code><?php echo $lightrod_path ?>layar-server.php</code> and your Layar name is <code><?php echo layer_name($db_name) ?></code>.  See <a href="http://www.lightrod.org/mediawiki/index.php/Becoming_Visible_on_Layar">these instructions</a> to become visible on the Layar augmented reality browser.<br/>
<br/>

You should add the following meta tag to your homepage so that your POI service is discoverable from your homepage by location browsers: <code>&lt;meta name="oar-server" content="<?php echo $lightrod_path ?>layar-server.php" /></code>.  See <a href="http://www.lightrod.org/mediawiki/index.php/LightRod_Location_Browser">further details</a> about location browser meta tags.<br/></br/>

<?php if($redir == "") { ?>For those on a second installation only: If you have upgraded the software recently you can <a href="upgrade.php">upgrade the databases</a>.<?php } ?>



<?php } else {

	if($_REQUEST['action'] == 'install') {
		//Run an install
		$db_username = $_REQUEST['db_username'];
		$db_password = $_REQUEST['db_password'];
		$db_name = $_REQUEST['db_name'];
		$google_maps_key = $_REQUEST['google_maps_key'];

		require_once("install.php");

		?>
		<a href="index.php">Start Using LightRod</a>
		<?php
	} else {
		?>
		<h2>Installation of LightRod</h2>

		<form action="index.php" method="post">
			<input type="hidden" name="action" value="install">
			<table cellpadding="8">

				<tr bgcolor="#dddddd">
					<td colspan="2" width="700">
					<b>1.</b><small> Ensure you have JSON for PHP installed. JSON comes with PHP 5.2, but earlier versions will need this installed. See install instructions <a href="http://www.lightrod.org/mediawiki/index.php/Layar_API#Ensuring_PHP_has_JSON">here</a></small>
					</td>
				<tr>


				<tr bgcolor="#cccccc">
					<td colspan="2" width="700">
					<b>2.</b><small> Make sure the permissions of the directory config/ are 'world-readable and writable'.  E.g. on the command line: <pre>
					chmod o+rw config/</pre>
					 &nbsp;&nbsp;&nbsp;Or use your FTP package to change the permissions.</small>
					</td>
				<tr>

				<tr bgcolor="#dddddd">
					<td colspan="2" width="700">
					<b>3.</b><small> Enter your MySQL username and password on your server below.  Check with your hosting providers if you don't know this already.  This will be your LightRod admin panel login.  The database name you enter will be the title of your Layar layer.</small><br/><br/>
					</td>
				<tr bgcolor="#cccccc">
					<td>Database Username: </td><td><input type="text" name="db_username"></td>
				</tr>
				<tr bgcolor="#dddddd">
					<td>Database Password: </td><td><input type="password" name="db_password"></td>
				</tr>
				<tr bgcolor="#cccccc">
					<td>Database Name:<br/><small>(Lower case, letters and numbers only)</small> </td><td><input type="text" name="db_name"></td>
				</tr>
				<tr bgcolor="#dddddd">
					<td>Google maps API key<br/><small>(Optional, for browser on remote host.<br/>Get a free key for your domain <a target="_new" href="http://code.google.com/apis/maps/signup.html">here</a>)</small>: </td><td><input type="text" name="google_maps_key"> </td>
				</tr>
				<tr>
					<td></td>
					<td>
						<input type="submit" value="Install">
					</td>
				</tr>
		</form>
		<?php
	}
} ?>


</body>
</html>

