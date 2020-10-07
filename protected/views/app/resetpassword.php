

<div class="container">
  <div class="login-wrap rounded">
  <img src="<?php echo FrontFunctions::getLogoURL() ?>"  class="logo" alt="" class="" height="50">
     
    <form id="frm" class="frm rounded3" method="POST" onsubmit="return false;">
    <div class="inner">
    <?php echo CHtml::hiddenField('action','resetPassword')?>
    <?php echo CHtml::hiddenField('hash',$hash)?>
    
    <p class="text-center" style="margin-bottom:15px;"><?php echo t("Se ha enviado un código de verificación a su email")?></p>
    <div>
    <?php 
    echo CHtml::textField('verification_code','',array(
      'placeholder'=>Driver::t("Ingrese el código de verificación"),
      'class'=>"lightblue-fields rounded",
      'required'=>true,
      'maxlength'=>5
    ));
    ?>
    </div>
    
    <div class="top20">
    <?php 
    echo CHtml::passwordField('password','',array(
      'placeholder'=>Driver::t("Password"),
      'class'=>"lightblue-fields rounded",
      'required'=>true
    )); 
    ?>
    </div>
    
    <div class="top20">
    <?php 
    echo CHtml::passwordField('cpassword','',array(
      'placeholder'=>Driver::t("Confirmar Password"),
      'class'=>"lightblue-fields rounded",
      'required'=>true
    )); 
    ?>
    </div>
      
    <div class="top20">
    <button class="yellow-button large rounded3 relative">
       <?php echo Driver::t("Resetear Password")?> <i class="ion-ios-arrow-thin-right"></i>
    </button>
    </div>
    </form>
    
    </div> <!--inner-->
    
     <hr/>
    <a href="<?php echo Yii::app()->createUrl('app/login')?>" class="text-white" style="color:#fff;">
    <i class="ion-ios-arrow-thin-left"></i> <?php echo t("Atrás")?>
    </a>
    
    
    
  </div> <!--login-wrap-->
</div> <!--container-->