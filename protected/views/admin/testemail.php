
<div class="card">
 <div class="card-content">
 
 <div class="row">
    <div class="col s6">
      <h5><?php echo t("Send Test Email")?></h5>
    </div>    
  </div> <!--row-->
 
 <form id="frm" method="POST" onsubmit="return false;">
 <?php echo CHtml::hiddenField('action','sendTestEmail')?>
 
  <div class="row">
     <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::textField('email_address',
	       ''
	       ,array('class'=>"validate",
	       'data-validation'=>"required"
	       ))?>
	       <label><?php echo t("Email address")?></label>
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