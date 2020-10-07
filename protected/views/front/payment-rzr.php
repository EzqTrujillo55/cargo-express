<?php
//dump($credentials);
//dump($price);
$amount_to_pay=str_replace(",",'',$price)*100;
//dump($amount_to_pay);
//dump($customer_details);
?>
<div class="page-content sections">
<div class="container medium"> 

<div class="card">
 <div class="card-content">
 
   
   <h2><?php echo t("RazorPay")?></h2>
   
   <div class="row">
     <div class="col s16">
       <b><?php echo t("Amount to pay")?></b> : <?php echo prettyPrice($price)?>
     </div>
   </div>
   
   <form action="<?php echo Yii::app()->createUrl('/front/verifyPaymentRzr',array('hash'=>$hash))?>" method="POST">
   <script
    src="https://checkout.razorpay.com/v1/checkout.js"
    data-key="<?php echo $credentials['rzr_key_id']?>"
    data-amount="<?php echo $amount_to_pay;?>"
    data-buttontext="<?php echo t("Pay Now")?>"
    data-name="<?php echo getOptionA('company_name')?>"
    data-description="<?php echo $memo?>"
    data-image="<?php echo FrontFunctions::getLogoURL();?>"
    data-prefill.name="<?php echo $customer_details['first_name']." ".$customer_details['last_name']?>"
    data-prefill.email="<?php echo $customer_details['email_address']?>"
    data-prefill.contact="<?php echo $customer_details['mobile_number']?>"  
    data-theme.color="#F37254"></script>
   <input type="hidden" value="Hidden Element" name="hidden">
   </form>
   
   
    <div class="card-action" style="margin-top:20px;">
	    <div class="row">
		    <div class="col s6">
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
      
 
 </div> <!--card-content-->
</div> <!--card-->

 </div>
</div> <!--sections-->