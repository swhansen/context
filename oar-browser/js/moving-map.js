function isDefined(object)
{
	//alert(object);
	return (typeof(object) === undefined) ? false : true;
}




var has_been_mapped = false;
var properties;

var poiMarkers = [];
var ajaxString;
var advertMarkers = [];

var queries_cnt = 0;		//debugging
var showQueries = 'false';

var insideInfoBox = false;

var initializingMap = false;
var initializedMapZoom = false;


//See http://code.google.com/apis/maps/documentation/events.html#Event_Closures
function createMarker(point, number, options, content, linkTarget) {
  //var marker = new google.maps.Marker(point, options);
 
  var marker = new google.maps.Marker(options);
   marker.value = number;
   
   
   
   //alert(content.actions[0].uri);
   if(eval("content.actions[0]") != undefined) {

	
   	 if(eval("content.actions[0].uri") != undefined) {
	   	var url = content.actions[0].uri;
	   	
	   	
	   	 //Special case conduit -- yerg
	 	if(linkTarget == '_conduit') {
	 		 url = url + '#_new';
	  	}
	  } else {
	  	var url = "";
	  }
    } else {
	 var url = "";
    }

   
  

 
  
  if(url != '') {
  	titleLink = '<a href="' + url+ '" target="' + linkTarget + '">'+ content.title +'</a>';
  	
  	 if(content.imageURL != "") {
  		imageSrc = '<a href="' + url+ '" target="' + linkTarget + '"><img align="right" src="' + content.imageURL + '" border="0"></a>';
	  } else {
	  	imageSrc = '';
	  }
  } else {
  	titleLink = content.title;
  	 if(content.imageURL != "") {
  		imageSrc = '<img align="right" src="' + content.imageURL + '" border="0">';
	  } else {
	  	imageSrc = '';
	  }
  	
  }
  
  if((content.line2 != '')&&(content.line2 != null)) {
  	line2 = content.line2;
  } else {
  	line2 = '';
  }
 
  if((content.line3 != '')&&(content.line3 != null)) {
  	line3 = content.line3;
  } else {
  	line3 = '';
  } 
  
  if((content.line4 != '')&&(content.line4 != null)) {
  	line4 = content.line4;
  } else {
  	line4 = '';
  }  
  
  var contentString = '<div id="content">'+
    '<div id="siteNotice">'+
    '</div>'+
        '<h3 class="heading" style="font-size: 12px">' + titleLink + '</h3>'+
        '<div id="bodyContent">'+
        imageSrc +
        '</div>'+
        '<div class="regular" style="font-size: 11px">'+
        line2 + '<br>'+
        line3 + '<br>'+
        line4 + '<br>'+
        '</div>'+
        '</div>';
    
    '</div>'+
    '</div>';


	var infowindow = new google.maps.InfoWindow({
	    content: contentString
	});

	google.maps.event.addListener(marker, 'click', function() {
	  infowindow.open(mapview,marker);
	});
  
  /*google.maps.event.addListener(marker, "click", function(e) {
       insideInfoBox = true;
       var infoBox = new InfoBox({latlng: marker.getPosition(), map: mapview});
    });*/
    //google.maps.event.trigger(marker, "click");
    
    /*
  if(content.title == '') {
     var iWindowHTML = '<div id="wrap_div">'+
        '<div class="tTitle">'+
        '<h3 class="popup-title">' + content.line2 + '</h3>'+
        //'<div class="cur_place">icon_or_something</div>'+
        '</div>'+
        '<div class="popup-content">'+
        content.line3 + '<br>'+
        content.line4 + '<br>'+
        //'What happens when we put some really long text in here?' +
        '</div>'+
        '</div>';
  
  
  
  } else {
      var iWindowHTML = '<div id="wrap_div">'+
        '<div class="tTitle">'+
        '<h3 class="popup-title">' + content.title + '</h3>'+
        //'<div class="cur_place">icon_or_something</div>'+
        '</div>'+
        '<div class="popup-content">'+
        content.line2 + '<br>'+
        content.line3 + '<br>'+
        content.line4 + '<br>'+
        '</div>'+
        '</div>';
    }*/

	/*
	google.maps.event.addListener(marker, 'click', function(e) {
		

		var infoBox = new InfoBox({latlng: marker.getPosition(), map:mapview, html:iWindowHTML, id: content.id});

	});*/
  
  


  return marker;

}





