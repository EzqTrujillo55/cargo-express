
<div class="card">
 <div class="card-content">
  
  <div class="row">
    <div class="col s6">
      <h5><?php echo t("Assign Custom page")?></h5>
    </div>
    <div class="col s6 right-align">     
      <a href="<?php echo Yii::app()->createUrl('/admin/custompage')?>" class="btn-floating btn-small waves-effect waves-light">
        <i class="material-icons">replay</i>
      </a>
    </div>
  </div> <!--row-->
 
  
  <form id="frm" method="POST" onsubmit="return false;">
  <?php echo CHtml::hiddenField('action','saveAssignPage')?>
  
  
  <div class="row">
    <div class="col s12">
      <ul class="collection">
        <?php if (is_array($data) && count($data)>=1):?>
        <?php foreach ($data as $val):  $page_id=$val['page_id']?>
        <li class="collection-item">
                                 
          <div class="row">
            <div class="col s6">
            
             <?php echo CHtml::checkBox("page_id[$page_id]",
		      $val['active']==1?true:false
		      ,array(
		        'id'=>$val['page_id'],
		        'class'=>"with-gap",
		        'value'=>1
		      ))?>
		      <label for="<?php echo $val['page_id']?>"><?php echo $val['title']?></label>	   
            
            </div>
            <div class="col s6">
            
              <?php echo CHtml::dropDownList("assign_to[$page_id]",
              $val['assign_to']
              ,array(
                'top'=>t("top"),
                'bottom-1'=>t("Bottom - menu"),
                'bottom-2'=>t("Bottom - others")
              ),array(               
               'class'=>"select-normal"
              ))?>
            
            </div>
          </div> <!--row-->
	      	      
        </li>
        <?php endforeach;?>
        <?php endif;?>
      </ul>
    </div> <!--col-->    
  </div> <!--row-->
  
  
  <div class="card-action" style="margin-top:20px;">
  <button class="btn waves-effect waves-light" type="submit" name="action">
    <?php echo t("Submit")?>
  </button>
  </div>
  
  </form>
  
  
 </div>
</div> 