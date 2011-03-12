<?php 

	
	require("../oar-server/cls.basic_geosearch.php");

	require("../config/db_connect.php");
	require("../config/global_functions.php");

	

	$ly = new clsARLayarServer();
	$ly_params = array('layar_name' => layer_name($db_name),
				'layar_attribution' => "",
				'actions_label_1' => "var_actions_label_1",
				'actions_uri_1' => "var_actions_uri_1",
				'line_2' => "var_line_2",
				'line_3' => "var_line_3",
				'line_4' => "var_line_4",
				'title' => "var_title",
				'imageURL' => "imageURL",
				'dimension' => "dimension",
				'rel' => "rel",
				'angle' => "angle",
				'scale' => "scale",
				'baseURL' => "baseURL",
				'full' => "full",
				'reduced' => "reduced",
				'icon' => "icon",
				'size' => "size",
				'morePages' => true,
				'layerURL' => "layerURL",
				'type' => "type",
				'debug' => false);		//Switch to false when taking server live  TEMP to TRUE
	$ly->layar_request($ly_params);		
	$params = array('latitude' => $ly->layar_latitude,		//Latitude in decimal degrees of center of search
		'longitude' => $ly->layar_longitude,			//Longitude in decimal degrees of center of search 
		'table_name' => "tbl_layar_poi",		//Main table name that is being searched on 
		'id_field' => "int_point_id",	//Unique reference to each point in the table
		'latitude_field' => "dec_latitude",	//Field in table that has the latitude in decimal
		'longitude_field' => "dec_longitude",	//Field in table that has the longitude in decimal
		'peano_field_header' => "int_peano",	//First letters of fields in table that hold peano
                                                        //integers E.g. 'int_peano', which gets appended
			 				//to create 'int_peano1, int_peano2, int_peano1iv,
			 				// int_peano2iv'
		'max_records' => $ly->max_records,	//10 by default
		'first_record' => $ly->layar_next_page_key,
		'radius' => $ly->layar_radius,
		'misc_fields' => "var_actions_label_1 AS var_actions_label_1, 
				 m.var_actions_uri_1 AS var_actions_uri_1,
				 var_line_2 AS var_line_2, 
				 var_line_3 AS var_line_3, 
				 var_line_4 AS var_line_4, 
				 var_title AS var_title, 
				 var_image AS imageURL,
				  2 AS dimension,		
				  'true' AS rel,
				  0 AS angle,
				  1.0 AS scale,
				  '' AS baseURL,
				  var_image AS full,
				  var_image AS reduced,
				  var_image AS icon,
				  20 AS size,
				  0 AS layerURL,
				  1 AS type			
				  ",				//Type 0 is the default black circle, 1 = first entered type
		  	  					//2 AS dimension
		'show_queries' => false,
		'provide_count' => true
		);
			//'custom_where' => "pp.int_main = 1",


	list($results_array, $count) = $ly->proximity_finder($params);
	//print_r($results_array);
	
	
	//Append another layer result - with a couple of results
	if($merge_with != "") {
		mysql_close();
		connect_merge($dir_source, $sources);			//Connect to the new database of the second layer
		$params['max_records'] = 2;				//We only want a couple of results from the secondary
									//layer
		$ly->layar_attribution = "LightRod.org";		//Insert some branding
		list($results_merge_array, $count_merge) = $ly->proximity_finder($params);
		
		//print_r($results_merge_array);
		
		//Append results to the list 
		$results_array = array_merge($results_merge_array, $results_array);	//The merged results come first

		
	}
	
	//Optionally append a few Rvolve adverts
	if(isset($_REQUEST['rvolve_in_results'])) {
	
		//$ly->layar_attribution = "";		//Insert some branding
		list($results_merge_array, $count_merge) = $ly->insert_adverts("rvolve", 3, $_REQUEST['rvolve_in_results'], $params);	//get 3 results
		$results_array = array_merge($results_merge_array, $results_array);	//The merged results come first
		
	
	}
	
	//print_r($results_array);
	
	$ly->layar_response($results_array, $count['show_next']);	
		
	mysql_close();
?>
