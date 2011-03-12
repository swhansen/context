<?php
include("../../config/db_connect.php");		//For the purposes of getting username and password

include("../../config/global_functions.php");		//For the purposes of getting username and password

if(file_exists("../../config/browser.php")) {
	require_once("../../config/browser.php");
} else {
	//127.0.0.1
	define("GOOGLE_MAPS_KEY", 'ABQIAAAAbi3pFJ-7B6wOjr3zC6E8cRRxPEkfY55iQt7n8wZYUNTmOUKuthSMZ5ZmxcZx5BaQNioK-oEQDZ9YJQ');
}


authenticate($user_username, $user_password);

$shortcut = "";

//For multiple users on one server
	if(isset($_REQUEST['dir_source'])) {
		$redir = $_REQUEST['dir_source'] . "/lr/";
		
		if(isset($my_brand)) {
			//A shortcut works on their servers
			$shortcut = "Use the shortcut '" . $_REQUEST['dir_source'] . "'";
		}
	} else {
		$redir = "";
		
	}

	$lightrod_path = "http://" . $server_name . str_ireplace("locations/admin/index.php", "", $_SERVER['PHP_SELF']) . $redir.  "locations/";


?>

<html>
<head>
<title><?php if(isset($my_brand)) { echo $my_brand; } else { echo "LightRod"; }; ?> POI Admin Panel</title>
<script src="codebase/dhtmlxcommon.js" type="text/javascript"></script>
<script src="codebase/dhtmlxgrid.js" type="text/javascript"></script>
<script src="codebase/dhtmlxgridcell.js" type="text/javascript"></script>

<script src="codebase/dhtmlxgrid_filter.js" type="text/javascript"></script>

<script src="codebase/dhtmlxdataprocessor.js" type="text/javascript"></script>
<!-- Debug codebase -->
<!--<script src="codebase/dhtmlxdataprocessor_debug.js" type="text/javascript"></script>-->



<link rel="STYLESHEET" type="text/css" href="codebase/dhtmlxgrid.css">


<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo GOOGLE_MAPS_KEY; ?>
&sensor=true"
type="text/javascript"></script>
<script src="http://code.google.com/apis/gears/gears_init.js" type="text/javascript" charset="utf-8"></script>
<script src="../../oar-browser/js/geo.js" type="text/javascript" charset="utf-8"></script>
<script src="../../oar-browser/js/geolocation.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="../../oar-browser/css/main.css"/>

<?php if(isset($my_brand)) { ?>
<link rel="shortcut icon" type="image/x-icon" href="./favicon.ico" />
<link rel="icon" type="image/x-icon" href="./favicon.ico" />
<?php } ?>

<script>
atl_scenario = 'adminpanel';		//For geocode selection

</script>

</head>

<body onload="initialize_oar_map(0,0,0);">
<div id="gridbox" style="height:300px;width:1024px;"></div>
<button onclick="addRow()">Add POI</button>   <button onclick="removeRow()">Remove POI</button> <button onclick="removeAll()">Remove All</button> <small>Note: double click to edit</small>
<!--<form action="#">
<input id="search_title" type="text" length="50">
</form>-->
<script>
mygrid = new dhtmlXGridObject("gridbox");
mygrid.setImagePath("codebase/imgs/");
mygrid.setEditable(true);
mygrid.setSkin("light");
mygrid.setHeader("ID,Type,Title,Line 2,Line 3,Line 4,Image,Latitude,Longitude,Action Label 1,Action URL 1");	//7th is Image
//mygrid.setInitWidths("*,*,*,*,*,*,*,*,*,*,*");
mygrid.setInitWidths("30,50,150,100,100,100,100,100,100,120,*");	//7th should be 100 or so for the Image
mygrid.setColTypes("ro,ed,ed,ed,ed,ed,ed,ed,ed,ed,ed");
mygrid.setColAlign("left,left,left,left,left,left,left,left,left,left,left");
mygrid.setColSorting("na,na,na,na,na,na,na,na,na,na,na");
//mygrid.enableResizing("true,true,true,true,true,true,true,true,true,true,true");
//mygrid.attachEvent("onRowSelect",doOnRowSelected);
mygrid.init();
mygrid.loadXML("layar-select.php");
var dp = new dataProcessor('layar-edit.php');
dp.init(mygrid);
//TODO in future: mygrid.makeSearch("search_title",3);

  function addRow(){
        var newId = (new Date()).valueOf()
        mygrid.addRow(newId,"-",mygrid.getRowsNum())
        mygrid.selectRow(mygrid.getRowIndex(newId),false,false,true);
    }
    function removeRow(){
        if(confirm("Are you sure you want to delete this row?")) {
        	var selId = mygrid.getSelectedId()
        	mygrid.deleteRow(selId);
    	}
    }


    function removeAll(){
        if(confirm("Are you sure you want to delete all rows?")) {
        	window.location = "delete-all.php";
        	return true;
        } else {
        	return false;
        }
    }





