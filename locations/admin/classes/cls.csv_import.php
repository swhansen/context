<?php  /* CSV Import */


if(!class_exists("clsCSVImport")) {


require_once("../../oar-server/cls.basic_geosearch.php");


if (!function_exists('str_getcsv')) {
     function str_getcsv($input, $delimiter = ",", $enclosure = '"', $escape = "\\") {
	$fiveMBs = 5 * 1024 * 1024;
	$fp = fopen("php://temp/maxmemory:$fiveMBs", 'r+');
	fputs($fp, $input);
	rewind($fp);

	$data = fgetcsv($fp, 1000, $delimiter, $enclosure); //  $escape only got added in 5.3.0

	fclose($fp);
	return $data;
    }
} 



class clsCSVImport
{

	public $fields;
	public $field_separator;
	public $ids_imported;
	
	public function __construct()	
	{
		$this->fields = array();
	
		//Get first line of csv file, which gives the fields
		$this->field_separator = ",";
		
		$this->ids_imported = array();
	}

	private function set_peanos($id_array)
	{
	
		$bg = new clsBasicGeosearch();


		$sql = "SELECT int_point_id, dec_latitude, dec_longitude FROM tbl_layar_poi WHERE int_peano1 = 0";

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
	}


	



	public function read_file($filename, $data = null)
	{
		//Get all lines into RAM - TODO: might want to process one line at a time for large files in future
		if($data != null) {
			//Coming from a POST request
			
			//Write the buffer into a file
			$lines = explode("\n", $data);
			//print_r($lines);
		} else {
			//Coming from a multipart form file uploaded
			$lines = file($filename);
		}	
		
		$first_line = true;
		$ids = array();
		
		foreach ($lines as $line_num => $line) {
    			
    			//echo $line;
    			
    			if($first_line == true) {
    				//Process first line as array fields
    				$this->fields = str_replace("\"", "", $line);
    				$first_line = false;
    			} else {
    			
    				//echo $line;
    				//OLD:$parts = explode($this->field_separator, $line);
    				if($line != "") {
	    				$parts = str_getcsv($line);
	    				
	    				//print_r($parts);		//TESTING IN
	    				foreach($parts as $part_num => $part)
	    				{
	    					//echo $part;
	    					if($part == "") {
	    						$parts[$part_num] = "\"\"";
	    					
	    					} else {
	    						$parts[$part_num] = "\"" . $parts[$part_num] . "\"";
	    					}
	    				}
	    				
	    				//print_r($parts);
	    				$line = implode($this->field_separator, $parts);
	    				
	    				//echo "\nLine=" . $line;
	    			
	    				$sql = "INSERT INTO tbl_layar_poi (" . $this->fields . ") VALUES (" . $line . ")";
	    				
	    				//echo $sql;
					$result = mysql_query($sql) or die("Unable to execute query $sql " . mysql_error());
	   				
	   				//Get the id inserted
	   				$this->ids_imported[] = mysql_insert_id();
	   			}
   			
    			}
    			
		}
		$this->set_peanos();
		return true;	
	}
	
	public function delete_all()
	{
		//Use with caution, but useful after importing a spreadsheet wrongly
		$sql = "DELETE FROM tbl_layar_poi";
		$result = mysql_query($sql) or die("Unable to execute query $sql " . mysql_error());

		$sql = "ALTER TABLE tbl_layar_poi AUTO_INCREMENT=1";
		$result = mysql_query($sql) or die("Unable to execute query $sql " . mysql_error());
	}


	public function delete_row($point_id)
	{
		//Delete a single row
		$sql = "DELETE FROM tbl_layar_poi WHERE int_point_id = " . $point_id;
		$result = mysql_query($sql) or die("Unable to execute query $sql " . mysql_error());

	}

}


}

?>