function searchComplete(pois)
{
	
	/*for (myKey in pois){
			alert ("pois["+myKey +"] = "+pois[myKey]);
		}
	alert('got to here a');*/
	//var pois = [];
	//eval("pois="+data);		//Turn JSON string into array

	/*alert(pois.layer);
	alert(pois.hotspots[0].lat);*/
	
	//If on an initialization, we want to zoom out to the level of the first two results
	if((initializingMap == true)&&(initializedMapZoom == false)) {
		//alert('in here');
		
		var bounds = new google.maps.LatLngBounds();

		//Include self location in bounds
		centerPoint = mapview.getCenter();
		bounds.extend(centerPoint);

		for (var i = 0; i < 3; i++) {
		  // Insert code to add marker to map here
		  if(eval("pois.hotspots[i]") != undefined) {
			  var point = new google.maps.LatLng(pois.hotspots[i].lat/1000000,pois.hotspots[i].lon/1000000);

			   if(eval("point") != undefined) {
				// Extend the LatLngBound object
				//alert(point);
			  	bounds.extend(point);
			   }
		   }
		}
		
		centerPoint = mapview.getCenter();
		zoom = mapview.getZoom(mapview.fitBounds(bounds));
		//alert(bounds + ' zoom = ' + zoom);
		if(zoom > 15) { zoom = 15 };		
		mapview.setZoom(zoom-1);
		mapview.setCenter(centerPoint);		
		

		
		initializingMap = false;	//Zoom out to the right level
	}
	
	
	//alert(pois.hotspots.length);
	
	//Loop through array
    	for(cnt=0; cnt<pois.hotspots.length; cnt++) {
    		//Get the marker from the point
    		//var point = new google.maps.Point(pois.hotspots[cnt].lat/1000000, pois.hotspots[cnt].lon/1000000);
		//var point = new google.maps.Point(51, 0);

		var point = new google.maps.LatLng(pois.hotspots[cnt].lat/1000000,pois.hotspots[cnt].lon/1000000);


    		if(eval("poiMarkers[cnt]") != undefined) {

			poiMarkers[cnt].setMap(null);
    			//mapview.removeOverlay(poiMarkers[cnt]);		//Delete the old one
    			//LEAVE OUT delete propertyMarkers[cnt];   //clear element of array
    		}

    		var markerOptions = {
    			position: point, 
    			title: pois.hotspots[cnt].title,
    			icon: markerImage,
    			shadow: markerShadow
    		};
    		
    		
		
		if(markerOptions.title == '') {
			markerOptions.title = pois.hotspots[cnt].line2;
		}

    		//Create the new one
    		poiMarkers[cnt] = createMarker(point, cnt, markerOptions, pois.hotspots[cnt], linkTarget);
    		poiMarkers[cnt].setMap(mapview);
    		//mapview.addOverlay(poiMarkers[cnt]);	//Add to the map

    	}
    	//http://stackoverflow.com/questions/1220063/dynamically-adding-listeners-to-google-maps-markers &



    	//Remove the trailing icons - start off at the end of the list of new pois
    	for(cnt; cnt<poiMarkers.length; cnt++) {
    		if(eval("poiMarkers[cnt]") != undefined) {
			
			poiMarkers[cnt].setMap(null);
    			//mapview.removeOverlay(poiMarkers[cnt]);		//Delete the old one
    			//LEAVE OUT delete propertyMarkers[cnt];   //clear element of array
    		}
    	}

	

}



