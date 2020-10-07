
<div class="card">
 <div class="card-content">
   
  <div class="row">
    <div class="col s6">
      <h5><?php echo t("API logs")?></h5>
    </div>
    
  </div> <!--row-->
  
  
  <form id="frm_table" method="POST">
  <?php //echo CHtml::hiddenField('action','customPageList');
echo CHtml::hiddenField('action','apiLogs');
  ?>
   <table id="table_list" class="bordered highlight responsive-table">
    <thead>
      <tr>
       <th width="5%"><?php echo t("ID")?></th>
       <th width="10%"><?php echo t("Provider")?></th>
       <th width="10%"><?php echo t("Functions")?></th>
       <th width="10%"><?php echo t("Response")?></th>
       <th width="10%"><?php echo t("Date")?></th>       
      </tr>
    </thead>
    <tbody>     
    </tbody>
   </table> 
   </form>
  
  
 </div> <!--card-content-->
</div> <!--card-->