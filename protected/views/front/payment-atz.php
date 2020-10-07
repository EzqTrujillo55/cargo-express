<?php
$amount_to_pay=number_format($price,2,'.','');
?>

<div class="page-content sections">
<div class="container medium"> 

<div class="card">
 <div class="card-content">
 
 <form id="frm" method="POST" onsubmit="return false;">
 <?php echo CHtml::hiddenField('action','atz')?>    
 <?php echo CHtml::hiddenField('hash',$hash)?>    
  

 <h2><?php echo t("Pay using Authorize.net")?></h2>
 
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
      
  </div> <!-- row-->
  
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
   
 <div class="row"> 
      <div class="col s6">
      
       <div class="input-field">
	     <?php 
	     echo CHtml::textField('first_name',''	     
	     ,array(
	       'class'=>"validate",
	       'data-validation'=>"required",
	     ));
	     ?>
         <label><?php echo t("First name")?></label>
	   </div>   
      
      </div> <!--col-->
      <div class="col s6">      
      
        <div class="input-field">
	     <?php 	     
	     echo CHtml::textField('last_name',''	     
	     ,array(
	       'class'=>"validate",
	        'data-validation'=>"required",
	     ));
	     ?>
         <label><?php echo t("Last name")?></label>
	   </div>   
      
      </div> <!--col-->
   </div> <!--row-->     
   
   <div class="row">  
      <div class="col s6">
      
       <div class="input-field">
	     <?php 
	     echo CHtml::textField('address',''	     
	     ,array(
	       'class'=>"validate",
	        'data-validation'=>"required",
	     ));
	     ?>
         <label><?php echo t("Address")?></label>
	   </div>   
      
      </div> <!--col-->
      <div class="col s6">      
      
        <div class="input-field">
	     <?php 	     
	     echo CHtml::textField('city',''	     
	     ,array(
	       'class'=>"validate",
	        'data-validation'=>"required",
	     ));
	     ?>
         <label><?php echo t("City")?></label>
	   </div>   
      
      </div> <!--col-->
   </div> <!--row-->        
   

   <div class="row">  
      <div class="col s6">
      
       <div class="input-field">
	     <?php 
	     echo CHtml::textField('state',''	     
	     ,array(
	       'class'=>"validate",
	        'data-validation'=>"required",
	     ));
	     ?>
         <label><?php echo t("State")?></label>
	   </div>   
      
      </div> <!--col-->
      <div class="col s6">      
      
        <div class="input-field">
	     <?php 	     
	     echo CHtml::textField('zipcode',''	     
	     ,array(
	       'class'=>"validate",
	        'data-validation'=>"required",
	     ));
	     ?>
         <label><?php echo t("Zip Code")?></label>
	   </div>   
      
      </div> <!--col-->
   </div> <!--row-->           
   
   <div class="row">  
     <div class="col s6">
      <?php 
	     echo CHtml::dropDownList('country',$default_country,
	     AdminFunctions::getCountryList()
	     ,array(
	       'class'=>"select-material",
	       'data-validation'=>"required",
	     ));
	     ?>
         <!--<label><?php echo t("Country")?></label>-->
     </div>
   </div>
 
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
		        'lang'=>Yii::app()->language
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