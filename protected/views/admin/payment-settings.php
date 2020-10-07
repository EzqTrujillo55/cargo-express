
<div class="card">
 <div class="card-content">
   
  <div class="row">
    <div class="col s6">
      <h5><?php echo t("Payment Gateway")?></h5>
    </div>    
  </div> <!--row-->
         
  <?php if(is_array($payment_list) && count($payment_list)>=1):?>
   <div class="row">
    <div class="col s12">
      <ul class="tabs z-depth-1">
        <?php foreach ($payment_list as $key=>$val):?>
        <li class="tab col s2"><a class="active" href="#<?php echo $key?>"><?php echo $val?></a></li>
        <?php endforeach;?>        
      </ul>
    </div> <!--col-->
    
    <?php foreach ($payment_list as $key=>$val):?>
    <div id="<?php echo $key;?>" class="col s12 top40">
      <?php $this->renderPartial('/admin/gateway-'.$key);?>
    </div>    
    <?php endforeach;?>
    
   </div> <!--row-->
   <?php endif;?>
  
  
 </div> <!--card-content-->
</div> <!--card-->