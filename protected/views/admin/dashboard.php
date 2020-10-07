
<div class="card">
 <div class="card-content">

  <h5><?php echo t("New Signup for today")?> <?php echo Driver::prettyDate(date("c"),false); //echo date("F,d Y")?></h5>
  
 
  <form id="frm_table" method="POST">
  <?php echo CHtml::hiddenField('action','newSignup');?>
   <table id="table_list" class="bordered highlight responsive-table">
    <thead>
      <tr>
        <th width="5%"><?php echo t("ID")?></th>
       <th width="10%"><?php echo t("Name")?></th>
       <th width="10%"><?php echo t("Mobile number")?></th>
       <th width="10%"><?php echo t("Email address")?></th>
       <th width="10%"><?php echo t("Plan")?></th>  
       <th width="10%"><?php echo t("Company name")?></th>  
       <th width="10%"><?php echo t("Code")?></th>  
       <th width="10%"><?php echo t("Status")?></th>
       <th width="15%"><?php echo t("Actions")?></th>
      </tr>
    </thead>
    <tbody>     
    </tbody>
   </table>
   </form>
 
 </div> <!--card-content-->
</div> <!--card-->


<div class="card">
 <div class="card-content">
 
  <h5><?php echo t("Last 30 days signup")?></h5>
  
  <div class="charts"></div>
 
 </div> <!--card-content-->
</div> <!--card-->