

<div class="card">
 <div class="card-content">
   
 
  <div class="row">
    <div class="col s6">
      <h5><?php echo t("Currency")?></h5>
    </div>
    <div class="col s6 right-align">
     <a href="<?php echo Yii::app()->createUrl('/admin/currency-new')?>" class="btn-floating btn-small waves-effect waves-light">
        <i class="material-icons">add</i>
      </a>
      
      <a href="<?php echo Yii::app()->createUrl('/admin/currency')?>" class="btn-floating btn-small waves-effect waves-light">
        <i class="material-icons">replay</i>
      </a>
    </div>
  </div> <!--row-->
 
  
   <form id="frm" method="POST" onsubmit="return false;">
   <?php echo CHtml::hiddenField('action','addCurrency')?>
   <?php if (isset($data['curr_id'])):?>
   <?php echo CHtml::hiddenField('id',$data['curr_id'])?>
   <?php endif;?>   
   
   <?php if (isset($_GET['msg'])):?>
   <?php echo CHtml::hiddenField('msg',$_GET['msg'])?>
   <?php endif;?>   
   
   <div class="row">
      <div class="col s6">
      
        <div class="input-field">
	      <?php echo CHtml::textField('currency_code',
	      isset($data['currency_code'])?$data['currency_code']:''
	      ,array('class'=>"validate",
	      'data-validation'=>"required",
	      'maxlength'=>3
	      ))?>
	      <label for="currency_code"><?php echo t("Currency Code")?></label>
       </div>   
      
      </div>
      <div class="col s6">
      
        <div class="input-field">
	      <?php echo CHtml::textField('currency_symbol',
	      isset($data['currency_symbol'])?$data['currency_symbol']:''
	      ,array('class'=>"validate",
	      'data-validation'=>"required",	
	      'maxlength'=>10
	      ))?>
	      <label for="currency_symbol"><?php echo t("Currency Symbol")?></label>
       </div>   
      
      </div>
   </div>   
   
   <div class="row">
    <div class="col s6">
    <div class="input-field">
        <?php echo CHtml::dropDownList('status',
        isset($data['status'])?$data['status']:''
        ,AdminFunctions::statusList())?>
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