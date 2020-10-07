var ajax_request_2;
var ajax_request_3;
var ajax_request_4;


function callAjax2(action,params,button)
{
	params+="&language="+language;
	
	dump(ajax_url+"/"+action+"?"+params);
	
	ajax_request_2 = $.ajax({
		url: ajax_url+"/"+action, 
		data: params,
		type: 'post',                  
		//async: false,
		dataType: 'json',
		timeout: 6000,		
	 beforeSend: function() {
	 	dump( ajax_request_2 );
	 	if(ajax_request_2 != null) {
	 	   ajax_request_2.abort();	 	   
	 	   busy(false,button);	 	   
	 	} else {
	 	   busy(true,button);	 	  
	 	}
	 },
	 complete: function(data) {		 		
		ajax_request_2 = (function () { return; })();		
		busy(false,button);	
	 },
	 success: function (data) {	  
	 	 if (data.code==1){
	 	 	 switch (action)
	 	 	 {
	 	 	 
	 	 	 	case "getDashboardTask":
	 			$(".task_"+data.msg).html( data.details.html );
	 			$(".task-total-"+data.msg).html( data.details.total );
	 			break;
	 			
	 			case "loadAgentDashboard":		 			  
	 			  fillAgentDashboard(data);
	 			break;
	 			
	 	 	 	default:
	 			nAlert(data.msg,"success");
	 			break;	
	 	 	 }
	 	 } else {
	 	 	 switch ( action )
	 	 	 {
	 	 	 	case "getDashboardTask":	 
	 			$(".task_"+data.details).html( '' );
	 			$(".task-total-"+data.details).html( "0" );
	 			break;
	 			
	 			//silent
	 			case "loadAgentDashboard":	
	 			break;
	 			
	 	 	 	default :
	 			nAlert(data.msg,"warning");
	 			break;
	 	 	 }
	 	 }
	 },
	 error: function (request,error) {	    
	 	 	 		
	 }
    });   	
}

function callAjaxSilent(action,params,button)
{
	
	params+="&language="+language;
	
	dump(ajax_url+"/"+action+"?"+params);	
	
	ajax_request_3 = $.ajax({
		url: ajax_url+"/"+action, 
		data: params,
		type: 'post',                  		
		dataType: 'json',
		timeout: 6000,		
	 beforeSend: function() {
	 	if(ajax_request_3 != null) {
	 	   ajax_request_3.abort();
	 	   dump("ajax abort");	 	     
	 	} else {	 	   
	 	}
	 },
	 complete: function(data) {							
		ajax_request_3 = (function () { return; })();	
		//callAjaxSilent(action,params,button);
	 },
	 success: function (data) {	  	 	
	 	if (data.code==1){
	 	 	 switch (action)
	 	 	 {
	 	 	 
	 	 	 	case "getDashboardTask":
	 			
	 	 	 	$.each( data.details , function( key, val ) {     	 				
	 				if ( !empty(val)){	 					
	 					$(".task_"+key).html( val.html );
	 			        $(".task-total-"+key).html( val.total );
	 				} else {	 					
	 				   $(".task_"+key).html( '');
	 			       $(".task-total-"+key).html( "0" );
	 				}
	 			});	 			
	 				 			
	 			dashboard_run_silent=1;
	 			dump( "coordinatesxx=>" + data.msg.length);	 			
	 			//plotTaskMap( data.msg  );
	 			
	 			  if (map_provider=="google"){
	 			  	  plotTaskMap( data.msg  );
	 			  } else if ( map_provider=="mapbox"){
	 			  	  mapbox_plotTaskMap(data.msg , false);
	 			  }
	 			
	 			break;
	 			
	 	 	 	default:
	 			nAlert(data.msg,"success");
	 			break;	
	 	 	 }
	 	 } else {
	 	 	 switch ( action )
	 	 	 {
	 	 	 	case "getDashboardTask":	 	 			
	 			break;
	 				 			
	 			
	 	 	 	default :
	 			nAlert(data.msg,"warning");
	 			break;
	 	 	 }
	 	 }
	 },
	 error: function (request,error) {	    
	 	 	 		
	 }
    });   	
}

function callAjaxSilent2(action,params,button)
{
	params+="&language="+language;
	
	dump(ajax_url+"/"+action+"?"+params);
	
	ajax_request_4 = $.ajax({
		url: ajax_url+"/"+action, 
		data: params,
		type: 'post',                  		
		dataType: 'json',
		timeout: 6000,		
	 beforeSend: function() {
	 	if(ajax_request_4 != null) {
	 	   ajax_request_4.abort();
	 	   dump("ajax abort");	 	   
	 	} else {	 	   
	 	}
	 },
	 complete: function(data) {							
		ajax_request_4 = (function () { return; })();				
	 },
	 success: function (data) {	  	 	
	 	if (data.code==1){
	 	 	 switch (action)
	 	 	 {
	 	 	 	 	 	 
	 			case "loadAgentDashboard":		 			  
	 			  fillAgentDashboard(data);
	 			break;
	 			
	 	 	 	default:
	 			nAlert(data.msg,"success");
	 			break;	
	 	 	 }
	 	 } else {
	 	 	 switch ( action )
	 	 	 {	 	 		
	 			case "loadAgentDashboard":
	 			break;
	 			
	 	 	 	default :
	 			nAlert(data.msg,"warning");
	 			break;
	 	 	 }
	 	 }
	 },
	 error: function (request,error) {	    
	 	 	 		
	 }
    });   	
}

