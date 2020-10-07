<div class="track_map_wrapper">

  <div class="track-header">
    <div class="row">
      <div class="col-xs-6">
         <?php if (!empty($logo_url)):?>
         <img class="company-logo" src="<?php echo $logo_url?>" />
        <?php endif;?>
      </div>
      <div class="col-xs-6 text-right">
         <img class="logo" src="<?php echo FrontFunctions::getLogoURL();?>">
      </div>
    </div>
  </div> <!--track-header-->
  
  <div class="track-layer track-done">
  <h3 class="top30"><?php echo $msg?></h3>
  </div>
  
  
 </div>