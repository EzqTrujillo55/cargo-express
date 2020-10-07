

<div class="page-content sections">
<div class="container medium"> 

   <h2 class="text-center"><?php echo t("Thank You")?>!</h2>
      
   <?php if(isset($renew) && $renew==1):?>
   
        <p class="top40 text-center">	   
	   </p>
	   <p class="text-center">
	   <?php echo t("You plan successfully renewed")?>
	   </p>
	   <p class="text-center">
	   <?php echo t("Please re login again for the renewal to take effect")?>
	   </p>
   
   <?php else :?>
   
	   <?php if ($needs_approval==""):?>  
	   <p class="top40 text-center">
	   <?php echo t("Thank you for signing up")?>. 
	   </p>
	   <p class="text-center">
	   <?php echo t("Your account is now ready you can login")?> 
	   <a href="<?php echo Yii::app()->createUrl('/app/login')?>"><?php echo t("Here")?></a>
	   </p>
	   
	   <?php else :?>
	   
	   <p class="top40 text-center">
	   <?php echo t("Thank you for signing up")?>. 
	   </p>
	   <p class="text-center">	   
	   <?php echo t("Your account is subject for approval you will be notified")?> 
	   <?php echo t("once your account is approved")?>
	   .
	   </p>
	   
	   <?php endif;?>
   
   <?php endif;?>
   
</div> <!--container-->   
</div> <!--sections-->