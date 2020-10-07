var mapbox;
var mapbox_marker = [];
var mapbox_bounds = [];

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
	
};

mapbox_plotTaskMap = function(data, auto_fit){
	dump("mapbox_plotTaskMap");
	if ( data.length >0) {
		$.each( data , function( index, val ) {
			
			lat = val.lat;
			lng = val.lng;
			
			if (!empty(val.lat)){
				
				if ( map_hide_delivery==1){
			 	   if ( val.trans_type_raw=="delivery"){
			 	 	   return;
			 	   }
			 	}
			 	 
			 	if ( map_hide_pickup==1){
			 	 	  if ( val.trans_type_raw=="pickup"){
			 	 	  	   return;
			 	 	  }
			 	 }
			 	 
			 	 if ( map_hide_success_task==1){
			 	 	  if ( val.status_raw=="successful"){
			 	 	  	   return;
			 	 	  }
			 	}
			 	
			 	info_html='';
		 	 
			 	 if ( val.map_type=="restaurant"){
			 	 info_html+="<div class=\"map-info-window\">";
			 	    info_html+="<h4>"+ jslang.task_id + ": " + val.task_id+"</h4>";
			 	    info_html+="<h5>"+ jslang.name + ": " + val.customer_name+"</h5>";
			 	    info_html+="<p>"+val.address+"</p>";		 	    
			 	    info_html+="<p class=\"inline green-button small rounded\">"+val.trans_type+"</p>";
			 	    info_html+="<p class=\"inline orange-button-small rounded\">"+val.status+"</p>";		 	    
			 	    info_html+="<a href=\"javascript:;\"  class=\"top10 task-details\" data-id=\""+val.task_id+"\"  >"+jslang.details+"</a>";
			 	 info_html+="</div>";
			 	 } else {
			 	 	info_html+=val.first_name+" ";
		  	 	    info_html+=val.last_name;
			 	 }

			 	 latlng = [lat,lng];
		         mapbox_bounds.push( latlng );
		         		        
		         if ( val.map_type=="restaurant"){
		         	 icon = mapbox_getIcon( val.trans_type, val.status_raw );
		         } else {
		         	 icon = mapbox_getDriverIcon( val.is_online );
		         }
		         		         
		         mapbox_marker[index] = L.marker([lat,lng], { icon : icon } ).addTo(mapbox);
		         //mapbox_marker[index] = L.marker([lat,lng] ).addTo(mapbox);
		         mapbox_marker[index].bindPopup(info_html);
		        
			} /*empty val*/
		});
		
		if(auto_fit){			
		   mapbox_fitMap();
		}
	}
};

mapbox_getIcon = function(trans_type, status_raw){
	var icon = '';
	
	if(trans_type=="delivery"){
		switch (status_raw){
			case "successful":	
			  icon=delivery_icon_success;
			break;
			
			case "failed":
   	 		case "declined":
   	 		case "cancelled":
   	 		  icon=delivery_icon_failed;
   	 		break;
   	 		
   	 		default: 
   	 		  icon=map_marker_delivery;
   	 		break;
		}
	} else {
		switch (status_raw)	{
   	 		case "successful":	
   	 		icon=pickup_icon_success;
   	 		break;
   	 		
   	 	    case "failed":
   	 		case "declined":
   	 		case "cancelled":
   	 		icon=pickup_icon_failed;
   	 		break;
   	 		
   	 		default: 
   	 		icon=map_pickup_icon;
   	 		break;
   	 	}
	}
	
	return default_icon = L.icon({
		iconUrl: icon,	    	   
	});
};

mapbox_getDriverIcon = function(is_online){
	var icon;
	if(is_online==1){
		icon=driver_icon_online;
	} else {
		icon=driver_icon_offline;
	}
	return default_icon = L.icon({
		iconUrl: icon,	    	   
	});
};



mapbox_moveMapMarker = function(lat, lng){
		
	if(empty(lat) || empty(lng)){
		return;
	}		
	if(empty(mapbox_marker)){             	
        mapbox_marker = L.marker([ lat , lng ], { draggable : true } ).addTo(mapbox);  
     } else {             	 
     	var newLatLng = new L.LatLng(lat, lng);
     	mapbox_marker.setLatLng(newLatLng); 
     }	
     mapbox_setMapCenter(lat , lng);          
};


