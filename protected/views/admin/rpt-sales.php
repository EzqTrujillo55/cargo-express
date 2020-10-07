<form id="frm_table" method="POST" onsubmit="return false;">
<?php echo CHtml::hiddenField('action','rptSales');?>

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
      <h5><?php echo t("Sales report")?></h5>
    </div>
    <div class="col s6 right-align">
    
     <a target="_blank" class="waves-effect blue lighten-1 btn" href="<?php echo Yii::app()->createUrl('admin/export',array(
      'filename'=>'sales_report'
     ))?>" >
     <i class="material-icons left">import_export</i> <?php echo t("Export")?>
     </a>  
    
    </div>
  </div> <!--row-->
     
  
   <table id="table_list" class="bordered highlight responsive-table">
    <thead>
      <tr>
       <th width="8%"><?php echo t("Date")?></th>
       <th width="5%"><?php echo t("Trans Type")?></th>
       <th width="10%"><?php echo t("Payment Provider")?></th>
       <th width="10%"><?php echo t("Memo")?></th>
       <th width="5%"><?php echo t("Total")?></th>
       <th width="10%"><?php echo t("Transaction Ref")?></th>                
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