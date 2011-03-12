<?php
//set the error level of the website
error_reporting(E_ERROR | E_PARSE);

require_once("classes/cls.image_scale.php");
require_once("../../config/db_connect.php");


if($server_name == "") {
	$server_name = "poi.nakdreality.com";		//This is a quick fix because I've updated a live site
}


$lightrod_path = "http://" . $server_name . str_ireplace("admin/upload-image.php", "", $_SERVER['PHP_SELF']) . "pics/";

$target_path = "../pics/";

//If this is a multi-user access version - append the database name to the start of the image name
if(isset($_REQUEST['dir_source'])) {
	$append_name = $_REQUEST['dir_source'] . "_";
} else {
	$append_name = "";
}


$target_path = $target_path . $append_name . basename( $_FILES[ 'file' ][ 'name' ] );


if ( move_uploaded_file( $_FILES[ 'file' ][ 'tmp_name' ], $target_path ) )
{
    //Scale only if gd exists on server
   if(function_exists("gd_info")) {
    	$scale = new clsImageScale();

    	if((isset($load_balancer)) && ($load_balancer == true)) {
    		//Get the output resized image into a string, pass to an image server, and get the
    		//resultant url from the image server
    		$scale->load_balancer = true;
    		$scale->image_server = $my_image_server;
    		$scale->scale_image($target_path);
    		//$image_data = file_get_contents($target_path);
    		
    		/*
    		$file = $_FILES[$target_path];
 		$post_field="@$file[tmp_name]"; //notice the @ sign prefixed. This does the trick

    		 $ch = curl_init($scale->image_server);
		 curl_setopt($ch, CURLOPT_POST      ,1);
		 curl_setopt($ch, CURLOPT_POSTFIELDS    ,"height=" . $scale->height . "&width=" . $scale->width . "&data=".$post_field);
		 curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
		 curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
		 curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
		 $response = curl_exec($ch);
			*/
    		/*
    		$handle = fopen($target_path, "rb");
		$image_data = @fread($handle, filesize($target_path));
		fclose($handle);
		
		$fileHandle = fopen($target_path, "rb");
		$image_data = stream_get_contents($fileHandle);
		fclose($fileHandle);
		*/
		//echo "Length = " . strlen($image_data);
    		
    		
    		$postdata = array(
		    'url' => 'http://www.unique.com/pics'. rand(1,10000000000) . $append_name . basename( $_FILES[ 'file' ][ 'name' ] ),
		    'height' => $scale->height,
		    'width' => $scale->width
		);

		//sample image
		$files['data'] = $target_path;

		$response = $scale->do_post_request("http://" .  $server_name . "/image_server/image_indexer.pl", $postdata, $files);
		    		
    		//echo $response;
    		
    		
    		$url = $scale->read_image_server_xml($response);
    		$url = "http://" . $server_name . $url;
    	} else {
    		//Scale the image and write out the file in this directory
    		$scale->scale_image($target_path);
    		$url = $lightrod_path . $append_name . basename( $_FILES[ 'file' ][ 'name' ]);
    	}


    } else {
    	echo "No scaling support. Install GD";
    }



     echo "The image URL to copy and paste into the image field is:<br/>" . $url;
}
else
{
     echo "There was an error uploading the file, please try again!  Ensure that your " . $lightrod_path . " directory is world writable, e.g. using the command line chmod 777 locations/pics, or your FTP package.";
}
?>
