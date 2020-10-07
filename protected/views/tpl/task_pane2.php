

<div class="blue_panel">
   <div class="row">
     <div class="col-xs-6"><?php echo t("Agent")?></div>
     <div class="col-xs-6 text-right">
     
     <a href="#no" class="filter_map" data-toggle="modal" data-target="#modalMapFilter" >
        <i class="ion-ios-settings"></i>
     </a>
     
     <a href="javascript:setMapCenter();"><i class="ion-ios-navigate-outline"></i></a>
     
     <a href="javascript:loadAgentDashboard();"><i class="ion-android-refresh"></i></a>
     <!--<a  href="#no" ><i class="ion-ios-search"></i></a>-->
     
     </div><!-- col-->
   </div> <!--row-->   
</div> <!--blue_panel-->


<ul id="tabs">
 <li class="active"><span class="agent-active-total" >0</span> <?php echo t("Active")?></li>
 <li><span class="agent-offline-total" >0</span> <?php echo t("Offline")?></li>
 <li><span class="agent-total-total">0</span> <?php echo t("Total")?></li>
</ul>

<ul id="tab" class="list_row">
 <li class="active agent-active">

 </li>
 <li class="agent-offline">

 </li>
 
 <li class="agent-total">

 </li>
</ul>

<?php 
$this->renderPartial('/app/filter_map',array(   
));
?>