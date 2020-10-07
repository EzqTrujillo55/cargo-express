
<form id="frm-4" method="POST" onsubmit="return false;">
  <?php echo CHtml::hiddenField('action','RazorSettings')?>
  
  
  <div class="row">
    <div class="col s6">
         <?php echo CHtml::checkBox('rzr_enabled',
	      getOptionA('rzr_enabled')==1?true:false
	      ,array(
	        'id'=>"rzr_enabled",
	        'class'=>"with-gap",
	        'value'=>1
	      ))?>
	      <label for="rzr_enabled"><?php echo t("Enabled RAzorPay")?></label>
     </div>
     
      <div class="col s6">
         <?php echo CHtml::radioButton('rzr_mode',
	      getOptionA('rzr_mode')=="sandbox"?true:false
	      ,array(
	        'id'=>"rzr_mode",
	        'class'=>"with-gap",
	        'value'=>"sandbox"
	      ))?>
	      <label for="rzr_mode"><?php echo t("Sanbox")?></label>
	      
	       <?php echo CHtml::radioButton('rzr_mode',
	      getOptionA('rzr_mode')=="live"?true:false
	      ,array(
	        'id'=>"rzr_mode1",
	        'class'=>"with-gap",
	        'value'=>"live"
	      ))?>
	      <label for="rzr_mode1"><?php echo t("Live")?></label>
     </div>         
  </div>  
  
  
  <div class="row">
    <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::textField('rzr_key_id',
	       getOptionA('rzr_key_id')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("Key Id")?></label>
	      </div>  
    </div>
    <div class="col s6">
         <div class="input-field">	    
	       <?php echo CHtml::textField('rzr_secret',
	       getOptionA('rzr_secret')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("Key Secret")?></label>
	      </div>  
    </div>
  </div>    
  

 <div class="card-action" style="margin-top:20px;">
     <button class="btn waves-effect waves-light" type="submit" name="action">
       <?php echo t("Save settings")?>
     </button>
  </div>  
  
  </form>  