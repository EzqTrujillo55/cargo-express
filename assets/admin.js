jQuery.fn.exists = function(){return this.length>0;}
var data_table;
var ajax_request;


function busy(e,button)
{
	if (e) {
        $('body').css('cursor', 'wait');	
    } else $('body').css('cursor', 'auto');        

    if (e) {    
    	dump('busy loading');        
        $(".main-preloader").show();
        if (!empty(button)){           
	 	   button.css({ 'pointer-events' : 'none' });	 	   	  
	 	}	 		 	
    } else {    	
    	dump('done loading');
    	$(".main-preloader").hide();    	
    	if (!empty(button)){
	 	   button.css({ 'pointer-events' : 'auto' });	 	   	  
	 	}
    }       
}

function empty(data)
{
	if (typeof data == "undefined" || data==null || data=="" ) { 
		return true;
	}
	return false;
}

function dump(data)
{
	console.debug(data);
}

$(document).ready(function(){	
		
	$.validate({ 	
		language : jsLanguageValidator,
	    form : '#frm',    
	    onError : function() {      
	    },
	    onSuccess : function() { 	           
	    	
	      var params= $("#frm").serialize();
	      
	      /*if ( $(".text-editor").exists() ){
		     var text_editor_val = $("textarea").sceditor('instance').getBody();		
		     params+="&text_editor_value="+text_editor_val.html();
      	  }*/
      	  
	      var action = $("#frm #action").val();
	      var button = $('#frm button[type="submit"]');
	      dump(button);
	      callAjax(action,params,button);
	      return false;
	    }  
	});    
	
	$.validate({ 	
		language : jsLanguageValidator,
	    form : '#frm-2',    
	    onError : function() {      
	    },
	    onSuccess : function() { 	           
	      var params= $("#frm-2").serialize();
	      var action = $("#frm-2 #action").val();
	      var button = $('#frm-2 button[type="submit"]');
	      dump(button);
	      callAjax(action,params,button);
	      return false;
	    }  
	});    
	
	$(document).ready(function() {
       $('select').material_select();
       $('.select-normal').material_select('destroy');
    });
	
    $('.numeric_only').keyup(function () {     
      this.value = this.value.replace(/[^0-9\.]/g,'');
    });	        
    
    if ( $("#msg").exists() ){
    	if ( $("#msg").val()!="" ){
    	   toast( $("#msg").val() );
    	}
    }    
        
    if ( $("#frm_table").exists() ){
    	initTable();
    }    
        
    $( document ).on( "click", ".rm-records", function() {    	     	    
   	    var params="tbl="+$(this).data("tbl");
   	    params+="&field="+$(this).data("field");
   	    params+="&value="+$(this).data("value");
   	    var a=confirm(jslang.delete_confirm+"?");
   	    if(a){
   	       callAjax("remRecords",params);
   	    }
    });   
    
   if ( $(".mobile_inputs").exists()){
      try {	
	      $(".mobile_inputs").intlTelInput({      
	        autoPlaceholder: false,		
	        defaultCountry: default_country,            
	        autoHideDialCode:true,    
	        nationalMode:false,
	        autoFormat:false,
	        utilsScript: site_url+"/assets/intel/lib/libphonenumber/build/utils.js"
	      });
	   }
	   catch(err) {
		 dump(err.message);
	   }   
    }	     

    switchEmailProvider( $(".email_provider:checked").val() );   
    
    $( document ).on( "click", ".email_provider", function() {    	     	        	
    	switchEmailProvider( $(this).val() );
    });	
    
    
    $( document ).on( "click", ".approved-customer", function() { 
    	callAjax("approvedCustomer", "customer_id="+$(this).data("id") );
    });		
    
    $( document ).on( "click", ".remove-logo", function() { 
    	var a=confirm(jslang.delete_confirm+"?");
   	    if(a){
    	   callAjax("removeLogo",'' );
   	    }
    });		
    
    /*if( $(".text-editor").exists() ){
	    $(".text-editor").sceditor({
	        plugins: "bbcode",	        
	    });
	    
	    if( $(".text-editor_val").exists() ){
	       $('.text-editor').sceditor('instance').val( $(".text-editor_val").html() , false );
	    }
    }*/
            
    $(".button-collapse").sideNav();
      
}); /*end docu*/