function formatAgetList(data,status)
{
    if (empty(data)){
    	return '';
    }
	var class_name='tag_offline';
	
	if (data.is_online==1){
		class_name='tag_online';
		if ( data.online_status=="lost_connection"){
			class_name='tag_offline';
		}
	}
	
	var html='';
	html+='<div class="row box locate-driver-onmap" data-lat="'+data.location_lat+'" data-lng="'+data.location_lng+'" >';
      html+='<div class="col-xs-2 center"> ';
       html+='<div class="top10"><i class="ion-ios-circle-filled '+class_name+' "></i></div> ';
      html+='</div> <!--col-->';
      
      html+='<div class="col-xs-7"> ';
        html+='<p class="agent_name">'+  data.first_name + " "+ data.last_name +'</p>  ';
        html+='<p class="connection_status">'+status+'</p>';
        html+='<p class="connection_status">'+ data.last_seen +'</p>';
        
        if(!empty(data.device_platform)){
         	html+='<p class="connection_status" style="font-weight: bold;">'+data.device_platform+'</p>';
        }  
         
        on_duty_stats='';
        if(data.on_duty==1){
        	//on_duty_stats = jslang.yes;
        	on_duty_stats = '<i class="ion-checkmark tag_online"></i>';
        } else {
        	//on_duty_stats = jslang.no;
        	on_duty_stats = '<i class="ion-close"></i>';
        }
        
        html+='<p class="connection_status">'+  jslang.on_duty + " : " + on_duty_stats +'</p>';
        
        html+="<a href=\"javascript:;\" data-id=\""+data.driver_id+"\" class=\"view-driver-details\">"+jslang.details+"</a>";
        
        html+="<a href=\"javascript:;\" data-id=\""+data.driver_id+"\" class=\"open-modal-push\">"+jslang.send_push+"</a>";
        
      html+='</div> <!--col-->';
      
      html+='<div class="col-xs-3 center">';
         html+='<p class="number_of_task">'+data.total_task+'</p>';
         html+='<p class="text-muted">'+jslang.task+'</p>';
      html+='</div>';
      
    html+='</div> <!--row-->';
    return html;
}

function formatTableRow(data)
{
	var link='<a style="display:block;" data-id="'+data.task_id+'" class="task-details" href="javascript:;">'+data.task_id+'</a>';
	var html='';
      html+='<tr>';
      html+='<td>'+link+'</td>';
       html+='<td>'+data.customer_name+'</td>';
       html+='<td>'+data.trans_type+'</td>';
       html+='<td>'+data.delivery_address+'</td>';
       html+='<td><span class="tag '+data.status_raw+'">'+data.status+'</span></td>';
      html+='</tr>';
    return html;  
}

function fillAgentDashboard(data)
{
	$(".agent-active-total").html( data.details.active.length );
	  if (data.details.active.length >0 ){
	  	 var html_offline='';
	  	 $.each( data.details.active , function( key, val ) {  
	  	 	  var agent_status='';
	  	 	  if ( val.is_online==1) {
	  	 	  	  if ( val.online_status=="lost_connection"){
	  	 	  	  	   agent_status=jslang.connection_lost;
	  	 	  	  } else agent_status=jslang.online;	  	 	  	  
	  	 	  } else {
	  	 	  	  agent_status=jslang.offline;
	  	 	  }
	  	 	  //html_offline += formatAgetList(val , val.is_online==1?jslang.online:jslang.offline );
	  	 	  html_offline += formatAgetList(val , agent_status );
	  	 	  	  	 	  
	  	 	  var info_window='';
	  	 	  info_window+=val.first_name+" ";
	  	 	  info_window+=val.last_name;
	  	 	  //setDriverMarker( val.location_lat , val.location_lng , info_window);
	  	 });	
	  	 $(".agent-active").html(html_offline);
	  } else {
	  	 $(".agent-active").html('');
	  }
	  
	  dump(data.details.offline.length);
	  $(".agent-offline-total").html( data.details.offline.length );
	  if (data.details.offline.length >0 ){
	  	 var html_offline='';
	  	 $.each( data.details.offline , function( key, val ) {     
	  	 	  html_offline += formatAgetList(val , jslang.connection_lost);
	  	 });	
	  	 $(".agent-offline").html(html_offline);
	  } else {
	  	 $(".agent-offline").html('');
	  }
	  
	  //dump(data.details.total.length);
	  $(".agent-total-total").html( data.details.total.length );
	  if (data.details.total.length >0 ){
	  	 var html_offline='';
	  	 $.each( data.details.total , function( key, val ) {     
	  	 	  html_offline += formatAgetList(val , val.is_online==1?jslang.online:jslang.offline );
	  	 });	
	  	 $(".agent-total").html(html_offline);
	  } else {
	  	 $(".agent-total").html('');
	  }
}