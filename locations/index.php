<?php
include("../config/db_connect.php");		//For the purposes of getting username and password


$lightrod_path = "http://" . $server_name . str_ireplace("index.php", "", $_SERVER['PHP_SELF']);

//Handle multi-user version
if(isset($_REQUEST['dir_source'])) {
	$redir = $_REQUEST['dir_source'] . "/lr/";
	$lightrod_path = str_ireplace("locations/", $redir . "locations/", $lightrod_path);
} else {
	$redir = "";
}


//PUT THESE META TAGS ONTO YOUR HOMEPAGE 
?>



<html>
<head>
<meta name="oar-server" content="<?php echo $lightrod_path . "layar-server.php";?>"/>
<meta name="oar-description" content="Powered by <?php if(isset($my_brand)) { echo $my_brand; } else { echo "LightRod Server"; }; ?>"/>
<meta name="oar-logo" content=""/>
</head>
<body>
This page should currently be viewed from within the <a href="http://owlz.org">location browser</a> at owlz.org, by entering the address '<?php echo $lightrod_path ?>' in the URL bar and clicking 'go', or clicking <a href="http://owlz.org?url=<?php echo $lightrod_path ?>">here</a>.  You can put whatever HTML you like on this page for ordinary web browsers.

</body>
</html>