/*mycall*/
function callAjax(action,params,button)
{
		
	dump(ajax_url+"/"+action+"?"+params);
	
	params+="&language="+language;
	
	ajax_request = $.ajax({
		url: ajax_url+"/"+action, 
		data: params,
		type: 'post',                  
		//async: false,
		dataType: 'json',
		timeout: 6000,		
	 beforeSend: function() {
	 	dump("before=>");
	 	dump( ajax_request );
	 	if(ajax_request != null) {
	 	   ajax_request.abort();
	 	   dump("ajax abort");
	 	   busy(false,button);	 	   
	 	} else {
	 	   busy(true,button);	 	  
	 	}
	 },
	 complete: function(data) {					
		ajax_request= (function () { return; })();
		dump( 'Completed');
		dump(ajax_request);
		busy(false,button);	
	 },
	 success: function (data) {	  
	 	dump(data);
	 	
	 	dump("action->"+action);
	 	
	 	if (data.code==1){
	 			 	
	 		switch (action)
	 		{

	 			case "login":
	 			window.location.href = home_url+"/dashboard";
	 			break;
	 			
	 			case "addPlans":
	 			case "addCustomer":
	 			case "addCurrency":
	 			case "addCustomePage":
	 			case "addServices":
	 			case "addPromoCode":
	 			if(!empty(data.details)){
	 				window.location.href = data.details;
	 			}	 		
	 			toast(data.msg);	
	 			break;
	 			
	 			case "remRecords":	 	
	 			if ( data.details.table=="services"){
	 				toast(data.msg);
	 				window.location.href = data.details.redirect;
	 			} else {
		 			reloadTable();
		 			toast(data.msg);
	 			}
	 			break;
	 			
	 			case "signupCharts":
	 			$(".charts").html(data.details);
	 			break;
	 			
	 			case "approvedCustomer":
	 			toast(data.msg);
	 			reloadTable();
	 			break;
	 			
	 			case "removeLogo":
	 			$(".website_logo").remove();
	 			$(".remove-logo-wrap").remove();
	 			toast(data.msg);	 			
	 			break;
	 			
	 			
	 			case "generateServicesKey":
	 			  $("#api_services_key").val(data.details);	 			  
	 			  $("#api_services_key").focus();
	 			break;
	 						
	 			default:	 	 			
	 			toast(data.msg);
	 			break;
	 		}
	 		
	 	} else {
	 		
	 		// failed mycon
	 		switch ( action )
	 		{	 		 			
	 			// silent
	 			case "signupCharts":
	 			break;
	 			
	 			default :	 		
	 			toastf(data.msg);	
	 			break;
	 		}
	 			 		
	 	}
	 },
	 error: function (request,error) {	    
	 	 	 		
	 }
    });       
}

function toast(message)
{
	 Materialize.toast(message, 4000,'toast-success');
}
function toastf(message)
{
	 Materialize.toast(message, 4000);
}

