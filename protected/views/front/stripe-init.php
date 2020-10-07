

<div class="page-content sections">
<div class="container medium"> 

<div class="card">
 <div class="card-content">
 
   
   <h2><?php echo t("Credit Card Information")?></h2>
     
   <form id="frm-stripe" method="POST" onsubmit="return false;">
   <?php echo CHtml::hiddenField('action','stripePayment')?>   
   <?php echo CHtml::hiddenField('publish_key',$publish_key)?>   
   <?php echo CHtml::hiddenField('hash',$hash)?>  
      
   <div class="row">
     <div class="col s16">
       <b><?php echo t("Amount to pay")?></b> : <?php echo prettyPrice($price)?>
     </div>
   </div>
   
   <div class="row"> 
      <div class="col s6">
      
       <div class="input-field">
	      <?php echo CHtml::textField('card_number',
	      ''      
	      ,
	       array(
	        'class'=>"validate",
	        'data-validation'=>"required",
	        'maxlength'=>16
	      ))?>
	      <label><?php echo t("Card Number")?></label>
	   </div>   
      
      </div> <!--col-->
      <div class="col s6">      
      
         <div class="input-field">
	      <?php echo CHtml::textField('cvc',
	      ''      
	      ,
	       array(
	        'class'=>"validate",
	        'data-validation'=>"required",
	        'maxlength'=>3
	      ))?>
	      <label><?php echo t("CVC")?></label>
	   </div>   
      
      </div> <!--col-->
   </div> <!--row-->
   
   <div class="row"> 
      <div class="col s6">
      
       <div class="input-field">
	     <?php 
	     echo CHtml::dropDownList('expiration_year','',
	     FrontFunctions::YearList()
	     ,array(
	       'class'=>"select-material"
	     ));
	     ?>
         <label><?php echo t("Expiration Year")?></label>
	   </div>   
      
      </div> <!--col-->
      <div class="col s6">      
      
        <div class="input-field">
	     <?php 	     
	     echo CHtml::dropDownList('expiration_month','',
	     FrontFunctions::MonthList()
	     ,array(
	       'class'=>"select-material"
	     ));
	     ?>
         <label><?php echo t("Expiration Month")?></label>
	   </div>   
      
      </div> <!--col-->
   </div> <!--row-->
   
   
    <div class="card-action" style="margin-top:20px;">
	    <div class="row">
		    <div class="col s6">
		     <button class="btn waves-effect waves-light" type="submit" name="action">
		       <?php echo t("Pay Now")?>
		     </button>
		     </div>
		     <div class="col s6">
		       <a href="<?php echo Yii::app()->createUrl('front/payment',array(
		        'hash'=>$hash,
		        //'lang'=>Yii::app()->language
		       ))?>">
		         <i class="ion-ios-arrow-thin-left"></i> <?php echo t("Go back")?></a>
		     </div>
	     </div>
     </div> 
   
   </form>
 
 </div> <!--card-content-->
</div> <!--card-->

 </div>
</div> <!--sections-->
