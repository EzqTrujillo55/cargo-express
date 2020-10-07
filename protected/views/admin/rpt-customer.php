<form id="frm_table" method="POST" onsubmit="return false;">
<?php echo CHtml::hiddenField('action','rptCustomerList');?>

 <ul class="collapsible" data-collapsible="accordion">
  <li>
    <div class="collapsible-header"><i class="material-icons">zoom_in</i><?php echo t("Filter")?></div>
    <div class="collapsible-body">
    
    <div class="row" style="padding:0 20px;">
       <div class="col s3">
         <div class="input-field">	    
	     <?php echo CHtml::textField('start_date',
	     ''
	     ,array('class'=>"datepicker",	  
	     'data-validation'=>'required'
	     ))?>
	     <label><?php echo t("Start Date")?></label>
	   </div>  
       </div>
       <div class="col s3">
         <div class="input-field">	    
	     <?php echo CHtml::textField('end_date',
	     ''
	     ,array('class'=>"datepicker ",	     
	     'data-validation'=>'required'
	     ))?>
	     <label><?php echo t("End Date")?></label>
	   </div>  
       </div>
       
       <div class="col s3">
	     <button class="btn waves-effect waves-light" type="submit" name="action" style="margin-top: 18px;">
	       <?php echo t("Submit")?>
	     </button>
       </div>
       
    </div>
        
    </div>
  </li>
</ul>  
  
<div class="card">
 <div class="card-content">
   
  <div class="row">
    <div class="col s6">
      <h5><?php echo t("Customer Signup")?></h5>
    </div>
    <div class="col s6 right-align">
    
     <a target="_blank" class="waves-effect blue lighten-1 btn" href="<?php echo Yii::app()->createUrl('admin/export',array(
      'filename'=>'customer_signup'
     ))?>" >
     <i class="material-icons left">import_export</i> <?php echo t("Export")?>
     </a>  
    
    </div>
  </div> <!--row-->
     
  
   <table id="table_list" class="bordered highlight responsive-table">
    <thead>
      <tr>
       <th width="5%"><?php echo t("ID")?></th>
       <th width="10%"><?php echo t("Name")?></th>
       <th width="10%"><?php echo t("Mobile number")?></th>
       <th width="10%"><?php echo t("Email address")?></th>
       <th width="10%"><?php echo t("Plan")?></th>  
       <th width="10%"><?php echo t("Status")?></th>       
       <th width="10%"><?php echo t("Date")?></th>  
      </tr>
    </thead>
    <tbody>     
    </tbody>
   </table> 
   
  
  
 </div> <!--card-content-->
</div> <!--card-->

</form>

<style type="text/css">
#table_list_filter{
display:none;
}
</style>