function initTable()
{
	var action=$("#action").val();
	var params=$("#frm_table").serialize();	
	params+="&language="+language;
	 data_table = $('#table_list').dataTable({
    	   "iDisplayLength": 15,
	       "bProcessing": true, 	       
	       "bServerSide": true,
	       "bLengthChange": false,
	       "sAjaxSource": ajax_url+"/"+ action +"/?"+params,	       
	       "aaSorting": [[ 0, "desc" ]],
	       "oLanguage":{	       	 
	       	 "sProcessing": "<p>Processing.. <i class=\"fa fa-spinner fa-spin\"></i></p>"
	       },
	       "oLanguage": {
	       	  "sEmptyTable":    js_lang.tablet_1,
			    "sInfo":           js_lang.tablet_2,
			    "sInfoEmpty":      js_lang.tablet_3,
			    "sInfoFiltered":   js_lang.tablet_4,
			    "sInfoPostFix":    "",
			    "sInfoThousands":  ",",
			    "sLengthMenu":     js_lang.tablet_5,
			    "sLoadingRecords": js_lang.tablet_6,
			    "sProcessing":     js_lang.tablet_7,
			    "sSearch":         js_lang.tablet_8,
			    "sZeroRecords":    js_lang.tablet_9,
			    "oPaginate": {
			        "sFirst":    js_lang.tablet_10,
			        "sLast":     js_lang.tablet_11,
			        "sNext":     js_lang.tablet_12,
			        "sPrevious": js_lang.tablet_13
			    },
			    "oAria": {
			        "sSortAscending":  js_lang.tablet_14,
			        "sSortDescending": js_lang.tablet_15
			    }
	       },
	       "fnInitComplete": function(oSettings, json) {
	       	     	  		     
		   }
    });		
}

function reloadTable()
{	
	data_table.fnReloadAjax(); 
}

function switchEmailProvider(provider)
{
	dump(provider);
	if ( provider=="smtp"){
		$(".smtp_wrap").slideDown();
	} else {
		$(".smtp_wrap").slideUp();
	}
}

$(document).ready(function(){	
	
	if ( $("#upload-logo").exists() ){
		
		var btn = document.getElementById('upload-logo'),
        progressBar = document.getElementById('progressBar'),
        progressOuter = document.getElementById('progressOuter'),
        msgBox = document.getElementById('msgBox');
		
		var uploader = new ss.SimpleUpload({
        button: btn,
        url: ajax_url+'/uploadFile/?prefix=website_logo',
        name: 'uploadfile',
        multipart: true,
        allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
        hoverClass: 'hover',
        focusClass: 'focus',
        responseType: 'json',
        startXHR: function() {
            progressOuter.style.display = 'block'; // make progress bar visible
            this.setProgressBar( progressBar );
        },
        onSubmit: function() {
            msgBox.innerHTML = ''; // empty the message box
            btn.innerHTML = jslang.uploading +'...'; // change button text to "Uploading..."
          },
        onComplete: function( filename, response ) {
        	
        	dump(response);
        	dump(filename);
        	
            btn.innerHTML = jslang.upload_logo;
            progressOuter.style.display = 'none'; // hide progress bar when upload is completed

            if ( !response ) {
                msgBox.innerHTML = jslang.unabled_to_upload_file;
                return;
            }
                                   
            if ( response.success == 1 ) {                
                toast( jslang.success_uploaded );
                $(".website_logo").html(response.logo);
            } else {
                if ( response.msg )  {                    
                    toast( response.msg );
                } else {                    
                    toast( jslang.an_error_occured );
                }
            }
          },
          onExtError : function (filename, extension ){
          	  toastf( jslang.invalid_file_extension );
          }
	     });
	}
	
        if ( $("#upload-foto-servicio").exists() ){
		
		var btn = document.getElementById('upload-foto-servicio'),
        progressBar = document.getElementById('progressBar'),
        progressOuter = document.getElementById('progressOuter'),
        msgBox = document.getElementById('msgBox');
		
		var uploader = new ss.SimpleUpload({
        button: btn,
        url: ajax_url+'/uploadFotoServicio/?prefix=servicio',
        name: 'uploadfile',
        multipart: true,
        allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
        hoverClass: 'hover',
        focusClass: 'focus',
        responseType: 'json',
        startXHR: function() {
            progressOuter.style.display = 'block'; // make progress bar visible
            this.setProgressBar( progressBar );
        },
        onSubmit: function() {
            msgBox.innerHTML = ''; // empty the message box
            btn.innerHTML = jslang.uploading +'...'; // change button text to "Uploading..."
          },
        onComplete: function( filename, response ) {
        	
        	dump(response);
        	dump(filename);
        	
            btn.innerHTML = jslang.upload_logo;
            progressOuter.style.display = 'none'; // hide progress bar when upload is completed

            if ( !response ) {
                msgBox.innerHTML = jslang.unabled_to_upload_file;
                return;
            }
                                   
            if ( response.success == 1 ) {                
                toast( jslang.success_uploaded );
                $(".website_logo").html(response.logo);
                $("#foto").val(response.foto);
            } else {
                if ( response.msg )  {                    
                    toast( response.msg );
                } else {                    
                    toast( jslang.an_error_occured );
                }
            }
          },
          onExtError : function (filename, extension ){
          	  toastf( jslang.invalid_file_extension );
          }
	     });
	}
	
	if ( $(".charts").exists() ){
		callAjax('signupCharts','chartype=30');
	}
	
	if ( $("#upload-ios-dev").exists() ){	
		uploadCertifcate('ios_dev_certificate','upload-ios-dev','progressBar','progressOuter','msgBox' );	
	}
	if ( $("#upload-ios-prod").exists() ){	
		uploadCertifcate('ios_prod_certificate','upload-ios-prod','progressBar1','progressOuter1','msgBox1' );	
	}
	
	if ( $(".datepicker").exists() ){
	   $('.datepicker').pickadate({
	      selectMonths: true, 
	      selectYears: 15,
	      formatSubmit: 'yyyy-mm-dd',
	      format: 'yyyy-mm-dd'
	  });
	}
	
	$.validate({ 	
		language : jsLanguageValidator,
	    form : '#frm_table',    
	    onError : function() {      
	    },
	    onSuccess : function() { 
	      var action=$("#action").val();
	      var params=$("#frm_table").serialize();			           
	      data_table.fnReloadAjax( ajax_url+"/"+ action +"/?"+params  ); 
	      return false;
	    }  
	});    
	
}); /*end docu*/