/*TASK MAP*/

var mapbox_d;
var mapbox_marker_d = [];
var mapbox_bounds_d = [];

mapbox_PlotMapDelivery = function(div, lat , lng){
	
	if (mapbox_d != undefined) {		
		mapbox_d.remove();
	}	
		
	mapbox_d = L.map(div,{ 
		scrollWheelZoom:true,
		zoomControl:true,
	 }).setView([lat,lng], 5 );
	
	L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token='+mapbox_token, {		    
	    maxZoom: 18,
	    id: 'mapbox.streets',		    
	}).addTo(mapbox_d);	
};

mapbox_initGeocoderCompany = function(div){
	
	mapbox_marker_d = '';
	
	data = $("#"+div).html();	
	if(empty(data)){
		var geocoder = new MapboxGeocoder({
		    accessToken: mapbox_token ,
		    country: default_country ,
		    flyTo : false
	    });		    
	    document.getElementById(div).appendChild(geocoder.onAdd(mapbox_d));	   
	    
	    $("#"+div +" input").attr("name","direccion_matriz");
	    $("#"+div +" input").attr("id","direccion_matriz");
	    $("#"+div +" input").attr("placeholder", jslang.delivery_address );	
	    $("#"+div +" input").attr("autocomplete","off");
	    $("#"+div +" input").attr("required","required");
	    
	    geocoder.on('result', function(ev) {
             dump("ev.result.geometry");             
             resp_geocoder = ev.result.geometry;                                   
             lat = resp_geocoder.coordinates[1];
             lng = resp_geocoder.coordinates[0];
             
             if(empty(mapbox_marker_d)){             	
                mapbox_marker_d = L.marker([ lat , lng ], { draggable : true } ).addTo(mapbox_d);  
             } else {             	 
             	var newLatLng = new L.LatLng(lat, lng);
             	mapbox_marker_d.setLatLng(newLatLng); 
             }
             
             mapbox_setMapCenterDelivery(lat , lng);
             
             $("#latitud_matriz").val( lat );
             $("#longitud_matriz").val( lng );
             
             mapbox_marker_d.on('dragend', function (e) {	
             	var latlng = e.target.getLatLng();
             	dump(latlng.lat);
             	dump(latlng.lng);
			    $("#latitud_matriz").val( latlng.lat );
                $("#longitud_matriz").val( latlng.lng );
			});
		             
        });
	    
	}
};


mapbox_initGeocoderDelivery = function(div){
	
	mapbox_marker_d = '';
	
	data = $("#"+div).html();	
	if(empty(data)){
		var geocoder = new MapboxGeocoder({
		    accessToken: mapbox_token ,
		    country: default_country ,
		    flyTo : false
	    });		    
	    document.getElementById(div).appendChild(geocoder.onAdd(mapbox_d));	   
	    
	    $("#"+div +" input").attr("name","delivery_address");
	    $("#"+div +" input").attr("id","delivery_address");
	    $("#"+div +" input").attr("placeholder", jslang.delivery_address );	
	    $("#"+div +" input").attr("autocomplete","off");
	    $("#"+div +" input").attr("required","required");
	    
	    geocoder.on('result', function(ev) {
             dump("ev.result.geometry");             
             resp_geocoder = ev.result.geometry;                                   
             lat = resp_geocoder.coordinates[1];
             lng = resp_geocoder.coordinates[0];
             
             if(empty(mapbox_marker_d)){             	
                mapbox_marker_d = L.marker([ lat , lng ], { draggable : true } ).addTo(mapbox_d);  
             } else {             	 
             	var newLatLng = new L.LatLng(lat, lng);
             	mapbox_marker_d.setLatLng(newLatLng); 
             }
             
             mapbox_setMapCenterDelivery(lat , lng);
             
             $("#task_lat").val( lat );
             $("#task_lng").val( lng );
             
             mapbox_marker_d.on('dragend', function (e) {	
             	var latlng = e.target.getLatLng();
             	dump(latlng.lat);
             	dump(latlng.lng);
			    $("#task_lat").val( latlng.lat );
                $("#task_lng").val( latlng.lng );
			});
		             
        });
	    
	}
};

mapbox_setMapCenterDelivery = function(lat, lng){
	mapbox_d.setView(new L.LatLng(lat, lng), 15);
};


