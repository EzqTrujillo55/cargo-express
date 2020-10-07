

<div class="card">
 <div class="card-content">
   
 
  <div class="row">
    <div class="col s6">
      <h5><?php echo t("Currency")?></h5>
    </div>
    <div class="col s6 right-align">
     <a href="<?php echo Yii::app()->createUrl('/admin/services-new')?>" class="btn-floating btn-small waves-effect waves-light">
        <i class="material-icons">add</i>
      </a>
      
      <a href="<?php echo Yii::app()->createUrl('/admin/services')?>" class="btn-floating btn-small waves-effect waves-light">
        <i class="material-icons">replay</i>
      </a>
    </div>
  </div> <!--row-->
 
  
   <form id="frm" method="POST" onsubmit="return false;">
   <?php echo CHtml::hiddenField('action','addServices')?>
   <?php if (isset($data['services_id'])):?>
   <?php echo CHtml::hiddenField('id',$data['services_id'])?>
   <?php endif;?>   
   <?php echo CHtml::hiddenField('foto',$data['foto']);
   $imagen=$data['foto']?>
   
   <?php if (isset($_GET['msg'])):?>
   <?php echo CHtml::hiddenField('msg',$_GET['msg'])?>
   <?php endif;?>   
   
   <div class="row">
      <div class="col s6">
      
        <div class="input-field">
	      <?php echo CHtml::textField('sevices_name',
	      isset($data['sevices_name'])?$data['sevices_name']:''
	      ,array('class'=>"validate",
	      'data-validation'=>"required"	      
	      ))?>
	      <label for="sevices_name"><?php echo t("Services Name")?></label>
       </div>   
      
      </div>     
      
    <div class="col s6">
    <div class="input-field">
        <?php echo CHtml::dropDownList('services_parent_id',
        isset($data['services_parent_id'])?$data['services_parent_id']:''
        ,(array)AdminFunctions::servicesListParentAsList())?>
	    <label><?php echo t("Parent Services")?></label>
    </div>
    </div>
      
   </div> <!-- row-->
   
    <div class="row">
      <div class="col s12">
      
        <div class="input-field">
	      <?php echo CHtml::textArea('description',
	      isset($data['description'])?$data['description']:'',array(
	       'class'=>"materialize-textarea"
	      ))?>
	      <label for="description"><?php echo t("Description")?></label>
       </div>   
      
      </div>     
   </div>   
   
   <div class="row">
    <div class="col s6">
    <div class="input-field">
        <?php echo CHtml::dropDownList('status',
        isset($data['status'])?$data['status']:''
        ,AdminFunctions::statusList())?>
	    <label><?php echo t("Status")?></label>
    </div>
    </div>
    </div>
      <div class="row">
    
     
     <div class="col s6">
         <a id="upload-foto-servicio" class="waves-effect blue lighten-1 btn"><?php echo t("Subir Foto")?></a>
        
         
         <div id="progressBar"></div>
         <div id="progressOuter"></div>
         <div id="msgBox"></div>         
     </div>
     
     <div class="col s6">
       <div class="website_logo card-panel grey lighten-5 z-depth-1">
         <?php if(isset($imagen)):?>       
         <img class="responsive-img" src="<?php echo $imagen?>">         
         <?php endif?>
       </div>
       
       <?php if(isset($imagen)):?>       
        <p class="remove-logo-wrap">
         <a class="remove-logo"  href="#no" ><?php echo t("Click here")?> </a> <?php echo t("to remove logo")?>
        </p>
       <?php endif;?>
         
     </div>
     
   </div>
       
   
     <div class="card-action" style="margin-top:20px;">
     <button class="btn waves-effect waves-light" type="submit" name="action">
       <?php echo t("Submit")?>
     </button>
     
     <?php if (isset($_GET['id'])):?>
     <a title="Delete" href="#no" 
        class="rm-records btn red lighten-1" data-field="services_id" data-value="<?php echo $data['services_id']?>" data-tbl="services">
        <?php echo t("Delete")?>
     </a>
     <?php endif;?>
     
     </div>
            
   </form>         
  
 </div>
</div> 