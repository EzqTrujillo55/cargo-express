<div class="track_map_wrapper">

  <div class="track-header">
    <div class="row">
      <div class="col-xs-6">
        <?php if (!empty($logo_url)):?>
         <img class="company-logo" src="<?php echo $logo_url?>" />
        <?php endif;?>
      </div>
      <div class="col-xs-6 text-right">
         <a href="<?php echo websiteUrl()?>">
         <img class="logo" src="<?php echo FrontFunctions::getLogoURL();?>">
         </a>
      </div>
    </div>
  </div> <!--track-header-->
  
  <div class="track-center-map">
     <a href="#no" class="track-center-map">     
     <i class="ion-ios-navigate-outline"></i>     
     </a>
  </div> <!--track-center-map-->
  
  <?php if (!empty($data['driver_phone'])):?>
  <div class="track-center-map call-wrap">
     <a href="tel:<?php echo $data['driver_phone']?>" >     
     <i class="ion-ios-telephone-outline"></i>     
     </a>
  </div> <!--track-center-map-->
  <?php else :?>
  <div class="track-center-map call-wrap"></div>
  <?php endif;?>
  
  <div class="track_map" id="track_map"></div> <!--track_map-->
  
  <div class="track-arrived-wrap track-layer">       
     <i class="ion-ios-bell-outline"></i>
     <h3><?php echo t("Hey i'm here")?>!</h3>
     <p class="arrived-msg">
     <?php echo $data['driver_name']?>, <?php echo t("just arrived at")?> 
     <?php      
     if ($data['trans_type']=="delivery"){
     	echo $data['delivery_address'];
     } else {
     	if (!empty($data['drop_address'])){
     		echo $data['drop_address'];
     	} else echo $data['delivery_address'];
     };
     ?></p>
  </div> <!--track-arrived-wrap-->
  
  <div class="track-message track-layer">       
  </div>
  
  
  <div class="track-layer track-rating-wrap">
       
     <h3><?php echo t("Rate Your Experience")?>!</h3>     
      <div class="avatar-wrapper top20">
        <img src="<?php echo $avatar?>" class="avatar">      
      </div> <!--avatar-wrapper-->
      <p><?php echo $data['driver_name']?></p>
     
      <form method="POST" class="frm top20" id="frm">
      
      <div class="raty-stars" data-score="0"></div>   
      
      <?php echo CHtml::hiddenField('action','customerRating')?>
      <?php echo CHtml::hiddenField('task_id',$data['task_id']);?>
      
      <p class="top20"><?php echo CHtml::textArea('rating_comment','',array(
        'placeholder'=>t("Leave your comment")
      ))?></p>
      <p class="top30"><button class="rounded relative yellow-button large"><?php echo t("Submit")?></button></p>
      </form>
      
  </div> <!--track-rating-wrap-->
  
  <div class="track-details">
   <div class="spinner">
		  <div class="double-bounce1"></div>
		  <div class="double-bounce2"></div>
		</div>

    
     <div class="trackdetails-wrap">
       <p class="text-center"><?php echo t("Loading information")?></p>
     </div>     
     <!--<p class="text-center no-agent-p"><?php echo t("There is no assign agent for this task")?></p>-->
    
  </div> <!--track-details-->
  
</div> <!--track_map_wrapper-->

<?php 
//dump($data);die();

echo CHtml::hiddenField('task_lat',$data['task_lat']);
echo CHtml::hiddenField('task_lng',$data['task_lng']);
echo CHtml::hiddenField('task_status',$data['status']);

$delivery_address='<p>'.$data['delivery_address'];
$delivery_address.='</p>';
if ($data['trans_type']=="delivery"){
   $delivery_address.='<p class="text-muted">'.t("Delivery address").'</p>';
} else $delivery_address.='<p class="text-muted">'.t("Pickup Details").'</p>';

$driver_info_window='<p>'.$data['driver_name']."</p>";
$driver_info_window.='<p>'.t("Your Agent")."</p>";

$drofoff_info_window='<p>'.$data['drop_address']."</p>";
if ($data['trans_type']=="delivery"){
   $drofoff_info_window.='<p>'.t("Pickup Details")."</p>";	
} else $drofoff_info_window.='<p>'.t("Drop Details")."</p>";

