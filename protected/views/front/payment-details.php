

<div class="page-content sections">
<div class="container medium"> 

<div class="card">
 <div class="card-content">

   <h2><?php echo t("Payment")?></h2>
     
   <form id="frm" method="POST" onsubmit="return false;">
   <?php echo CHtml::hiddenField('action','paymentOption')?>   
   <?php echo CHtml::hiddenField('token',$data['token'])?>
   <?php 
   if(isset($_GET['plan_id'])){
      echo CHtml::hiddenField('plan_id',$_GET['plan_id']);
   }
   ?>
      
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
       <?php echo nl2br($plan_details['plan_name_description'])?>    
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
       <a href="#no" class="remove_promo_code"><?php echo t("remove")?></a>
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
      
   <?php if(is_array($payment_options) && count($payment_options)>=1):?>
   <ul class="collection with-header">
        <li class="collection-header"><h5><?php echo t("Choose payment option")?></h5></li>        
        <?php foreach ($payment_options as $p_key=>$p_options):?>
        <li class="collection-item">
            <input name="payment_provider" value="<?php echo $p_key?>" type="radio" id="<?php echo $p_key?>" />
            <label for="<?php echo $p_key?>"><?php echo $p_options?></label>
        </li>              
        <?php endforeach;?>
   </ul>
   <?php endif;?>
   
   <!--PROMO CODE-->   
   <?php   
   if ($enabled_promo_codes==1){
   	   if (is_array($apply_promo) && count($apply_promo)>=1){
   	   	  $enabled_promo_codes=2;
   	   }
   }      
   ?>
   <?php if($enabled_promo_codes==1):?>
   <div class="row">
       <div class="col m3">
		    <div class="input-field">
			      <?php echo CHtml::textField('promo_code',
			      '',array(
			         'class'=>"promo_code",
			         'maxlength'=>25
			      ));
			      ?>
			      <label for="first_name"><?php echo t("Promo Code")?></label>
		   </div>
	   </div> <!--col-->
	   <div class="col m3">
	     <div class="input-field"> 
	     <button type="button" class="apply_promo_code btn waves-effect light-blue lighten-2"><?php echo t("Apply")?></button>
	     </div>
	   </div>
   </div>
   <?php endif;?>
   <!--PROMO CODE-->
   
   
     <div class="card-action" style="margin-top:20px;">
     <button class="btn waves-effect waves-light" type="submit" name="action">
       <?php echo t("Pay Now")?>
     </button>
     </div> 
   
   </form>
  
</div> 
</div> 
   
</div> <!--container-->   
</div> <!--sections-->