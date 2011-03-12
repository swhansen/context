<?php  /* CSV Import */


if(!class_exists("clsImageScale")) {


require_once("../../oar-server/cls.basic_geosearch.php");


class clsImageScale
{

	//public $fields;
	//public $field_separator;
	public $load_balancer;
	public $image_server;
	public $width;
	public $height;
	
	public function __construct()	
	{
		//$this->fields = array();
		$this->load_balancer = false;
		$this->image_server = null;
		
	}
	
	  
	
	
	 public function do_post_request($url, $postdata, $files = null)
	{
	    $data = "";
	    $boundary = "xYzZY"; //substr(md5(rand(0,32000)), 0, 10);
	      
	    //Collect Postdata
	    foreach($postdata as $key => $val)
	    {
		$data .= "--$boundary\r\n";
		$data .= "Content-Disposition: form-data; name=\"".$key."\"\r\n\r\n".$val."\r\n";
	    }
	    
	    $data .= "--$boundary\r\n";
	   
	    //Collect Filedata
	    foreach($files as $key => $file)
	    {
		$fileContents = file_get_contents($file);
	       
		$data .= "Content-Disposition: form-data; name=\"{$key}\"\r\n\r\n"; 
		$data .= $fileContents."\r\n";
		$data .= "--$boundary--\r\n";
	    }
	    //echo $data;
	 
	  
	    $params = array('http' => array(
		   'method' => 'POST',
		   'header' => "Content-Type: multipart/form-data; boundary=".$boundary ."\r\n",
		   'content' => $data
		));
		
	   $ctx = stream_context_create($params);
	   $fp = fopen($url, 'rb', false, $ctx);

	   if (!$fp) {
	      throw new Exception("Problem with $url, $php_errormsg");
	   }
	 
	   $response = @stream_get_contents($fp);
	   if ($response === false) {
	      throw new Exception("Problem reading data from $url, $php_errormsg");
	   }
	   return $response;
	}
	
	    
	  
	  
	  
	  
	  /*
	  	image_indexer.pl?url=&height=&width=&data=
		<imageIndex>
			<url></url>
			<id></id>
		</imageIndex>
	  
	  */
	  public function read_image_server_xml($response)
	  {
	  	$xml = simplexml_load_string($response);
	  	$xml->getName();
	  	$url = false;
	  	foreach($xml->children() as $child)
		  {
		    	if($child->getName() == 'url') {
		    		$url = $child;
		    	}
		  }
		  
		return $url;
	  }
	
	
	

	public function scale_image($filename)
	{
		//Scale the incoming image to max 100x100 size if it is larger than 150x120 in either direction
		$size = getimagesize($filename);
		$width = $size[0];
		$height = $size[1];
		if(($width > 150)||($height > 120)) {
		
		     $ratio = $width / $height;
		     if ($ratio >= 1){
			  $scale = 100 / $width;
		     } else {
			  $scale = 100 / $height;
		     }
		     
		     $this->width = $width * $scale;
		     $this->height = $height * $scale;
		
			$path_parts = pathinfo($filename);
			switch($path_parts['extension'])
			{
	
				case "jpg":
				{
					//echo "A JPG scale=" . $scale . " filename=" . $filename;
					$quality = 100;
					$image_input = imagecreatefromjpeg($filename);
					
					if($image_input) {
						$image_output = imagecreatetruecolor($width * $scale, $height * $scale);
						imagecopyresampled($image_output, $image_input, 0, 0, 0, 0, $width * $scale, $height * $scale, $width, $height);
						$quality = 100;
						imagejpeg($image_output, $filename, $quality);
						
					} else {
						echo "Error creating image from jpeg.";
					}
				}
				break;
				case "gif":
				{
					$image_input = imagecreatefromgif($filename);
					$image_output = imagecreatetruecolor($width * $scale, $height * $scale);
					imagecopyresampled($image_output, $image_input, 0, 0, 0, 0, $width * $scale, $height * $scale, $width, $height);
					imagegif($image_output, $filename, $quality);
				}
				break;
				case "png":
				{
					//echo "in here";
					$image_input = imagecreatefrompng($filename);
					$image_output = imagecreatetruecolor($width * $scale, $height * $scale);
					imagecopyresampled($image_output, $image_input, 0, 0, 0, 0, $width * $scale, $height * $scale, $width, $height);
					$quality = 0;
					imagepng($image_output, $filename, $quality);
				}
				break;
				
			}
			imagedestroy($image_output);
			imagedestroy($image_input);
		} else {
			$this->width = $width;
			$this->height = $height;
		}
	}

}


}

?>