</script>
<br/>
<br/>

<table bgcolor='#EEEEEE' cellpadding="0" width="1024">
  	<tr >
    		<td bgcolor='#EEEEEE' valign="top" width="50%">
    			<table bgcolor='#EEEEEE' cellpadding="8" width="100%">
    				<tr>
    					<td bgcolor='#e4e0d8'>
    						<small><b>Location Browsers</b><br/>
    						See your data in any of the following location browsers</small>
    						<ul>
			    				<?php if(($server_name != '127.0.0.1')&&
			    					($server_name != "localhost")) { ?>
			    				<li><small>The <a href="http://owlz.org/?url=<?php echo $lightrod_path ?>" target="_blank">Owlz.org</a> Open Web Location browZer</small></li>
			    				<?php } else { ?>
			    					<li><small>The <a href="../../oar-browser/?url=<?php echo $lightrod_path ?>" target="_blank">LightRod location browser</a></small></li>
			    				<?php } ?>
		    				</ul>
		    				<small>You now have your own mobile web app. <?php echo $shortcut ?><br/><code><?php echo $lightrod_path ?></code> is your shareable URL.</small>
    					</td>
    				</tr>

    				<tr>
    					<td bgcolor='#e4e0d8'>
    					    <small><b>Image Upload</b></small>
    					    <form action="upload-image.php" enctype="multipart/form-data" method="post">

						    <small>You can upload images and copy the URL we provide into the 'image field' for each POI (rec. max 100x100 pixels):</small><br/>
						    <input id="file" type="file" name="file">

						    <input id="submit" type="submit" name="submit" value="Upload">

					    </form>


    					</td>
    				</tr>

    				<tr>
    					<td bgcolor='#e4e0d8'>
    					   <small><b>Spreadsheet Import</b></small>
    					   <form action="upload-csv.php" enctype="multipart/form-data" method="post">

						<small>You have the option of importing your data using a CSV spreadsheet:</small><br/>
						<input id="file" type="file" name="file">
						    <input id="submit" type="submit" name="submit" value="Upload">

						    <ul>
						    	<li><small>Fields are in this <a href="sample_spreadsheet.csv">sample spreadsheet</a></small></li>
						    	<li><small>Ensure comma delimited, with "" for text fields.</small></li>
						    	<li><small>Be careful with decimal places on lat/lon fields</small></li>
						    </ul>


					    </form>
    					</td>
    				</tr>
    			</table>
    		</td>

    		<td bgcolor='#EEEEEE' valign="top">
    			<table bgcolor='#EEEEEE' cellpadding="4" width="100%">
    				<tr>
    					<td bgcolor='#e4e0d8' style="padding: 6px";>

			    			<span style="font-weight: bold; font-size: 80%;">Latitude/Longitude Look-up</span><br/><br/>
			    			<div id="map_canvas" class="mapspecs" style="width: 360px; height: 240px; overflow: hidden;";></div>
						<div id="location_message" class="locationmessagespecs" style="font-weight:bold; font-size: 70%"><small>Select you POI location.</small></div>

						<form action="" name="lpf">
						<small>
						Lat <input type="text" name="lat" value="0">
						Lon <input type="text" name="lon" value="0">
						<input type="hidden" name="acc" value="0">
						</small>

						</form>
					</td>
				</tr>
			</table>

    		</td>
    	</tr>
</table>



    	<!--<tr bgcolor='#e4e0d8'>
    		<td valign="top">-->




</body>


</html>
