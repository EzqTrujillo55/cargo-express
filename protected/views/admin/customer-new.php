

<div class="card">
 <div class="card-content">
   
 
  <div class="row">
    <div class="col s6">
      <h5><?php echo t("Customer")?></h5>
    </div>
    <div class="col s6 right-align">
     <a href="<?php echo Yii::app()->createUrl('/admin/customer-new')?>" class="btn-floating btn-small waves-effect waves-light">
        <i class="material-icons">add</i>
      </a>
      
      <a href="<?php echo Yii::app()->createUrl('/admin/customer-list')?>" class="btn-floating btn-small waves-effect waves-light">
        <i class="material-icons">replay</i>
      </a>
    </div>
  </div> <!--row-->
 
  
   <form id="frm" method="POST" onsubmit="return false;">
   <?php echo CHtml::hiddenField('action','addCustomer')?>
   <?php if (isset($data['customer_id'])):?>
   <?php echo CHtml::hiddenField('id',$data['customer_id'])?>
   <?php endif;?>   
   
   <?php if (isset($_GET['msg'])):?>
   <?php echo CHtml::hiddenField('msg',$_GET['msg'])?>
   <?php endif;?>   
   
   <div class="row">
    <div class="col s6">   
	   <div class="input-field">
	      <?php echo CHtml::textField('first_name',
	      isset($data['first_name'])?$data['first_name']:''
	      ,
	       array(
	        'class'=>"validate",
	        'data-validation'=>"required"
	      ))?>
	      <label><?php echo t("First name")?></label>
	   </div>   
    </div>
   
    <div class="col s6">
	   <div class="input-field">
	      <?php echo CHtml::textField('last_name',
	      isset($data['last_name'])?$data['last_name']:''
	      ,
	       array(
	        'class'=>"validate",	   
	        'data-validation'=>"required"    
	      ))?>
	      <label><?php echo t("Last name")?></label>
	   </div>
    </div>
   </div><!-- row-->
   
   <div class="row">
    <div class="col s6">   
	   <div class="input-field">
	      <?php echo CHtml::textField('mobile_number',
	      isset($data['mobile_number'])?$data['mobile_number']:''
	      ,
	       array(
	        'class'=>"validate mobile_inputs",
	        'data-validation'=>"required",
	        'Placeholder'=>t("Mobile number")
	      ))?>
	      <!--<label for="mobile_number"><?php echo t("Mobile number")?></label>-->
	   </div>   
    </div>
   
    <div class="col s6">
	   <div class="input-field">
	      <?php echo CHtml::textField('email_address',
	      isset($data['email_address'])?$data['email_address']:''
	      ,
	       array(
	        'class'=>"validate",	       
	        'data-validation'=>"required"
	      ))?>
	      <label><?php echo t("Email address")?></label>
	   </div>
    </div>
   </div><!-- row-->   
       
   
   <div class="row">
    <div class="col s6">   
     <div class="input-field">
	      <?php echo CHtml::passwordField('password',
	      ''
	      ,
	       array(
	        'class'=>"validate",	       
	        //'data-validation'=>"required"
	      ))?>
	      <label><?php echo t("Password")?></label>
	   </div>
	</div>   
   </div>
	   
   
   <div class="row">
    <div class="col s6">   
	   <div class="input-field">
	      <?php echo CHtml::textField('company_name',
	      isset($data['company_name'])?$data['company_name']:''
	      ,
	       array(
	        'class'=>"validate",
	        //'data-validation'=>"required"
	      ))?>
	      <label><?php echo t("Company name")?></label>
	   </div>   
    </div>
   
    <div class="col s6">
	   <div class="input-field">
	      <?php echo CHtml::textField('company_address',
	      isset($data['company_address'])?$data['company_address']:''
	      ,
	       array(
	        'class'=>"validate",	       
	        //'data-validation'=>"required"
	      ))?>
	      <label><?php echo t("Company address")?></label>
	   </div>
    </div>
   </div><!-- row-->   
          
   
    <div class="row">
    <div class="col s6">   
     <div class="input-field">
	       <?php echo CHtml::dropDownList('country_code',
	        isset($data['country_code'])?$data['country_code']:'',
	        AdminFunctions::getCountryList())?>
		    <label><?php echo t("Country")?></label>
	   </div>
	</div>   
		
	<div class="col s6">   
	<div class="input-field">
	       <?php echo CHtml::dropDownList('plan_id',
	        isset($data['plan_id'])?$data['plan_id']:'',
	        (array)AdminFunctions::asList( AdminFunctions::getPlan() ,'plan_id','plan_name' )
	        )?>
		    <label><?php echo t("Plan name")?></label>
	   </div>
	</div>	
   </div>
   
   
   <div class="row">
     <div class="col s4">
     
       <div class="input-field">
	      <?php echo CHtml::textField('plan_expiration',
	      isset($data['plan_expiration'])?$data['plan_expiration']:''
	      ,
	       array(
	        'class'=>"validate datepicker",	       
	        'data-validation'=>"required"
	      ))?>
	      <label><?php echo t("Plan Expiration")?></label>
	   </div>
     
     </div>
     <div class="col s4">
     
      <?php echo CHtml::checkBox('with_sms',
      isset($data['with_sms'])?$data['with_sms']==1?true:false:''
      ,array(
       'value'=>1
      ))?>      
      <label for="with_sms"><?php echo t("With SMS Notification")?></label>
     
     </div>
     
     <div class="col s4">
       <?php echo CHtml::textField('sms_limit',
	      isset($data['sms_limit'])?$data['sms_limit']:''
	      ,
	       array(
	        'class'=>"validate numeric_only",
	        'data-validation'=>"required"
	      ))?>
	   <label><?php echo t("SMS Limit")?></label>
     </div>
     
   </div>
   
   
   <div class="row">
     <div class="col s6">
          <?php echo CHtml::checkBox('with_broadcast',
	      isset($data['with_broadcast'])?$data['with_broadcast']==1?true:false:''
	      ,array(
	       'value'=>1
	      ))?>      
	      <label for="with_broadcast"><?php echo t("With Push Broadcast")?></label>
     </div>
   </div> 
   
   <br/>
   
   
   <h5><?php echo t("Task & Driver")?></h5>
   <p><?php echo t("if you want to set to unlimited task and driver set the value to unlimited")?></p>
   <div class="top10"></div>
   
    <div class="row">
    <div class="col s6">   
	   <div class="input-field">
	      <?php echo CHtml::textField('no_allowed_driver',
	      isset($data['no_allowed_driver'])?$data['no_allowed_driver']:''
	      ,
	       array(
	        'class'=>"validate",
	        'data-validation'=>"required"
	      ))?>
	      <label><?php echo t("No allowed Driver")?></label>
	   </div>   
    </div>
   
    <div class="col s6">
	   <div class="input-field">
	      <?php echo CHtml::textField('no_allowed_task',
	      isset($data['no_allowed_task'])?$data['no_allowed_task']:''
	      ,
	       array(
	        'class'=>"validate",	       
	        'data-validation'=>"required"
	      ))?>
	      <label><?php echo t("No allowed Task")?></label>
	   </div>
    </div>
   </div><!-- row-->   
   
   
    <div class="row">
    <div class="col s12">
    <div class="input-field">
        <?php echo CHtml::dropDownList('status',
        isset($data['status'])?$data['status']:''
        ,AdminFunctions::customerStatus())?>
	    <label><?php echo t("Status")?></label>
    </div>
    </div>
    </div>
    
     <div class="card-action" style="margin-top:20px;">
     <button class="btn waves-effect waves-light" type="submit" name="action">
       <?php echo t("Submit")?>
     </button>
     </div>
         
   
   </form>
         
  
 </div>
</div> 