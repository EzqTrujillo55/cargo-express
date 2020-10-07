
<div class="card">
 <div class="card-content">
   
   <h5><?php echo t("SEO")?></h5>  
  
   <form id="frm" method="POST" onsubmit="return false;">
   <?php echo CHtml::hiddenField('action','seoSettings')?>
   
    <h6 class="top30"><?php echo t("Homepage SEO")?></h6>
   
    <div class="row">
     <div class="col s6">     
     <div class="input-field">	    
       <?php echo CHtml::textField('home_seo_title',
     getOptionA('home_seo_title')
     ,array('class'=>"validate",'data-validation'=>"required"))?>
     <label><?php echo t("SEO Title")?></label>
      </div>       
     </div>
   </div>
   
   <div class="row">
     <div class="col s6">     
     <div class="input-field">	    
       <?php echo CHtml::textArea('home_seo_meta',
       getOptionA('home_seo_meta')
       ,array('class'=>"materialize-textarea"))?>
       <label><?php echo t("Meta Description")?></label>
      </div>       
     </div>
   </div>
   
   
    <h6 class="top30"><?php echo t("Price Page SEO")?></h6>
   
    <div class="row">
     <div class="col s6">     
     <div class="input-field">	    
       <?php echo CHtml::textField('price_seo_title',
     getOptionA('price_seo_title')
     ,array('class'=>"validate",'data-validation'=>"required"))?>
     <label><?php echo t("SEO Title")?></label>
      </div>       
     </div>
   </div>
   
   <div class="row">
     <div class="col s6">     
     <div class="input-field">	    
       <?php echo CHtml::textArea('price_seo_meta',
       getOptionA('price_seo_meta')
       ,array('class'=>"materialize-textarea"))?>
       <label><?php echo t("Meta Description")?></label>
      </div>       
     </div>
   </div>
   
    <div class="card-action" style="margin-top:20px;">
     <button class="btn waves-effect waves-light" type="submit" name="action">
       <?php echo t("Save settings")?>
     </button>
    </div>
     
    
   </form>  
  
 </div> <!--card-content-->
</div> <!--card-->