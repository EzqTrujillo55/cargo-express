
<div class="card">
 <div class="card-content">
   
  <div class="row">
    <div class="col s6">
      <h5><?php echo t("Custom page")?></h5>
    </div>
    <div class="col s6 right-align">
      <a title="<?php echo t("new page")?>" href="<?php echo Yii::app()->createUrl('/admin/custompage-new')?>" class="btn-floating btn-small waves-effect waves-light">
        <i class="material-icons">add</i>
      </a>
      
      <a title="<?php echo t("assign page")?>" href="<?php echo Yii::app()->createUrl('/admin/custompage-assign')?>" class="btn-floating btn-small waves-effect waves-light">
        <i class="material-icons">web</i>
      </a>
      
    </div>
  </div> <!--row-->
  
  
  <form id="frm_table" method="POST">
  <?php echo CHtml::hiddenField('action','customPageList');?>
   <table id="table_list" class="bordered highlight responsive-table">
    <thead>
      <tr>
       <th width="5%"><?php echo t("ID")?></th>
       <th width="10%"><?php echo t("Slug")?></th>
       <th width="10%"><?php echo t("Page title")?></th>
       <th width="10%"><?php echo t("Status")?></th>
       <th width="10%"><?php echo t("Date")?></th>
       <th width="10%"><?php echo t("Actions")?></th>
      </tr>
    </thead>
    <tbody>     
    </tbody>
   </table> 
   </form>
  
  
 </div> <!--card-content-->
</div> <!--card-->