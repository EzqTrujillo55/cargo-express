
<div class="card">
 <div class="card-content">
   
  <div class="row">
    <div class="col s6">
      <h5><?php echo t("Plans")?></h5>
    </div>
    <div class="col s6 right-align">
      <a href="<?php echo Yii::app()->createUrl('/admin/plan-new')?>" class="btn-floating btn-small waves-effect waves-light">
        <i class="material-icons">add</i>
      </a>
    </div>
  </div> <!--row-->
  
  <form id="frm_table" method="POST">
  <?php echo CHtml::hiddenField('action','planList');?>
   <table id="table_list" class="bordered highlight responsive-table">
    <thead>
      <tr>
       <th width="5%"><?php echo t("ID")?></th>
       <th width="10%"><?php echo t("Plan name")?></th>       
       <th width="10%"><?php echo t("Description")?></th>       
       <th width="10%"><?php echo t("Type")?></th>   
       <th width="10%"><?php echo t("Price")?></th>
       <th width="10%"><?php echo t("Status")?></th>
       <th width="10%"><?php echo t("Actions")?></th>
      </tr>
    </thead>
    <tbody>     
    </tbody>
   </table> 
   </form>
  
  
 </div> <!--card-content-->
</div> <!--card-->