function addAdvert(options, advert, returnLink)
{

	 var marker = new google.maps.Marker(options);
	 var titleLink = '<a href="' + advert.url+ '" target="_new">'+ advert.fullText +'</a>';
	  
	  var contentString = '<div id="content">'+
	    '<div id="siteNotice">'+
	    '</div>'+
		'<h3 class="heading" style="font-size: 12px">' + titleLink + '</h3>'+
	        '<div class="regular" style="font-size: 11px" target="_new"><a href="' + returnLink + '">Rvolve Ads</a></div>' + 
	    '</div>'+
	    '</div>';	 
		 
	var infowindow = new google.maps.InfoWindow({
	    content: contentString
	});

	google.maps.event.addListener(marker, 'click', function() {
	  infowindow.open(mapview,marker);
	});
	 
	 return marker;
}




function advertsComplete(data)
{
	
	var cnt = 0;
	
	//Loop through array
    	$.each(data.advert, function(i,advert){ 
    	
    		//Get the marker from the point

		
		var point = new google.maps.LatLng(advert.lat,advert.lon);
		
		//alert(point);
		
		/*
    		if(eval("poiMarkers[cnt]") != undefined) {

			poiMarkers[cnt].setMap(null);
    			//mapview.removeOverlay(poiMarkers[cnt]);		//Delete the old one
    			//LEAVE OUT delete propertyMarkers[cnt];   //clear element of array
    		}*/

    		var markerOptions = {
    			position: point, 
    			title: advert.fullText,
    			icon: advertImage,
    			zIndex: -1
    			
    		};		//shadow: advertShadow
    				//setZIndex(zIndex:number)
    		//alert(advert.fullText);

    		//Create the new one
    		
    		if(eval("advertMarkers[cnt]") != undefined) {
			
			advertMarkers[cnt].setMap(null);
    		}
    		advertMarkers[cnt] = addAdvert(markerOptions, advert, data.returnLink);
    		advertMarkers[cnt].setMap(mapview);
    		
    		cnt++;
    	});
    	//http://stackoverflow.com/questions/1220063/dynamically-adding-listeners-to-google-maps-markers &



    	//Remove the trailing icons - start off at the end of the list of new pois
    	/*for(cnt; cnt<poiMarkers.length; cnt++) {
    		if(eval("poiMarkers[cnt]") != undefined) {
			
			poiMarkers[cnt].setMap(null);
    			//mapview.removeOverlay(poiMarkers[cnt]);		//Delete the old one
    			//LEAVE OUT delete propertyMarkers[cnt];   //clear element of array
    		}
    	}*/

	

}




  var mapview;
  var geocoder;
  var mainMarker;
  var formattedAddress;
  

  
  
  function initializeMap(startingLatitude, startingLongitude, startingZoom) {
  
    //geocoder = new google.maps.Geocoder();

    if(startingZoom != false) {
    	initializedMapZoom = true;
    } else {
    	//The default zoom level to start at
    	startingZoom = 15;
    } 

    var latlng = new google.maps.LatLng(startingLatitude, startingLongitude);
    
        
    var myOptions = {
      zoom: startingZoom,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      scaleControl: true,
      navigationControlOptions: {  
	     style: google.maps.NavigationControlStyle.ZOOM_PAN  
       }  
    };
    

    
    mapview = new google.maps.Map(document.getElementById("mapview"), myOptions);
    
    
    mainMarker = new google.maps.Marker({
	      map: mapview, 
	      position: latlng,
	      draggable: false,
	      zIndex: 0
	  });
    
    
   // movingMapInit();
   initializingMap = true;	//Zoom out to the right level
    getResultsData(startingLatitude,startingLongitude,50);  //50
    //getNakdData('lessent',startingLatitude,startingLongitude, 50);
    
    google.maps.event.addListener(mapview, 'dragend', function moveEnd() {
		//When the map moves, AJAX the results to the center
		
		var center = mapview.getCenter();
		//alert(center.lat());
		
		getResultsData(center.lat(),center.lng(),50); //50

    });
    
  }
  