mapbox_plotMarkerDelivery = function(lat, lng){
		
	if(empty(lat) || empty(lng)){
		return;
	}
		
	if(empty(mapbox_marker_d)){             	
        mapbox_marker_d = L.marker([ lat , lng ], { draggable : true } ).addTo(mapbox_d);  
     } else {             	 
     	var newLatLng = new L.LatLng(lat, lng);
     	mapbox_marker_d.setLatLng(newLatLng); 
     }	
     mapbox_setMapCenterDelivery(lat , lng);
     
     mapbox_marker_d.on('dragend', function (e) {           			    
	    var latlng = e.target.getLatLng();
     	dump(latlng.lat);
     	dump(latlng.lng);
	    $("#task_lat").val( latlng.lat );
        $("#task_lng").val( latlng.lng );
	 });
};


/*PICKUP*/

var mapbox_p;
var mapbox_marker_p = [];
var mapbox_bounds_p = [];

mapbox_PlotMapPickup = function(div, lat , lng){
	
	if (mapbox_p != undefined) {		
		mapbox_p.remove();
	}	
		
	mapbox_p = L.map(div,{ 
		scrollWheelZoom:true,
		zoomControl:true,
	 }).setView([lat,lng], 5 );
	
	L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token='+mapbox_token, {		    
	    maxZoom: 18,
	    id: 'mapbox.streets',		    
	}).addTo(mapbox_p);	
};


mapbox_initGeocoderPickup = function(div){
		
	dump('mapbox_initGeocoderPickup');
	mapbox_marker_p = '';
	
	data = $("#"+div).html();	
	if(empty(data)){		
		var geocoder_p = new MapboxGeocoder({
		    accessToken: mapbox_token ,
		    country: default_country ,
		    flyTo : false
	    });		    
	    document.getElementById(div).appendChild(geocoder_p.onAdd(mapbox_p));	   
	    
	    $("#"+div +" input").attr("name","drop_address");
	    $("#"+div +" input").attr("id","drop_address");
	    $("#"+div +" input").attr("placeholder", jslang.address );	
	    $("#"+div +" input").attr("autocomplete","off");
	    //$("#"+div +" input").attr("required","required");
	    
	    geocoder_p.on('result', function(ev) {
             dump("ev.result.geometry");             
             resp_geocoder = ev.result.geometry;                                   
             lat = resp_geocoder.coordinates[1];
             lng = resp_geocoder.coordinates[0];
             
             if(empty(mapbox_marker_p)){             	
                mapbox_marker_p = L.marker([ lat , lng ], { draggable : true } ).addTo(mapbox_p);  
             } else {             	 
             	var newLatLng = new L.LatLng(lat, lng);
             	mapbox_marker_p.setLatLng(newLatLng); 
             }
             
             mapbox_setMapCenterPickup(lat , lng);
             
             $("#dropoff_task_lat").val( lat );
             $("#dropoff_task_lng").val( lng );
             
             mapbox_marker_p.on('dragend', function (e) {           			    
			    var latlng = e.target.getLatLng();
			    dump(latlng.lat);
             	dump(latlng.lng);
			    $("#dropoff_task_lat").val( latlng.lat );
                $("#dropoff_task_lng").val( latlng.lng );
			});
		             
        });
	    
	}
};

mapbox_setMapCenterPickup = function(lat, lng){
	mapbox_p.setView(new L.LatLng(lat, lng), 15);
};


mapbox_plotMarkerPickup = function(lat, lng){
	
	if(empty(lat) || empty(lng)){
		return;
	}
	if(empty(mapbox_marker_p)){             	
        mapbox_marker_p = L.marker([ lat , lng ], { draggable : true } ).addTo(mapbox_p);  
     } else {             	 
     	var newLatLng = new L.LatLng(lat, lng);
     	mapbox_marker_p.setLatLng(newLatLng); 
     }	
     mapbox_setMapCenterPickup(lat , lng);
     
     mapbox_marker_p.on('dragend', function (e) {           			    
	    var latlng = e.target.getLatLng();
     	dump(latlng.lat);
     	dump(latlng.lng);
	    $("#dropoff_task_lat").val( latlng.lat );
        $("#dropoff_task_lng").val( latlng.lng );
	 });
};