function uploadCertifcate(prefix,btn,progressBar,progressOuter,msgBox)
{
	var btn = document.getElementById(btn),
    progressBar = document.getElementById(progressBar),
    progressOuter = document.getElementById(progressOuter),
    msgBox = document.getElementById(msgBox);
	
	var uploader = new ss.SimpleUpload({
    button: btn,
    url: ajax_url+'/uploadCertificateFile/?prefix='+prefix,
    name: 'uploadfile',
    multipart: true,
    allowedExtensions: ['pem'],
    hoverClass: 'hover',
    focusClass: 'focus',
    responseType: 'json',
    startXHR: function() {
        progressOuter.style.display = 'block'; // make progress bar visible
        this.setProgressBar( progressBar );
    },
    onSubmit: function() {
        msgBox.innerHTML = ''; // empty the message box
        btn.innerHTML = jslang.uploading +'...'; // change button text to "Uploading..."
      },
    onComplete: function( filename, response ) {
    	
    	dump(response);
    	dump(filename);
    	
        btn.innerHTML = jslang.upload_logo;
        progressOuter.style.display = 'none'; // hide progress bar when upload is completed

        if ( !response ) {
            msgBox.innerHTML = jslang.unabled_to_upload_file;
            return;
        }
                               
        if ( response.success == 1 ) {                
            toast( jslang.success_uploaded );
            //$(".website_logo").html(response.logo);
            $("#"+prefix).val(filename);
        } else {
            if ( response.msg )  {                    
                toast( response.msg );
            } else {                    
                toast( jslang.an_error_occured );
            }
        }
      },
      onExtError : function (filename, extension ){
      	  toastf( jslang.invalid_file_extension );
      }
     });
}


/*version 1.1 stars here*/

