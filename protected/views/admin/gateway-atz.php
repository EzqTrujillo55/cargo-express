
<form id="frm-5" method="POST" onsubmit="return false;">
  <?php echo CHtml::hiddenField('action','AuthorizeSettings')?>
  
  
  <div class="row">
    <div class="col s6">
         <?php echo CHtml::checkBox('atz_enabled',
	      getOptionA('atz_enabled')==1?true:false
	      ,array(
	        'id'=>"atz_enabled",
	        'class'=>"with-gap",
	        'value'=>1
	      ))?>
	      <label for="atz_enabled"><?php echo t("Enabled")?></label>
     </div>
     
      <div class="col s6">
         <?php echo CHtml::radioButton('atz_mode',
	      getOptionA('atz_mode')=="sandbox"?true:false
	      ,array(
	        'id'=>"atz_mode",
	        'class'=>"with-gap",
	        'value'=>"sandbox"
	      ))?>
	      <label for="atz_mode"><?php echo t("Sanbox")?></label>
	      
	       <?php echo CHtml::radioButton('atz_mode',
	      getOptionA('atz_mode')=="live"?true:false
	      ,array(
	        'id'=>"atz_mode1",
	        'class'=>"with-gap",
	        'value'=>"live"
	      ))?>
	      <label for="atz_mode1"><?php echo t("Live")?></label>
     </div>         
  </div>  
  
  
  <div class="row">
    <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::textField('atz_login_id',
	       getOptionA('atz_login_id')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("API Login ID")?></label>
	      </div>  
    </div>
    <div class="col s6">
         <div class="input-field">	    
	       <?php echo CHtml::textField('atz_transaction_key',
	       getOptionA('atz_transaction_key')
	       ,array('class'=>"validate"))?>
	       <label><?php echo t("Transaction Key")?></label>
	      </div>  
    </div>
  </div>    
  

 <div class="card-action" style="margin-top:20px;">
     <button class="btn waves-effect waves-light" type="submit" name="action">
       <?php echo t("Save settings")?>
     </button>
  </div>  
  
  </form>  