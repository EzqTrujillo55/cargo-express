

<div class="card">
 <div class="card-content">
   
 
  <div class="row">
    <div class="col s6">
      <h5><?php echo t("Promo Codes")?></h5>
    </div>
    <div class="col s6 right-align">
     <a href="<?php echo Yii::app()->createUrl('/admin/promocode-new')?>" class="btn-floating btn-small waves-effect waves-light">
        <i class="material-icons">add</i>
      </a>
      
      <a href="<?php echo Yii::app()->createUrl('/admin/promocode')?>" class="btn-floating btn-small waves-effect waves-light">
        <i class="material-icons">replay</i>
      </a>
    </div>
  </div> <!--row-->
 
  
   <form id="frm" method="POST" onsubmit="return false;">
   <?php echo CHtml::hiddenField('action','addPromoCode')?>
   <?php if (isset($data['promo_code_id'])):?>
   <?php echo CHtml::hiddenField('id',$data['promo_code_id'])?>
   <?php endif;?>   
   
   <?php if (isset($_GET['msg'])):?>
   <?php echo CHtml::hiddenField('msg',$_GET['msg'])?>
   <?php endif;?>   
   
   <div class="row">
	    <div class="col s6">   
		   <div class="input-field">
		      <?php echo CHtml::textField('promo_code_name',
		      isset($data['promo_code_name'])?$data['promo_code_name']:''
		      ,
		       array(
		        'class'=>"validate",
		        'data-validation'=>"required"
		      ))?>
		      <label><?php echo t("Promo Code Name")?></label>
		   </div>   
	    </div>
	    
	    <div class="col s6">   
		   <div class="input-field">
		      <?php echo CHtml::textField('discount',
		      isset($data['discount'])?$data['discount']:''
		      ,
		       array(
		        'class'=>"validate numeric_only",
		        'data-validation'=>"required"
		      ))?>
		      <label><?php echo t("Discount")?></label>
		   </div>   
	    </div>
	    
    </div> <!--row-->
    
    <div class="row">
	    <div class="col s6">   
	       <div class="input-field">
	       <?php echo CHtml::dropDownList('discount_type',
	        isset($data['discount_type'])?$data['discount_type']:'',
	        array(
	          'fixed'=>t("fixed"),
	          'percentage'=>t("percentage")
	        ))?>
		    <label><?php echo t("Type")?></label>
	       </div>
	    </div> <!--col-->
	    <div class="col s6">   
	    
	       <div class="input-field">
		      <?php echo CHtml::textField('expiration',
		      isset($data['expiration'])?$data['expiration']:''
		      ,
		       array(
		        'class'=>"validate datepicker",
		        'data-validation'=>"required"
		      ))?>
		      <label><?php echo t("Expiration")?></label>
		   </div>      
	    
	    </div> <!--col-->
	</div> <!--row-->   
   
     
    <div class="row">
    <div class="col s12">
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