
<div class="page-content sections section-pricing">
<div class="container"> 

   <h2 class="text-center"><?php echo t("Simple Pricing")?></h2>
   <p class="text-center"><?php echo t("No commitment, no hidden charges and no complications")?>; <?php echo t("simple and transparent pricing. Your business is unique and our pricing structure is flexible. Let's get started")?>.</p>
   
   <?php if (is_array($data) && count($data)>=1):?>
   <div class="row pricing top20">
   
      <?php foreach ($data as $val):?>
      <?php        
       $price=$val['price']; $promo_price=0;
       if($val['promo_price']>0.0001){
       	  $price=$val['promo_price'];
       	  $promo_price=$val['promo_price'];
       }
      ?>
      <div class="col-md-4 border">
         <div class="box">
           <h5><?php echo $val['plan_name']?></h5>
           
           <div class="section">
           <!--<price>0.00 <span>$</span></price>-->
           <?php if($final_price=FrontFunctions::formatPricing($price)):?>
           <?php echo $final_price?>
	           <?php if ($promo_price>0):?>
	           <p><?php echo t("Before")?> <span class="promo-price"><?php echo prettyPrice($val['price'])?></span></p>
	           <?php endif;?>
           <?php else :?>
           <price>-</price>
           <?php endif;?>
           <!--<p class="uppercase"><?php echo t("PER")." ".t($val['plan_type'])?></p>-->
           <p class="uppercase"><?php echo t("Membership Limit")?> <?php echo $val['expiration']?> <?php echo t($val['plan_type'])?> </p>
           
           <?php if(!empty($val['plan_name_description'])):?>
           <p class="plan_description readmore"><?php echo $val['plan_name_description']?></p>
           <?php endif;?>
           
           </div>
                      
           <div class="section text-left">
             <ul> 
              <li>- <?php echo t("Allowed")." ".t($val['allowed_driver'])." ".t("driver")?>.</li>
              <li>- <?php echo t("Allowed")." ".t($val['allowed_task'])." ".t("Task")?>.</li>
              <?php if ( $val['with_sms']==1):?>
              <li>- <?php echo t("With SMS Features")?></li>
              <?php else :?>
              <li>- <?php echo t("NO SMS Features")?></li>
              <?php endif;?>
              
              <?php if ($val['with_broadcast']==1):?>
              <li>- <?php echo t("With Push Broadcast")?></li>
              <?php endif;?>
             </ul>
           </div>
           
           
           <?php if (isset($hash) && !empty($hash)):?>
                      
	           <div class="action">
	           <a href="<?php echo Yii::app()->createUrl('front/payment',array(
	             'hash'=>$hash,
	             'plan_id'=>$val['plan_id'],
	           ))?>"
	            class="brown-button large relative top30 rounded">
			   <?php echo t("START FREE TRIAL")?>
			   <i class="ion-ios-arrow-thin-right"></i>
			   </a>
			   </div>
		   
           <?php else :?>
           
	           <?php 
	           $params=array(
	             'plan_id'=>$val['plan_id']
	           );
	           if(!empty($email)){
	           	  $params=array(
	                'plan_id'=>$val['plan_id'],
	                'email'=>$email
	             );
	           }
	           ?>
	           
	           <div class="action">
	           <a href="<?php echo Yii::app()->createUrl('front/signup',$params)?>"
	            class="brown-button large relative top30 rounded">
			   <?php echo t("START FREE TRIAL")?>
			   <i class="ion-ios-arrow-thin-right"></i>
			   </a>
			   </div>
		   <?php endif;?>
           
         </div> <!--box-->
      </div> <!--col-->
      <?php endforeach;?>
           
   </div> <!--row-->   
   <?php endif;?>

</div> <!--container-->
</div> <!--sections-->