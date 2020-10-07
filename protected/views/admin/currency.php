
<div class="card">
 <div class="card-content">
   
  <div class="row">
    <div class="col s6">
      <h5><?php echo t("Currency")?></h5>
    </div>
    <div class="col s6 right-align">
      <a href="<?php echo Yii::app()->createUrl('/admin/currency-new')?>" class="btn-floating btn-small waves-effect waves-light">
        <i class="material-icons">add</i>
      </a>
    </div>
  </div> <!--row-->
  
  
  <form id="frm_table" method="POST">
  <?php echo CHtml::hiddenField('action','currencyList');?>
   <table id="table_list" class="bordered highlight responsive-table">
    <thead>
      <tr>
       <th width="5%"><?php echo t("ID")?></th>
       <th width="10%"><?php echo t("Currency Code")?></th>
       <th width="10%"><?php echo t("Currency Symbol")?></th>
       <th width="10%"><?php echo t("Status")?></th>
       <th width="8%"><?php echo t("Actions")?></th>
      </tr>
    </thead>
    <tbody>     
    </tbody>
   </table> 
   </form>
  
  
 </div> <!--card-content-->
</div> <!--card-->