
<div class="card">
 <div class="card-content">
   
  <div class="row">
    <div class="col s6">
      <h5><?php echo t("Services")?></h5>
    </div>
    <div class="col s6 right-align">
      <a href="<?php echo Yii::app()->createUrl('/admin/services-new')?>" class="btn-floating btn-small waves-effect waves-light">
        <i class="material-icons">add</i>
      </a>
    </div>
  </div> <!--row-->
  
    
    <?php if (is_array($data) && count($data)>=1):?> 
    <ul class="collection services-list">
      <?php foreach ($data as $val):?>
      <li class="collection-item services-list-li" data-id="<?php echo $val['services_id']?>">
         <a href="<?php echo Yii::app()->createUrl('admin/services-new',array(
         'id'=>$val['services_id']
         ))?>">
         
         <div class="row">
	         <div class="col s6">
	            <b><?php echo $val['sevices_name']?></b>
	         </div>
	         <div class="col s6">
	           <div class="tag <?php echo $val['status']?>"><?php echo t($val['status'])?></div>
	         </div>
         </div>
         
         </a>
         <?php if(is_array($val['sub']) && count($val['sub'])>=1):?>
             <ul class="collection services-list-child">
             <?php foreach ($val['sub'] as $val2):?>
                <li class="collection-item services-list-child"  data-id="<?php echo $val2['services_id']?>" 
                style="padding-bottom:0;" >
                
                  <div class="row">
	                  <div class="col s6" style="padding:0;">
	                  <a href="<?php echo Yii::app()->createUrl('admin/services-new',array('id'=>$val2['services_id']))?>">
	                  <?php echo $val2['sevices_name']?>
	                  </a>
	                  </div>
	                  <div class="col s6">
	                    <div class="tag <?php echo $val2['status']?>"><?php echo t($val2['status'])?></div>
	                  </div>
                  </div> <!--row-->
                  
                </li>
             <?php endforeach;?>
             </ul>
         <?php endif;?>
      </li>      
      <?php endforeach;?>
    </ul>            
    <?php else :?>
    <p><?php echo t("No services found")?></p>
    <?php endif;?>
  
    
    <p style="margin-top:30px;"><?php echo t("Drag the list to sort")?></p>
  
 </div> <!--card-content-->
</div> <!--card-->