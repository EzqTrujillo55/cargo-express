
<div class="container">
   <div class="login-wrapper">
    
   <img src="<?php echo Yii::app()->baseUrl.'/assets/images/logo@2x.png'; ?>">
   
     <div class="grey-box rounded3">
         <div class="inner">

         <div id="admin-login-wrap">
         <h5 class="center-align">
         <?php          
         $company_name=getOptionA("company_name");
         if (empty($company_name)){
         	$company_name='Kartero Back Office';
         }
         echo $company_name;
         ?>
         </h5>        
         
         <form id="frm" method="POST" onsubmit="return false;">
         <?php echo CHtml::hiddenField('action','login')?> 
          <div class="input-field">
            <i class="material-icons prefix">account_circle</i>
            <?php echo CHtml::textField('username','',array('class'=>"validate",'data-validation'=>"required"))?>
            <label for="icon_prefix"><?php echo t("Username")?></label>
          </div>  
          
          <div class="input-field">
            <i class="material-icons prefix">lock</i>
            <?php echo CHtml::passwordField('password','',array('class'=>"validate",'data-validation'=>"required"))?>
            <label for="icon_prefix"><?php echo t("Password")?></label>
          </div>  
          
          
          <div class="center-align">
          <button class="btn waves-effect waves-light" type="submit" name="action">
          <?php echo t("LOG IN")?>    
          </button>
          </div>
          
          </form>
          
           <div class="top20" style="text-align: right; ">
            <a href="#no" class="admin_forgot_pass"><?php echo t("Forgot password")?>?</a>
           </div>
          
          </div><!-- adminloginwrap-->
          
          <form id="frm-forgotpass" method="POST" onsubmit="return false;">
          <?php echo CHtml::hiddenField('action','adminForgotPassword')?>
          
          <h5 class="center-align"><?php echo t("Forgot password")?></h5>
          
          <div class="input-field">
            <i class="material-icons prefix">account_circle</i>
            <?php echo CHtml::textField('username','',array('class'=>"validate",'data-validation'=>"required"))?>
            <label for="icon_prefix"><?php echo t("Username")?></label>
          </div>  
          
           <div class="center-align">
          <button class="btn waves-effect waves-light" type="submit" name="action">
          <?php echo t("Submit")?>    
          </button>
          </div>
          
          <div class="top20" style="text-align: right; ">
            <a href="#no" class="admin_back_login"><?php echo t("Login")?></a>
           </div>
                   
          </form>
        
          
         </div> <!--inner-->
     </div> <!--rounded-box-->
    
   </div> <!--login-wrapper-->
</div> <!--container-->