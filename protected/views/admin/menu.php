
<nav class="teal lighten-1">
<div class="nav-wrapper">
  <a href="<?php echo Yii::app()->createUrl('/admin/dashboard')?>" class="brand-logo">    
    <img src="<?php echo FrontFunctions::getLogoURL() ?>">
  </a>
  <ul id="nav-mobile" class="right hide-on-med-and-down">
  
    <li>
      <a href="<?php echo Yii::app()->createUrl('/front/index')?>" target="_blank">
      <i class="material-icons left">language</i> <?php echo t("View Site")?>
      </a>
    </li>
    
    <li>
    <a class="dropdown-button" href="#!" data-activates="reports-menu" style="width:150px;">
    <?php echo t("Reports")?>
    <i class="material-icons right">arrow_drop_down</i>
    </a>
    </li>
    
    <li>
      <a href="<?php echo Yii::app()->createUrl('/admin/profile')?>">
      <i class="material-icons left">perm_identity</i> <?php echo t("Hello")." ".AdminFunctions::getAdminUsername()?>
      </a>
    </li>    
    
    <li>
    <a class="dropdown-button" href="#!" data-activates="language-menu">
    <?php echo !empty($current_lang)?strtoupper($current_lang):'En'?>
    <i class="material-icons right">arrow_drop_down</i>
    </a>
    </li>
    
    <li>
     <a href="<?php echo Yii::app()->createUrl('/admin/logout')?>">
      <i class="material-icons">input</i>
     </a>
    </li>
  </ul>
</div>
</nav>

<ul id="reports-menu" class="dropdown-content">
  <li><a href="<?php echo Yii::app()->createUrl('admin/rpt-customer')?>"><?php echo t("Customer Signup")?></a></li>
  <li><a href="<?php echo Yii::app()->createUrl('admin/rpt-sales')?>"><?php echo t("Sales report")?></a></li>  
  <li><a href="<?php echo Yii::app()->createUrl('admin/rpt-sms')?>"><?php echo t("SMS Logs")?></a></li>
  <li><a href="<?php echo Yii::app()->createUrl('admin/rpt-email')?>"><?php echo t("Email Logs")?></a></li>
  <li><a href="<?php echo Yii::app()->createUrl('admin/rpt-push')?>"><?php echo t("Push Logs")?></a></li>  
</ul>

<?php 
if(!empty($language)){
	$language=json_decode($language,true);	
}
$action_name=Yii::app()->controller->action->id;
?> 
<ul id="language-menu" class="dropdown-content">  
<?php if(is_array($language) && count($language)>=1):?>
<?php foreach ($language as $val_lang) :?>
<li>
 <a href="<?php echo Yii::app()->getBaseUrl(true)."/admin/setlang/?lang=$val_lang&action=$action_name"?>">
 <?php echo $val_lang?>
 </a>
</li>
<?php endforeach;?>
<?php endif;?>
</ul>
    
<nav class="teal-white">
<div class="container" style="width:100%;border:0px solid red;">
<div class="nav-wrapper">    

  <a href="#" data-activates="mobile-demo" class="button-collapse"><i class="material-icons">menu</i></a>
  <?php
  function adminMenu($menu_id='nav-mobile',$class="left hide-on-med-and-down"){
  	
  	
  return  array( 
     'id' => $menu_id,
     'htmlOptions'=>array(
       'class'=>$class
     ),
     'activeCssClass'=>'active', 
     'encodeLabel'=>false,
     'items'=>array(
        array('visible'=>true,'label'=>t('Dashboard'),
        'url'=>array('/admin/dashboard'),'linkOptions'=>array()),               
        
        array('visible'=>true,'label'=>t('Plan'),
        'url'=>array('/admin/plan-list'),'linkOptions'=>array()),               
        
        array('visible'=>true,'label'=>t('Customer'),
        'url'=>array('/admin/customer-list'),'linkOptions'=>array()),               
                
        array('visible'=>true,'label'=>t('Payment'),
        'url'=>array('/admin/payment-list'),'linkOptions'=>array()),               

        array('visible'=>true,'label'=>t('Promo'),
        'url'=>array('/admin/promocode'),'linkOptions'=>array()),                      
        
        array('visible'=>true,'label'=>t('SMS'),
        'url'=>array('/admin/sms'),'linkOptions'=>array()),               

        array('visible'=>true,'label'=>t('Currency'),
        'url'=>array('/admin/currency'),'linkOptions'=>array()),               
        
        array('visible'=>true,'label'=>t('Services'),
        'url'=>array('/admin/services'),'linkOptions'=>array()),                
        
       array('visible'=>true,'label'=>t('SEO'),
        'url'=>array('/admin/seo'),'linkOptions'=>array()),                 
                              
        array('visible'=>true,'label'=>t('General Settings'),
        'url'=>array('/admin/settings'),'linkOptions'=>array()),               
        
       array('visible'=>true,'label'=>t('Mobile Settings'),
        'url'=>array('/admin/mobilesettings'),'linkOptions'=>array()), 
        
        array('visible'=>true,'label'=>t('Maps'),
        'url'=>array('/admin/map_settings'),'linkOptions'=>array()),  
        
        array('visible'=>true,'label'=>t('Page'),
        'url'=>array('/admin/custompage'),'linkOptions'=>array()),  
                
        array('visible'=>true,'label'=>t('Templates'),
        'url'=>array('/admin/templates'),'linkOptions'=>array()),
        
        array('visible'=>true,'label'=>t('Language'),
        'url'=>array('/admin/language'),'linkOptions'=>array()),
        
        array('visible'=>true,'label'=>t('Cron'),
        'url'=>array('/admin/cron'),'linkOptions'=>array()),
        
        array('visible'=>true,'label'=>t('Map Logs'),
        'url'=>array('/admin/api_logs'),'linkOptions'=>array()),
        
        array('visible'=>true,'label'=>t('API'),
        'url'=>array('/admin/api'),'linkOptions'=>array()),
        
     )
  );
  }
  ?>
  <?php $this->widget('zii.widgets.CMenu', adminMenu());?>

  <?php $this->widget('zii.widgets.CMenu', adminMenu('mobile-demo','side-nav') );?>
   
</div>
</div>
</nav>
