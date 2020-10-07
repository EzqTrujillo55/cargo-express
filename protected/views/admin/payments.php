
<div class="card">
 <div class="card-content">
   
  <div class="row">
    <div class="col s6">
      <h5><?php echo t("Customer Payment History")?></h5>
      <h6><b><?php echo $customer_data['first_name']." ".$customer_data['last_name']?></b></h6>
    </div>
    <div class="col s6 right-align">
      <a href="<?php echo Yii::app()->createUrl('/admin/customer-list')?>" class="btn-floating btn-small waves-effect waves-light">
        <i class="material-icons">replay</i>
      </a>
    </div>
  </div> <!--row-->
  
    
   <table  class="bordered highlight responsive-table">
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
    <?php if(is_array($data) && count($data)>=1): ?>
	    <?php foreach ($data as $val):?>
	    <tr>
	      <td><?php echo AdminFunctions::prettyDate($val['date_created']);?></td>
	      <td><?php echo t($val['transaction_type']);?></td>
	      <td><?php echo AdminFunctions::prettyGateway($val['payment_provider']);?></td>
	      <td><?php echo $val['memo'];?></td>
	      <td><?php echo prettyPrice($val['total_paid']);?></td>
	      <td><?php echo $val['transaction_ref'];?></td>
	    </tr>
	    <?php endforeach;?>
    <?php else :?>
    <tr>
     <td colspan="6"><?php echo t("no results")?></td>
    </tr>
    <?php endif;?>
    </tbody>
   </table> 
     
 </div> <!--card-content-->
</div> <!--card-->