if (!empty($data['transport_type'])){
	$travel_mode=$data['transport_type'];
} else $travel_mode='driving';

$cs = Yii::app()->getClientScript();

$cs->registerScript('task_id',"var task_id='".$_GET['id']."';",CClientScript::POS_HEAD);
$cs->registerScript('task_lat',"var task_lat='".$data['task_lat']."';",CClientScript::POS_HEAD);
$cs->registerScript('task_lng',"var task_lng='".$data['task_lng']."';",CClientScript::POS_HEAD);

$cs->registerScript('delivery_address',"var delivery_address='".$delivery_address."';",CClientScript::POS_HEAD);

// referrence http://stackoverflow.com/questions/31197596/google-map-api-marker-icon-url
//$icon_driver=websiteUrl()."/assets/images/car.png";
$icon_driver = websiteUrl().'/assets/images/red.png';
$icon_dropoff= websiteUrl().'/assets/images/blue.png';
$icon_finish = websiteUrl().'/assets/images/orange-dot.png';

$cs->registerScript('icon_driver',"var icon_driver='".$icon_driver."';",CClientScript::POS_HEAD);

$cs->registerScript('icon_finish',"var icon_finish='".$icon_finish."';",CClientScript::POS_HEAD);

$cs->registerScript('icon_dropoff',"var icon_dropoff='".$icon_dropoff."';",CClientScript::POS_HEAD);

$cs->registerScript('trans_type',"var trans_type='".$data['trans_type']."';",CClientScript::POS_HEAD);

$cs->registerScript('drop_address',"var drop_address='".$data['drop_address']."';",CClientScript::POS_HEAD);
$cs->registerScript('dropoff_task_lat',"var dropoff_task_lat='".$data['dropoff_task_lat']."';",CClientScript::POS_HEAD);
$cs->registerScript('dropoff_task_lng',"var dropoff_task_lng='".$data['dropoff_task_lng']."';",CClientScript::POS_HEAD);
$cs->registerScript('dropoff_contact_name',"var dropoff_contact_name='".$data['dropoff_contact_name']."';",CClientScript::POS_HEAD);
$cs->registerScript('dropoff_contact_number',"var dropoff_contact_number='".$data['dropoff_contact_number']."';",CClientScript::POS_HEAD);

$cs->registerScript('drofoff_info_window',"var drofoff_info_window='".$drofoff_info_window."';",CClientScript::POS_HEAD);

$cs->registerScript('driver_id',"var driver_id='".$data['driver_id']."';",CClientScript::POS_HEAD);
$cs->registerScript('driver_name',"var driver_name='".$data['driver_name']."';",CClientScript::POS_HEAD);
$cs->registerScript('driver_phone',"var driver_phone='".$data['driver_phone']."';",CClientScript::POS_HEAD);
$cs->registerScript('driver_email',"var driver_email='".$data['driver_email']."';",CClientScript::POS_HEAD);
$cs->registerScript('driver_location_lat',"var driver_location_lat='".$data['driver_location_lat']."';",CClientScript::POS_HEAD);
$cs->registerScript('driver_location_lng',"var driver_location_lng='".$data['driver_location_lng']."';",CClientScript::POS_HEAD);

$cs->registerScript('driver_info_window',"var driver_info_window='".$driver_info_window."';",CClientScript::POS_HEAD);
$cs->registerScript('travel_mode',"var travel_mode='".$travel_mode."';",CClientScript::POS_HEAD);

$find_driver_label=t("Find agent");
$cs->registerScript('find_driver_label',"var find_driver_label='".$find_driver_label."';",CClientScript::POS_HEAD);

$drv_map_style=Yii::app()->functions->getOption(  'drv_map_style' , $data['customer_id']);
$drv_map_style_res = json_decode($drv_map_style);
if ( is_array($drv_map_style_res) && !empty($drv_map_style)){

$cs->registerScript(
	 'map_style',
	 "var map_style=$drv_map_style",
	  CClientScript::POS_HEAD
	);
}
?>