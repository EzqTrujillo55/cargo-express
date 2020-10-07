
  <form id="frm" method="POST" onsubmit="return false;">
  <?php echo CHtml::hiddenField('action','paymentSettings')?>
  
  <div class="row">
    <div class="col s6">
         <?php echo CHtml::checkBox('paypal_enabled',
	      getOptionA('paypal_enabled')==1?true:false
	      ,array(
	        'id'=>"paypal_enabled",
	        'class'=>"with-gap",
	        'value'=>1
	      ))?>
	      <label for="paypal_enabled"><?php echo t("Enabled Paypal")?></label>
     </div>
     
      <div class="col s6">
         <?php echo CHtml::radioButton('paypal_mode',
	      getOptionA('paypal_mode')=="sandbox"?true:false
	      ,array(
	        'id'=>"paypal_mode",
	        'class'=>"with-gap",
	        'value'=>"sandbox"
	      ))?>
	      <label for="paypal_mode"><?php echo t("Sanbox")?></label>
	      
	       <?php echo CHtml::radioButton('paypal_mode',
	      getOptionA('paypal_mode')=="live"?true:false
	      ,array(
	        'id'=>"paypal_mode1",
	        'class'=>"with-gap",
	        'value'=>"live"
	      ))?>
	      <label for="paypal_mode1"><?php echo t("Live")?></label>
     </div>
          
  </div>
  
  <h5><?php echo t("Sandbox")?></h5>
  <div class="row">
    <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::textField('paypal_sandbox_user',
	       getOptionA('paypal_sandbox_user')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("Paypal User")?></label>
	      </div>  
    </div>
    <div class="col s6">
         <div class="input-field">	    
	       <?php echo CHtml::textField('paypal_sandbox_password',
	       getOptionA('paypal_sandbox_password')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("Paypal Password")?></label>
	      </div>  
    </div>
  </div>
  
  <div class="row">
    <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::textField('paypal_sandbox_signature',
	       getOptionA('paypal_sandbox_signature')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("Paypal Signature")?></label>
	      </div>  
    </div>
  </div>   
  
  <h5><?php echo t("Live")?></h5>
    <div class="row">
    <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::textField('paypal_live_user',
	       getOptionA('paypal_live_user')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("Paypal User")?></label>
	      </div>  
    </div>
    <div class="col s6">
         <div class="input-field">	    
	       <?php echo CHtml::textField('paypal_live_password',
	       getOptionA('paypal_live_password')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("Paypal Password")?></label>
	      </div>  
    </div>
  </div>
  
  <div class="row">
    <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::textField('paypal_live_signature',
	       getOptionA('paypal_live_signature')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("Paypal Signature")?></label>
	      </div>  
    </div>
  </div>   
  
  
 <div class="card-action" style="margin-top:20px;">
     <button class="btn waves-effect waves-light" type="submit" name="action">
       <?php echo t("Save settings")?>
     </button>
  </div>  
  
  </form>