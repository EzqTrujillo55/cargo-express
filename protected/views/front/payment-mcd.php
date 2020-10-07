<?php
/*dump($price);
dump($plan_details);
dump($credentials);*/

$amount_to_pay=number_format($price,2,'.','');
//dump($amount_to_pay);

require_once 'mercadopago/mercadopago.php';

try {
	
	$mp = new MP($credentials['mcd_client_id'], $credentials['mcd_secret']);	
	$reference=AdminFunctions::generateNumericCode(5);
	//dump($reference);
		
    $preference_data = array(
		"items" => array(
			array(
			"title" => $memo,
			"currency_id" => FrontFunctions::getCurrenyCode(true),
			"category_id" => "services",
			"quantity" => 1,
			"unit_price" =>(float)$amount_to_pay
			)
		  ),
		"back_urls" => array(
		"success" =>FrontFunctions::websiteUrl()."/mcd/?status=success",
		"failure" =>FrontFunctions::websiteUrl()."/mcd/?status=failure",
		"pending" =>FrontFunctions::websiteUrl()."/mcd/?status=pending",
	    ),
	    "auto_return"=>"approved",
	    "external_reference" => $reference."-".$hash,
    );   		
    
    //dump($preference_data);
    
    $preference = $mp->create_preference($preference_data);   
    
    //dump($preference);
	
} catch (Exception $e) {			
	$error=$e->getMessage();
}
?>


<div class="page-content sections">
<div class="container medium"> 

<div class="card">
 <div class="card-content">
 
 <h2><?php echo t("Pay using Mercapago")?></h2>
 
  <?php if(!empty($error)):?>
    <p class="text-danger"><?php echo t("ERROR")?>: <?php echo $error;?></p>
  <?php else :?>
  <div class="card-action" style="margin-top:20px;">
  
	    <div class="row">
	     <div class="col s16">
	       <b><?php echo t("Amount to pay")?></b> : <?php echo prettyPrice($price)?>
	     </div>
	    </div>
	    
	    <div class="row">
		    <div class="col s6">
		    
		    <?php if ($credentials['mcd_mode']=="sandbox"):?>
		    <a href="<?php echo $preference["response"]["sandbox_init_point"];; ?>"  name="MP-Checkout"
             class="btn waves-effect waves-light lightblue-M-Ov-ArOn" type="submit" name="action">
		       <?php echo t("Pay Now")?>
		     </a>
		    <?php else :?>
		     <a href="<?php echo $preference["response"]["init_point"]; ?>"   name="MP-Checkout"
             class="btn waves-effect waves-light lightblue-M-Ov-ArOn" type="submit" name="action">
		       <?php echo t("Pay Now")?>
		     </a>
		     <?php endif;?>
		     
		     <script type="text/javascript" src="http://mp-tools.mlstatic.com/buttons/render.js"></script>
		     
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
   <?php endif;?>  
 
 </div> <!--card-content-->
</div> <!--card-->

 </div>
</div> <!--sections-->