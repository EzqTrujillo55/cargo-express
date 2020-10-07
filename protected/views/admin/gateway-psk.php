
<form id="frm-6" method="POST" onsubmit="return false;">
  <?php echo CHtml::hiddenField('action','PaystackSettings')?>
  
  
  <div class="row">
    <div class="col s6">
         <?php echo CHtml::checkBox('psk_enabled',
	      getOptionA('psk_enabled')==1?true:false
	      ,array(
	        'id'=>"psk_enabled",
	        'class'=>"with-gap",
	        'value'=>1
	      ))?>
	      <label for="psk_enabled"><?php echo t("Enabled")?></label>
     </div>
     
      <div class="col s6">
         <?php echo CHtml::radioButton('psk_mode',
	      getOptionA('psk_mode')=="sandbox"?true:false
	      ,array(
	        'id'=>"psk_mode",
	        'class'=>"with-gap",
	        'value'=>"sandbox"
	      ))?>
	      <label for="psk_mode"><?php echo t("Sanbox")?></label>
	      
	       <?php echo CHtml::radioButton('psk_mode',
	      getOptionA('psk_mode')=="live"?true:false
	      ,array(
	        'id'=>"psk_mode1",
	        'class'=>"with-gap",
	        'value'=>"live"
	      ))?>
	      <label for="psk_mode1"><?php echo t("Live")?></label>
     </div>         
  </div>  
  
  
  <div class="row">
    <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::textField('psk_sandbox_secret_key',
	       getOptionA('psk_sandbox_secret_key')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("Sandbox Secret Key")?></label>
	      </div>  
    </div>
    <div class="col s6">
         <div class="input-field">	    
	       <?php echo CHtml::textField('psk_live_secret_key',
	       getOptionA('psk_live_secret_key')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("Live Transaction Key")?></label>
	      </div>  
    </div>
  </div>    
  
  
  <h6><?php echo t("Your Webhook URL")?>:</h6>
  <p><?php echo websiteUrl()."/paystack_webhook"?></p>
  

 <div class="card-action" style="margin-top:20px;">
     <button class="btn waves-effect waves-light" type="submit" name="action">
       <?php echo t("Save settings")?>
     </button>
  </div>  
  
  </form>  