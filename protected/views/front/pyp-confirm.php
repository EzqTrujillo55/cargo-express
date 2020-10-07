

<div class="page-content sections">
<div class="container medium"> 

<div class="card">
 <div class="card-content">
 
  <h2><?php echo t("Confirm payment")?></h2>
  
  <form id="frm" method="POST" onsubmit="return false;">
   <?php echo CHtml::hiddenField('action','paypalExpressCheckout')?> 
   <?php echo CHtml::hiddenField('token',$res_paypal['TOKEN'])?>    
   <?php echo CHtml::hiddenField('payerid',$res_paypal['PAYERID'])?>  
   <?php echo CHtml::hiddenField('amt',$res_paypal['AMT'])?>  
   <?php echo CHtml::hiddenField('hash',$_GET['hash'])?>    
      
   <div class="row">  
     <div class="col s3 ">
       <b><?php echo t("Plan name")?>:</b>          
     </div> <!--col-->
     <div class="col s6 ">
       <?php echo $plan_details['plan_name']?>    
     </div> <!--col-->
   </div> <!--row-->
   
   <div class="row">  
     <div class="col s3 ">
       <b><?php echo t("Plan Description")?>:</b>          
     </div> <!--col-->
     <div class="col s6 ">
       <?php echo $plan_details['plan_name_description']?>    
     </div> <!--col-->
   </div> <!--row-->
   
    <div class="row">  
     <div class="col s3 ">
       <b><?php echo t("Price")?>:</b>          
     </div> <!--col-->
     <div class="col s6 ">
       <?php if ($plan_details['promo_price']>0):?>
          <span class="promo-price"><?php echo prettyPrice($plan_details['price']) ?></span>
          <span><?php echo prettyPrice($plan_details['promo_price']) ?></span>
          <?php $total_price=$plan_details['promo_price'];?>
       <?php else :?>
          <span><?php echo prettyPrice($plan_details['price']) ?></span> 
          <?php $total_price=$plan_details['price'];?>
       <?php endif;?>
     </div> <!--col-->
   </div> <!--row-->
   
   <?php if ($enabled_promo_codes==1 && is_array($apply_promo) && count($apply_promo)>=1):?>
   <?php $total_price=$total_price-$apply_promo['discount_amount'];?>
   <div class="row">  
    <div class="col s3 ">
       <b><?php echo t("Less")." ".t("Promo Code");
       if ($apply_promo['discount_type']=="percentage"){
       	   echo " ".$apply_promo['discount'] . "%";
       }
       ?> :</b>          
     </div> <!--col-->
     <div class="col s6 ">
       (<?php echo prettyPrice($apply_promo['discount_amount'])?>)        
     </div> <!--col-->
   </div>
   <?php endif;?>
   
    <div class="row">  
     <div class="col s3 ">
       <b><?php echo t("Total")?>:</b>          
     </div> <!--col-->
     <div class="col s6 ">
       <?php echo prettyPrice($total_price)?>    
     </div> <!--col-->
   </div> <!--row-->
   
   <div class="row">
     <div class="col s12">
     <p class="uppercase">
       <b><?php echo t("Membership Limit")?> 
       <?php echo $plan_details['expiration']?> <?php echo t($plan_details['plan_type'])?> 
       </b>
     </p>
     </div>
   </div>
   
   <div class="card-action" style="margin-top:20px;">
   <div class="row">
   
     <div class="col m6">
     <button class="btn waves-effect waves-light" type="submit" name="action">
       <?php echo t("Confirm & Pay Now!")?>
     </button>
     </div>
     
     <div class="col m6">
              <a href="<?php echo Yii::app()->createUrl('front/payment',array(
		        'hash'=>$hash,
		        'lang'=>Yii::app()->language
		       ))?>">
		         <i class="ion-ios-arrow-thin-left"></i> <?php echo t("Go back")?></a>
     </div>
     
   </div>  
   </div> <!--card action-->
   
   </form>   
 
</div>  <!--card-content-->
</div> 
    
</div> <!--container-->   
</div> <!--sections-->