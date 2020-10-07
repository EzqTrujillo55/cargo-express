
<div class="card">
 <div class="card-content">
   
   <h5><?php echo t("Website Information")?></h5>  
  
   <form id="frm" method="POST" onsubmit="return false;">
   <?php echo CHtml::hiddenField('action','generalSettings')?>
   
   <div class="row">
   <div class="col s6">  
   
   <div class="input-field">	    
     <?php echo CHtml::textField('company_name',
     getOptionA('company_name')
     ,array('class'=>"validate",'data-validation'=>"required"))?>
     <label><?php echo t("Company Name")?></label>
   </div>  
   
   <div class="input-field">	    
     <?php echo CHtml::textField('contact_number',
     getOptionA('contact_number')
     ,array('class'=>"validate mobile_inputs",
     //'data-validation'=>"required",
     'placeholder'=>t("Contact number")
     ))?>
     <!--<label><?php echo t("Contact number")?></label>-->
   </div>  
   
   </div>  <!--col-->
   
   <div class="col s6">  

      <div class="input-field">	    
       <?php echo CHtml::textField('company_address',
       getOptionA('company_address')
       ,array('class'=>"validate"))?>
       <label><?php echo t("Company Address")?></label>
      </div>    
      
      <div class="input-field">	    
     <?php echo CHtml::textField('email_address',
     getOptionA('email_address')
     ,array('class'=>"validate"))?>
     <label><?php echo t("Email address")?></label>
   </div>  
   
   </div>  <!--col-->
   
   </div> <!--row-->
   
   <div class="row">
     <div class="col s6">
       <div class="input-field">
	       <?php echo CHtml::dropDownList('website_default_country',
	        getOptionA('website_default_country'),
	        AdminFunctions::getCountryList())?>
		    <label><?php echo t("Default Country")?></label>
	   </div>
     </div>
     
     <div class="col s3">
         <a id="upload-logo" class="waves-effect blue lighten-1 btn"><?php echo t("Upload Logo")?></a>
         
         <div id="progressBar"></div>
         <div id="progressOuter"></div>
         <div id="msgBox"></div>         
     </div>
     
     <div class="col s3">
       <div class="website_logo card-panel grey lighten-5 z-depth-1">
         <?php if(!empty($logo)):?>       
         <img class="responsive-img" src="<?php echo $logo?>">         
         <?php endif?>
       </div>
       
       <?php if(!empty($logo)):?>       
        <p class="remove-logo-wrap">
         <a class="remove-logo"  href="#no" ><?php echo t("Click here")?> </a> <?php echo t("to remove logo")?>
        </p>
       <?php endif;?>
         
     </div>
     
   </div>
   
   <div class="row">
     <div class="col s6">
     
     <div class="input-field">	    
       <?php echo CHtml::textArea('website_custom_footer',
       getOptionA('website_custom_footer')
       ,array('class'=>"materialize-textarea"))?>
       <label><?php echo t("Custom Footer")?></label>
      </div>  
     
     </div>
   </div>
   
   
   <h5><?php echo t("Mail Settings")?></h5>
   
   <div class="row">     
	   <div class="col s2">  
	      <p>
	      <!--<input name="group1" type="radio" id="test1" class="with-gap" />-->
	      <?php echo CHtml::radioButton('email_provider',
	      getOptionA('email_provider')=="php_mail"?true:false
	      ,array(
	        'id'=>"email_provider",
	        'class'=>"with-gap email_provider",
	        'value'=>"php_mail"
	      ))?>
	      <label for="email_provider"><?php echo t("Use php mail")?></label>
	    </p>
	   </div> <!--col-->
	   <div class="col s2">  
	      <p>
	      <!--<input name="group1" type="radio" id="test2" class="with-gap" />-->
	      <?php echo CHtml::radioButton('email_provider',
	      getOptionA('email_provider')=="smtp"?true:false
	      ,array(
	        'id'=>"email_provider1",
	        'class'=>"with-gap email_provider",
	        'value'=>"smtp"
	      ))?>
	      <label for="email_provider1"><?php echo t("Use SMTP")?></label>
	    </p>
	   </div> <!--col-->
	   
	  <div class="col s4">
	      <a href="<?php echo Yii::app()->createUrl('/admin/testemail')?>" 
	      class="btn"><?php echo t("Click here to send Test Email")?></a>
	  </div> 
   </div> <!--row-->
   
   
   <div class="smtp_wrap">
   
   <div class="row">
       <div class="col s6">
           <div class="input-field">	    
	       <?php echo CHtml::textField('smtp_host',
	       getOptionA('smtp_host')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("SMTP host")?></label>
	      </div>  
       </div>
       <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::textField('smtp_port',
	       getOptionA('smtp_port')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("SMTP port")?></label>
	      </div>  
       </div>
   </div> 
   
    <div class="row">
       <div class="col s6">
           <div class="input-field">	    
	       <?php echo CHtml::textField('smtp_username',
	       getOptionA('smtp_username')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("Username")?></label>
	      </div>  
       </div>
       <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::textField('smtp_password',
	       getOptionA('smtp_password')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("Password")?></label>
	      </div>  
       </div>
   </div> 
   
   <div class="row">
     <div class="col s6">
         <div class="input-field">
	       <?php echo CHtml::dropDownList('smtp_secure',
	        getOptionA('smtp_secure'),
	        array(
	          'tls'=>"tls",
	          'ssl'=>"ssl"
	        ))?>
		    <label><?php echo t("SMTP Secure")?></label>
	     </div>
     </div>
   </div>
   
   </div> <!--smtp_wrap-->
      
   <div class="row">   
   <div class="col s6">  
    
      <div class="input-field">	    
       <?php echo CHtml::textField('global_sender',
       getOptionA('global_sender')
       ,array('class'=>"validate"))?>
       <label><?php echo t("Email Global Sender")?></label>
      </div>  
   
   </div> <!--col-->
   </div> <!--row-->
  
   

   
   
   <!--<h5><?php echo t("iOS Settings")?></h5>-->
   
   <h5><?php echo t("Follow us")?></h5>
   <div class="row">
      <div class="col s6">
             <div class="input-field">	    
		       <?php echo CHtml::textField('follow_fb',
		       getOptionA('follow_fb')
		       ,array(
		         'class'=>"validate",
		         //'data-validation'=>'required'
		         ))?>
		       <label><?php echo t("Facebook")?></label>
		      </div>      
      </div>
      <div class="col s6">
             <div class="input-field">	    
		       <?php echo CHtml::textField('follow_google',
		       getOptionA('follow_google')
		       ,array(
		         'class'=>"validate",
		         //'data-validation'=>'required'
		         ))?>
		       <label><?php echo t("Google")?></label>
		      </div>    
      </div>
   </div>   
   
   <div class="row">
       <div class="col s6">
             <div class="input-field">	    
		       <?php echo CHtml::textField('follow_twitter',
		       getOptionA('follow_twitter')
		       ,array(
		         'class'=>"validate",
		         //'data-validation'=>'required'
		         ))?>
		       <label><?php echo t("Twitter")?></label>
		      </div>    
      </div>
   </div>
   
   
   <h5><?php echo t("Signup Settings")?></h5>
   
     <div class="row"> 
     <div class="col s6">
         <?php echo CHtml::checkBox('signup_verification_enabled',
	      getOptionA('signup_verification_enabled')==1?true:false
	      ,array(
	        'id'=>"signup_verification_enabled",
	        'class'=>"with-gap",
	        'value'=>1
	      ))?>
	      <label for="signup_verification_enabled"><?php echo t("Enabled Signup Verification")?></label>	
	 </div>
	 
	 <div class="col s6">
         <?php echo CHtml::checkBox('signup_needs_approval',
	      getOptionA('signup_needs_approval')==1?true:false
	      ,array(
	        'id'=>"signup_needs_approval",
	        'class'=>"with-gap",
	        'value'=>1
	      ))?>
	      <label for="signup_needs_approval"><?php echo t("Signup Needs Admin approval")?></label>	
	 </div>
	 
     </div> <!--row-->
   
    <div class="row">     
	   <div class="col s2">  
	      <p>	      
	      <?php echo CHtml::radioButton('signup_verification',
	      getOptionA('signup_verification')=="mail"?true:false
	      ,array(
	        'id'=>"signup_verification",
	        'class'=>"with-gap",
	        'value'=>"mail"
	      ))?>
	      <label for="signup_verification"><?php echo t("Use Email")?></label>
	    </p>
	   </div> <!--col-->
	   <div class="col s2">  
	      <p>	      
	      <?php echo CHtml::radioButton('signup_verification',
	      getOptionA('signup_verification')=="sms"?true:false
	      ,array(
	        'id'=>"signup_verification1",
	        'class'=>"with-gap",
	        'value'=>"sms"
	      ))?>
	      <label for="signup_verification1"><?php echo t("Use SMS")?></label>
	    </p>
	   </div> <!--col-->
   </div> <!--row-->
   
   
   <h5 class="top30"><?php echo t("Time Settings")?></h5>
   
   <div class="row">
     <div class="col s6">
     
         <div class="input-field">
	       <?php echo CHtml::dropDownList('website_timezone',
	        getOptionA('website_timezone'),
	        (array)AdminFunctions::timeZone()
	        )?>
		    <label><?php echo t("Timezone")?></label>
	    </div>
     
     </div>
   </div>
   
   <h5 class="top30"><?php echo t("Currency Settings")?></h5>
   
   <div class="row"> 
      <div class="col s6">
      
        <div class="input-field">
	       <?php echo CHtml::dropDownList('website_currency',
	        getOptionA('website_currency'),
	        (array)AdminFunctions::asList(AdminFunctions::currencyList(),'curr_id','currency_code')
	        )?>
		    <label><?php echo t("Default Currency")?></label>
	    </div>
       
      </div>
      <div class="col s6">
      
         <div class="input-field">
	       <?php echo CHtml::dropDownList('currency_position',
	        getOptionA('currency_position'),
	        (array)array(
	          'left'=>t("Left"),
	          'right'=>t("Right"),
	        )
	        )?>
		    <label><?php echo t("Currency Position")?></label>
	    </div>
        
      </div>
   </div>
   
   <div class="row">
     <div class="col s6">
     
          <div class="input-field">	    
	       <?php echo CHtml::textField('currency_decimal_places',
	       getOptionA('currency_decimal_places')
	       ,array(
	         'class'=>"validate numeric_only",	         
	         ))?>
	       <label><?php echo t("Decimal Places")?></label>
	      </div>    
     
     </div>
     <div class="col s6">
     
         <div class="input-field">	    
	       <?php echo CHtml::textField('currency_thousand_sep',
	       getOptionA('currency_thousand_sep')
	       ,array(
	         'class'=>"validate",	         
	         'maxlength'=>1
	         ))?>
	       <label><?php echo t("Thousand Separators")?></label>
	      </div>    
     
     </div>
   </div>
   
   <div class="row">
     <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::textField('currency_decimal_sep',
	       getOptionA('currency_decimal_sep')
	       ,array(
	         'class'=>"validate",	    
	         'maxlength'=>1
	         ))?>
	       <label><?php echo t("Decimal Separators")?></label>
	      </div>    
     </div>
     
     <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::checkBox('currency_space',
	       getOptionA('currency_space')==1?true:false
	       ,array(
	        'id'=>"currency_space",
	        'class'=>"with-gap",
	        'value'=>1
	      ))?>
	       <label for="currency_space"><?php echo t("Add Space between Currency and price")?></label>
	      </div>    
     </div>
     
   </div>
   
   <h5 class="top30"><?php echo t("Promo Codes")?></h5>
   
   <div class="row"> 
     <div class="col s6">
         <?php echo CHtml::checkBox('enabled_promo_codes',
	      getOptionA('enabled_promo_codes')==1?true:false
	      ,array(
	        'id'=>"enabled_promo_codes",
	        'class'=>"with-gap",
	        'value'=>1
	      ))?>
	      <label for="enabled_promo_codes"><?php echo t("Enabled Promo Codes")?></label>	
	 </div>	 
	 
	<!-- <div class="col s6">
         <?php /*echo CHtml::checkBox('display_promo_codes',
	      getOptionA('display_promo_codes')==1?true:false
	      ,array(
	        'id'=>"display_promo_codes",
	        'class'=>"with-gap",
	        'value'=>1
	      ))*/?>
	      <label for="display_promo_codes"><?php echo t("Display Promo Codes during payment")?></label>	
	 </div>-->	 
	 
	</div> <!--row-->
   
    <div class="card-action" style="margin-top:20px;">
     <button class="btn waves-effect waves-light" type="submit" name="action">
       <?php echo t("Save settings")?>
     </button>
    </div>
     
    
   </form>  
  
 </div> <!--card-content-->
</div> <!--card-->