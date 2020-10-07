
<div class="card">
 <div class="card-content">

 <h5><?php echo t("Manage Language")?></h5>
 
 
 <?php if(is_array($list) && count($list)>=1):?>
 
   <div class="top30"></div>
   <p><?php echo t("Tick the language to enabled")?></p>
  
   <form id="frm" method="POST" onsubmit="return false;">
   <?php echo CHtml::hiddenField('action','saveLanguage')?>
   
   <?php 
   /*$new_list='';
   foreach ($list as $val){
      $new_list[$val]=$val;
   }   */
   ?>
   
   <!--<div class="row top30">
     <div class="col s6">
     <div class="input-field">
	       <?php echo CHtml::dropDownList('default_lang',
	        getOptionA('default_lang'),
	        $new_list)?>
		    <label><?php echo t("Default Language")?></label>
	   </div>
     </div>
   </div>-->
	        
	<?php $lang_list=array();?>
 
    <ul class="collection">
      <?php foreach ($list as $val):?>
      <?php $lang_list[$val]=$val;?>
       <li class="collection-item">
               
         <?php echo CHtml::checkBox('lang[]',
	      in_array($val,(array)$selected_lang)?true:false
	      ,array(
	        'id'=>$val,
	        'class'=>"with-gap",
	        'value'=>$val
	      ))?>
	      <label for="<?php echo $val?>"><?php echo $val?></label>	
	      	      
       </li>
      <?php endforeach;?>
    </ul>
    
           
   <div class="row">
      <div class="col s4">  
	        <div class="input-field">
	        <p><?php echo t("App Default Language")?></p>   
	        <?php echo CHtml::dropDownList('app_default_lang',
	        getOptionA('app_default_lang'),
	        (array)$lang_list)?>
		    <!--<label><?php echo t("App Default Language")?></label>-->
		    </div>
      </div> <!--col-->
      
      <div class="col s4" style="padding-top:50px;">
      
          <?php echo CHtml::checkBox('app_force_lang',
	      getOptionA('app_force_lang')==1?true:false
	      ,array(
	        'id'=>"app_force_lang",
	        'class'=>"with-gap",
	        'value'=>1
	      ))?>
	      <label for="app_force_lang"><?php echo t("Force default language")?></label>	
      
      </div> <!--col-->
   </div> <!--row-->
    
    <div class="card-action" style="margin-top:20px;">
     <button class="btn waves-effect waves-light" type="submit" name="action">
       <?php echo t("Save settings")?>
     </button>
    </div>
         
   </form>  
 <?php else :?>
 <p><?php echo t("No language found")?></p>
 <?php endif;?>
 
 </div>
</div>