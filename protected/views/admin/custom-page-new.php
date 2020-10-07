

<div class="card">
 <div class="card-content">
   
 
  <div class="row">
    <div class="col s6">
      <h5><?php echo t("Custom page")?></h5>
    </div>
    <div class="col s6 right-align">
     <a href="<?php echo Yii::app()->createUrl('/admin/custompage-new')?>" class="btn-floating btn-small waves-effect waves-light">
        <i class="material-icons">add</i>
      </a>
      
      <a href="<?php echo Yii::app()->createUrl('/admin/custompage')?>" class="btn-floating btn-small waves-effect waves-light">
        <i class="material-icons">replay</i>
      </a>
    </div>
  </div> <!--row-->
   
   <form id="frm" method="POST" onsubmit="return false;">
   <?php echo CHtml::hiddenField('action','addCustomePage')?>
   <?php if (isset($data['page_id'])):?>
   <?php echo CHtml::hiddenField('id',$data['page_id'])?>
   <?php endif;?>   
   
   <?php if (isset($_GET['msg'])):?>
   <?php echo CHtml::hiddenField('msg',$_GET['msg'])?>
   <?php endif;?>   
   
   <div class="row">
    <div class="col s12">   
	   <div class="input-field">
	      <?php echo CHtml::textField('title',
	      isset($data['title'])?$data['title']:''
	      ,
	       array(
	        'class'=>"validate",
	        'data-validation'=>"required"
	      ))?>
	      <label><?php echo t("Title")?></label>
	   </div>   
    </div>      
  </div>
  
  <div class="row">
    <div class="col s12">
     <!--<textarea name="content" class="text-editor" style="min-height:250px;"></textarea>-->
     <?php 
     echo CHtml::textArea('content',
     $data['content']
     ,array(
       'class'=>'text-editor',
       'style'=>'min-height:250px;'
     ))
     ?>
     <div class="text-editor_val"><?php echo isset($data['content'])?$data['content']:''?></div>
    </div>
  </div>

  <div class="row">
    <div class="col s12">
    <div class="input-field">
        <?php echo CHtml::dropDownList('status',
        isset($data['status'])?$data['status']:''
        ,AdminFunctions::statusList())?>
	    <label><?php echo t("Status")?></label>
    </div>
    </div>
    </div>
       
  <div class="card-action" style="margin-top:20px;">
  <button class="btn waves-effect waves-light" type="submit" name="action">
    <?php echo t("Submit")?>
  </button>
  </div>
             
   </form>
         
  
 </div>
</div> 