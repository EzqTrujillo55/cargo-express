
<form id="frm-2" method="POST" onsubmit="return false;">
  <?php echo CHtml::hiddenField('action','stripeSettings')?>
  
  
  <div class="row">
    <div class="col s6">
         <?php echo CHtml::checkBox('stripe_enabled',
	      getOptionA('stripe_enabled')==1?true:false
	      ,array(
	        'id'=>"stripe_enabled",
	        'class'=>"with-gap",
	        'value'=>1
	      ))?>
	      <label for="stripe_enabled"><?php echo t("Enabled Stripe")?></label>
     </div>
     
      <div class="col s6">
         <?php echo CHtml::radioButton('stripe_mode',
	      getOptionA('stripe_mode')=="sandbox"?true:false
	      ,array(
	        'id'=>"stripe_mode",
	        'class'=>"with-gap",
	        'value'=>"sandbox"
	      ))?>
	      <label for="stripe_mode"><?php echo t("Sanbox")?></label>
	      
	       <?php echo CHtml::radioButton('stripe_mode',
	      getOptionA('stripe_mode')=="live"?true:false
	      ,array(
	        'id'=>"stripe_mode1",
	        'class'=>"with-gap",
	        'value'=>"live"
	      ))?>
	      <label for="stripe_mode1"><?php echo t("Live")?></label>
     </div>         
  </div>  
  
  <h5><?php echo t("Sandbox")?></h5>
  <div class="row">
    <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::textField('stripe_sandbox_secret_key',
	       getOptionA('stripe_sandbox_secret_key')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("Test Secret key")?></label>
	      </div>  
    </div>
    <div class="col s6">
         <div class="input-field">	    
	       <?php echo CHtml::textField('stripe_sandbox_publish_key',
	       getOptionA('stripe_sandbox_publish_key')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("Test Publishable Key")?></label>
	      </div>  
    </div>
  </div>  
  

  <h5><?php echo t("Live")?></h5>
  <div class="row">
    <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::textField('stripe_live_secret_key',
	       getOptionA('stripe_live_secret_key')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("Test Secret key")?></label>
	      </div>  
    </div>
    <div class="col s6">
         <div class="input-field">	    
	       <?php echo CHtml::textField('stripe_live_publish_key',
	       getOptionA('stripe_live_publish_key')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("Test Publishable Key")?></label>
	      </div>  
    </div>
  </div>    
  

 <div class="card-action" style="margin-top:20px;">
     <button class="btn waves-effect waves-light" type="submit" name="action">
       <?php echo t("Save settings")?>
     </button>
  </div>  
  
  </form>  