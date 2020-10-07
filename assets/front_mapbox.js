var mapbox;
var mapbox_marker = [];
var mapbox_bounds = [];

/*
mapbox_marker[0] = task
mapbox_marker[1] = Driver
mapbox_marker[2] = drop off
*/



mapbox_setMapCenter = function(lat, lng){
	mapbox.setView(new L.LatLng(lat, lng), 15);
};

mapbox_fitMap = function(){
	mapbox.fitBounds(mapbox_bounds, {padding: [30, 30]}); 
};


mapbox_PlotMap = function(div, lat , lng){
	
	if (mapbox != undefined) {		
		mapbox.remove();
	}	
	
	mapbox = L.map(div,{ 
		scrollWheelZoom:true,
		zoomControl:true,
	 }).setView([lat,lng], 5 );
	
	L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token='+mapbox_token, {		    
	    maxZoom: 18,
	    id: 'mapbox.streets',		    
	}).addTo(mapbox);
		
    mapbox_bounds.push( [lat,lng] );
        
    /*TASK */
    mapbox_marker[0] = L.marker([ lat , lng ], { 
    	draggable : false,
    	icon : toIcon(icon_finish)
    } ).addTo(mapbox);  
    
    mapbox_marker[0].bindPopup(delivery_address);
    
    /*DRIVER*/
    if ( driver_location_lat>0){
    	mapbox_marker[1] = L.marker([ driver_location_lat , driver_location_lng ], { 
	    	draggable : false,
	    	icon : toIcon(icon_driver)
	    } ).addTo(mapbox);  
	    mapbox_marker[1].bindPopup(driver_info_window);
	    	    
        mapbox_bounds.push( [driver_location_lat,driver_location_lng] );
        
        track_route_type=2;
    }
    
    /*DROP OFF*/
    if (!empty(dropoff_task_lat)){
    	mapbox_marker[2] = L.marker([ dropoff_task_lat , dropoff_task_lng ], { 
	    	draggable : false,
	    	icon : toIcon(icon_dropoff)
	    } ).addTo(mapbox);  
	    mapbox_marker[2].bindPopup(drofoff_info_window);
	    
	    mapbox_bounds.push( [dropoff_task_lat,dropoff_task_lng] );
	    
	    track_route_type=3;
    }
    
     dump("track_route_type=>"+track_route_type); 
     dump("trans_type=>"+trans_type);
     
     switch (track_route_type){
     	case 2:     	
     	break;
     	
     	case 3:
     	break;
     }
     
    
    mapbox_fitMap();    
};

toIcon = function(icon_link){
	return L.icon({
		iconUrl: icon_link,	    	   
	});
};


init_trackMap = function(task_info){
	dump("init_trackMap");
	dump(task_info);
	
	if(!empty(task_info.track_details)){
	   $(".trackdetails-wrap").html(task_info.track_details);	   
	}	
	
	mapbox_bounds = [];
	mapbox_bounds.push( [task_info.task_lat , task_info.task_lng] );
	
	
	if (!empty(task_info.driver_location_lat)){		
		if(!empty(mapbox_marker[1])){
			dump('move driver');	
			mapbox_markerMove_driver( task_info.driver_location_lat , task_info.driver_location_lng);
			
			mapbox_bounds.push( [task_info.driver_location_lat , task_info.driver_location_lng] );
						
		} else {
			dump('add driver');				
			mapbox_marker[1] = L.marker([ task_info.driver_location_lat , task_info.driver_location_lng ], { 
		    	draggable : false,
		    	icon : toIcon(icon_driver)
		    } ).addTo(mapbox);  
		    mapbox_marker[1].bindPopup(driver_info_window);
		    
		    mapbox_bounds.push( [task_info.driver_location_lat , task_info.driver_location_lng] );
		    		    
		}		
	}
	
	if(!empty(task_info.dropoff_task_lat)){
		dump("has dropoff");
		if(empty(mapbox_marker[2])){
			dump('add drop marker');
			mapbox_marker[2] = L.marker([ task_info.dropoff_task_lat , task_info.dropoff_task_lng ], { 
	    	draggable : false,
		    	icon : toIcon(icon_dropoff)
		    } ).addTo(mapbox);  
		    mapbox_marker[2].bindPopup( task_info.drofoff_info_window );
		    
		    mapbox_bounds.push( [task_info.dropoff_task_lat , task_info.dropoff_task_lng] );
		    		    			
		} else {
			dump('drop marker exist');
			mapbox_bounds.push( [task_info.dropoff_task_lat , task_info.dropoff_task_lng] );
		}
	} else {
		dump("no dropoff");
		if(!empty(mapbox_marker[2])){
			dump('remove marker');
			mapbox.removeLayer(mapbox_marker[2]); 		
			mapbox_marker[2]=null;	
		}
	}
	
	mapbox_fitMap();  
	
};

mapbox_markerMove_driver = function(lat, lng){
	var newLatLng = new L.LatLng(lat, lng);
     mapbox_marker[1].setLatLng(newLatLng); 
};