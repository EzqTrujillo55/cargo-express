
<div class="card">
 <div class="card-content">
 
 <form id="frm" method="POST" onsubmit="return false;">
 <?php echo CHtml::hiddenField('action','saveMobileSettings')?>
 
  <h5><?php echo t("Mobile API URL")?></h5>  
   <p class="rounded" style="background:#009688;color:#fff;display:table;padding:3px 5px;"><?php echo Yii::app()->getBaseUrl(true)."/api"?></p>   
   
   <h5 class="top30"><?php echo t("Mobile API Key")?></h5>  
   
    <div class="row">
     <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::textField('mobile_api_key',
	       getOptionA('mobile_api_key')
	       ,array(
	         'class'=>"validate",	        
	         ))?>
	       <label><?php echo t("Your Mobile API key")?></label>
	      </div>      
	      <p class="grey lighten-5"><?php echo t("This fields is optional if you want to secure api fill this fields")?></p>
     </div>
   </div>
   
   
<!--TABS-->   
  <div class="row">
    <div class="col s12">
      <ul class="tabs">
        <li class="tab col s3"><a href="#fcm" class="active" ><?php echo t("Firebase Cloud Messaging")?></a></li>
        <li class="tab col s3"><a href="#legacy"><?php echo t("Push Legacy Settings")?></a></li>        
      </ul>
    </div>
    <div id="fcm" class="col s12 addpad">
    
          <div class="input-field">	    
	       <?php echo CHtml::textField('fcm_server_key',
	       getOptionA('fcm_server_key')
	       ,array(
	         'class'=>"validate",	        
	         ))?>
	       <label><?php echo t("Server Key")?></label>
	      </div>      
    
    </div>
    <div id="legacy" class="col s12 addpad">
      <?php $this->renderPartial('/admin/legacy-settings');?>
    </div>    
  </div>
<!--END TABS-->

 <br/>
   
    <div class="card-action" style="margin-top:20px;">
     <button class="btn waves-effect waves-light" type="submit" name="action">
       <?php echo t("Save settings")?>
     </button>
    </div>
 
 </form>
 
 </div> <!--content-->
</div> <!--card-->