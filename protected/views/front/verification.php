

<div class="page-content sections">
<div class="container medium"> 

   <h2 class="text-center"><?php echo t("Verifications")?></h2>
   
   <form id="frm" method="POST" onsubmit="return false;">
   <?php echo CHtml::hiddenField('action','verifySignupCode')?>
   <?php echo CHtml::hiddenField('hash',$_GET['hash'])?>
   <?php echo CHtml::hiddenField('verification_type',$verification_type)?>
   
   
   <?php if($verification_type=="mail"):?>
   <p class="text-center"><?php echo t("We have sent verification code in your email")?></p>
   <?php else :?>
   <p class="text-center"><?php echo t("We have sent verification code in your mobile number")?></p>
   <?php endif;?>
   
   <div class="row">
      <div class="col s6 offset-s3">
      
         <div class="input-field">
			      <?php echo CHtml::textField('verification_code',
			      ''
			      ,array('class'=>"validate",
			      'data-validation'=>"required",
			      'maxlength'=>10
			      ))?>
			      <label for="verification_code"><?php echo t("Enter Verification Code")?></label>
		   </div>
      
		   <p><?php echo t("Did not receive your verification code")?>?
		   <a class="resend-code"  href="#no" ><?php echo t("Click here")?></a> <?php echo t("to resend your code")?>
		   </p>		   
		   
      </div> <!--col-->
   </div> <!--row-->
   
      
   
   
   <div class="row">
   <div class="col s6 offset-s3">
     <div class="card-action" >
     <button class="btn waves-effect waves-light" type="submit" name="action">
       <?php echo t("Submit")?>
     </button>
     </div>
   </div>   
   </div>
   
   </form>
   
   
   
</div> <!--container-->   
</div> <!--sections-->