$(document).ready(function(){	

	$.validate({ 	
		language : jsLanguageValidator,
	    form : '#frm-3',    
	    onError : function() {      
	    },
	    onSuccess : function() { 	           
	      var params= $("#frm-3").serialize();
	      var action = $("#frm-3 #action").val();
	      var button = $('#frm-3 button[type="submit"]');
	      dump(button);
	      callAjax(action,params,button);
	      return false;
	    }  
	});    
	
	$.validate({ 	
		language : jsLanguageValidator,
	    form : '#frm-4',    
	    onError : function() {      
	    },
	    onSuccess : function() { 	           
	      var params= $("#frm-4").serialize();
	      var action = $("#frm-4 #action").val();
	      var button = $('#frm-4 button[type="submit"]');
	      dump(button);
	      callAjax(action,params,button);
	      return false;
	    }  
	});    
	
	$.validate({ 	
		language : jsLanguageValidator,
	    form : '#frm-5',    
	    onError : function() {      
	    },
	    onSuccess : function() { 	           
	      var params= $("#frm-5").serialize();
	      var action = $("#frm-5 #action").val();
	      var button = $('#frm-5 button[type="submit"]');
	      dump(button);
	      callAjax(action,params,button);
	      return false;
	    }  
	});   
	
	$.validate({ 	
		language : jsLanguageValidator,
	    form : '#frm-forgotpass',    
	    onError : function() {      
	    },
	    onSuccess : function() { 	           
	      var params= $("#frm-forgotpass").serialize();
	      var action = $("#frm-forgotpass #action").val();
	      var button = $('#frm-forgotpass button[type="submit"]');
	      dump(button);
	      callAjax(action,params,button);
	      return false;
	    }  
	});    
		
	$( document ).on( "click", ".admin_forgot_pass", function() {    	     	    		
		$("#admin-login-wrap").hide();
		$("#frm-forgotpass").fadeIn();
    });   
    $( document ).on( "click", ".admin_back_login", function() {    	     	    		
		$("#admin-login-wrap").fadeIn();
		$("#frm-forgotpass").hide();
    });   
    
    if ( $(".services-list").exists() ){
	    $( ".services-list" ).sortable({
		  	  update: function( event, ui ) {
		  	  	  dump('update question');
		  	  	  var ids='';
		  	  	  $.each( $(this).find("li.services-list-li") , function() { 	 			  	  
		  	  	  	  ids+= $(this).data("id") + ",";
		  	  	  });	 			  	  	  
		  	  	  callAjax("sortServicesParent","ids="+ids);
		  	  }
		});
		$( ".services-list-child" ).sortable({
		  	  update: function( event, ui ) {
		  	  	  dump('update question');
		  	  	  var ids='';
		  	  	  $.each( $(this).find("li.services-list-child") , function() { 	 			  	  
		  	  	  	  ids+= $(this).data("id") + ",";
		  	  	  });	 			  	  	  
		  	  	  callAjax("sortServicesParent","ids="+ids);
		  	  }
		});
    }
    
    $( document ).on( "click", ".send-test-sms", function() {
    	 var phone_number = prompt("Please enter your Phone number", "");
    	 if (phone_number != null) {
    	 	callAjax("sendTestSMS","phone_number="+ encodeURIComponent(phone_number));
    	 }
    });
    
    $( document ).on( "click", ".read_more", function() {
		$(this).parent().find(".truncate-text").addClass("remove-truncate-text");
		$(this).remove();
	});
	
	$( document ).on( "click", ".gen_api_services_key", function() {		
		callAjax("generateServicesKey","");
	});
	
});  /*end docu*/


/*PAYSTACK*/
$(document).ready(function(){	

$.validate({ 	
		language : jsLanguageValidator,
	    form : '#frm-6',    
	    onError : function() {      
	    },
	    onSuccess : function() { 	           
	      var params= $("#frm-6").serialize();
	      var action = $("#frm-6 #action").val();
	      var button = $('#frm-6 button[type="submit"]');
	      dump(button);
	      callAjax(action,params,button);
	      return false;
	    }  
	});   
	
});  /*end docu*/	