/*CONTACT MAP*/
mapbox_initGeocoderContact = function(div){
	
	mapbox_marker = '';
	
	data = $("#"+div).html();	
	if(empty(data)){
		var geocoder = new MapboxGeocoder({
		    accessToken: mapbox_token ,
		    country: default_country ,
		    flyTo : false
	    });		    
	    document.getElementById(div).appendChild(geocoder.onAdd(mapbox));	   
	    
	    $("#"+div +" input").attr("name","address");
	    $("#"+div +" input").attr("id","address");
	    $("#"+div +" input").attr("placeholder", jslang.delivery_address );	
	    $("#"+div +" input").attr("autocomplete","off");
	    $("#"+div +" input").attr("required","required");
	    
	    geocoder.on('result', function(ev) {
             dump("ev.result.geometry");             
             resp_geocoder = ev.result.geometry;                                   
             lat = resp_geocoder.coordinates[1];
             lng = resp_geocoder.coordinates[0];
             
             if(empty(mapbox_marker)){             	
                mapbox_marker = L.marker([ lat , lng ], { draggable : true } ).addTo(mapbox);  
             } else {             	 
             	var newLatLng = new L.LatLng(lat, lng);
             	mapbox_marker.setLatLng(newLatLng); 
             }
             
             mapbox_setMapCenter(lat , lng);
             
             $("#addresss_lat").val( lat );
             $("#addresss_lng").val( lng );
             
             mapbox_marker.on('dragend', function (e) {	
             	var latlng = e.target.getLatLng();
             	dump(latlng.lat);
             	dump(latlng.lng);
			    $("#addresss_lat").val( latlng.lat );
                $("#addresss_lng").val( latlng.lng );
			});
		             
        });
	    
	}
};


mapbox_plotMarkerContact = function(lat, lng){
	
	if(empty(lat) || empty(lng)){
		return;
	}
	if(empty(mapbox_marker)){             	
        mapbox_marker = L.marker([ lat , lng ], { draggable : true } ).addTo(mapbox);  
     } else {             	 
     	var newLatLng = new L.LatLng(lat, lng);
     	mapbox_marker.setLatLng(newLatLng); 
     }	
     mapbox_setMapCenter(lat , lng);
     
     mapbox_marker.on('dragend', function (e) {           			    
	    var latlng = e.target.getLatLng();
     	dump(latlng.lat);
     	dump(latlng.lng);
	    $("#addresss_lat").val( latlng.lat );
        $("#addresss_lng").val( latlng.lng );
	 });
};

mapbox_initGeocoderSearch = function(div){
		
	data = $("#"+div).html();		
	if(empty(data)){	
		var geocoder = new MapboxGeocoder({
		    accessToken: mapbox_token ,
		    country: default_country ,
		    flyTo : false
	    });		    
	    document.getElementById(div).appendChild(geocoder.onAdd(mapbox));	   
	    
	    $("#"+div +" input").attr("name","address");
	    $("#"+div +" input").attr("id","address");
	    $("#"+div +" input").attr("placeholder", jslang.delivery_address );	
	    $("#"+div +" input").attr("autocomplete","off");
	    $("#"+div +" input").attr("required","required");
	    
	    geocoder.on('result', function(ev) {
             dump("ev.result.geometry");             
             resp_geocoder = ev.result.geometry;                                   
             lat = resp_geocoder.coordinates[1];
             lng = resp_geocoder.coordinates[0];
             
             mapbox_setMapCenter(lat , lng);
                     
        });
	    
	} 	
};


/*LOCATION MAP*/

var mapbox_location;
var mapbox_marker_location;


mapbox_plotLocationMap = function(div, lat , lng){
	
	if (mapbox_location != undefined) {		
		mapbox_location.remove();
	}	
	
	mapbox_location = L.map(div,{ 
		scrollWheelZoom:true,
		zoomControl:true,
	 }).setView([lat,lng], 5 );
	
	L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token='+mapbox_token, {		    
	    maxZoom: 18,
	    id: 'mapbox.streets',		    
	}).addTo(mapbox_location);
	
	mapbox_marker_location = L.marker([lat,lng], { 
		draggable:false
	} ).addTo(mapbox_location);
	
	mapbox_location.setView(new L.LatLng(lat, lng), 15);
	
};