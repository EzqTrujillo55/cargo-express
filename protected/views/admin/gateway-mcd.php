
<form id="frm-3" method="POST" onsubmit="return false;">
  <?php echo CHtml::hiddenField('action','MercadopagoSettings')?>
  
  
  <div class="row">
    <div class="col s6">
         <?php echo CHtml::checkBox('mcd_enabled',
	      getOptionA('mcd_enabled')==1?true:false
	      ,array(
	        'id'=>"mcd_enabled",
	        'class'=>"with-gap",
	        'value'=>1
	      ))?>
	      <label for="mcd_enabled"><?php echo t("Enabled Mercadopago")?></label>
     </div>
     
      <div class="col s6">
         <?php echo CHtml::radioButton('mcd_mode',
	      getOptionA('mcd_mode')=="sandbox"?true:false
	      ,array(
	        'id'=>"mcd_mode",
	        'class'=>"with-gap",
	        'value'=>"sandbox"
	      ))?>
	      <label for="mcd_mode"><?php echo t("Sanbox")?></label>
	      
	       <?php echo CHtml::radioButton('mcd_mode',
	      getOptionA('mcd_mode')=="live"?true:false
	      ,array(
	        'id'=>"mcd_mode1",
	        'class'=>"with-gap",
	        'value'=>"live"
	      ))?>
	      <label for="mcd_mode1"><?php echo t("Live")?></label>
     </div>         
  </div>  
  
  
  <div class="row">
    <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::textField('mcd_client_id',
	       getOptionA('mcd_client_id')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("Client ID")?></label>
	      </div>  
    </div>
    <div class="col s6">
         <div class="input-field">	    
	       <?php echo CHtml::textField('mcd_secret',
	       getOptionA('mcd_secret')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("Client Secret")?></label>
	      </div>  
    </div>
  </div>    
  

 <div class="card-action" style="margin-top:20px;">
     <button class="btn waves-effect waves-light" type="submit" name="action">
       <?php echo t("Save settings")?>
     </button>
  </div>  
  
  </form>  