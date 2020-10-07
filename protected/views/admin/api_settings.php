
<div class="card">
 <div class="card-content">
   
  <div class="row">
    <div class="col s6">
      <h5><?php echo t("API Services")?></h5>
    </div>
    
  </div> <!--row-->
  
  <form id="frm" method="POST" onsubmit="return false;">
   <?php echo CHtml::hiddenField('action','apiServicesSettings')?>   
  
     <div class="row">
     <div class="col s6">
          <div class="input-field">	    
	       <?php echo CHtml::textField('api_services_key',
	       getOptionA('api_services_key')
	       ,array(
	         'class'=>"validate",
	         'onclick'=>"this.blur()"
	         //'disabled'=>true
	         ))?>
	       <label><?php echo t("API services key")?></label>
	      </div>      
	      <a href="#no" class="gen_api_services_key"><?php echo t("Click here to generate keys")?></a>
     </div>
   </div>
   
   
   
  <ul class="collapsible">
     <li>
      <div class="collapsible-header"><i class="material-icons">reorder</i>
       <?php echo t("INSERT_TASK")?>
      </div>
      <div class="collapsible-body" style="padding:20px;">           
      
        <p>
        <?php echo t("API LINK")?> : 
        <a href="<?php echo Driver::getHostURL().Yii::app()->createUrl("api_services/insert_task")?>" target="_blank">
        <?php echo Driver::getHostURL().Yii::app()->createUrl("api_services/insert_task")?>
        </a>
        </p>
        
        <table class="striped responsive-table">
         <tr>
          <td colspan="3"><?php echo t("Parameters")?>:</td>
         </tr>
         
         <tr>
          <th><?php echo t("fields")?></th>
          <th><?php echo t("description")?></th>
          <th><?php echo t("required")?></th>
         </tr>
         
         <tr>
          <td><?php echo t("keys")?></td>
          <td><?php echo t("Your unique api services keys")?></td>
          <td><?php echo t("yes")?></td>
         </tr>
         <tr>
          <td><?php echo t("merchant_token")?></td>
          <td><?php echo t("unique merchant token")?></td>
          <td><?php echo t("yes")?></td>
         </tr>
         
         <tr>
          <td><?php echo t("task_description")?></td>
          <td><?php echo t("description of you task")?></td>
          <td><?php echo t("optional")?></td>
         </tr>
         <tr>
          <td><?php echo t("trans_type")?></td>
          <td><?php echo t("transaction type either delivery ot pickup")?></td>
          <td><?php echo t("yes")?></td>
         </tr>
         <tr>
          <td><?php echo t("contact_number")?></td>
          <td><?php echo t("customer contact number")?></td>
          <td><?php echo t("optional")?></td>
         </tr>
         <tr>
          <td><?php echo t("email_address")?></td>
          <td></td>
          <td><?php echo t("yes")?></td>
         </tr>
         <tr>
          <td><?php echo t("customer_name")?></td>
          <td></td>
          <td><?php echo t("yes")?></td>
         </tr>
         <tr>
          <td><?php echo t("delivery_date")?></td>
          <td><?php echo t("Delivery format YYYY-MM-DD HH:MM:SS");?></td>
          <td><?php echo t("yes")?></td>
         </tr>
         <tr>
          <td><?php echo t("delivery_address")?></td>
          <td></td>
          <td><?php echo t("yes")?></td>
         </tr>
         <tr>
          <td><?php echo t("task_lat")?></td>
          <td><?php echo t("latitude coordinates of your task")?></td>
          <td><?php echo t("optional")?></td>
         </tr>
         <tr>
          <td><?php echo t("task_lng")?></td>
          <td><?php echo t("longtitude coordinates of your task")?></td>
          <td><?php echo t("optional")?></td>
         </tr>
         
         
        </table>
                
        <table class="striped responsive-table" style="margin-top:30px;">
         <tr>
          <td colspan="2"><?php echo t("Response")?>:</td>
         </tr>
         
         <tr>
          <th><?php echo t("fields")?></th>
          <th><?php echo t("description")?></th>          
         </tr>
         
         <tr>
          <td><?php echo t("code")?></td>
          <td><?php echo t("1 = for successful and 2 = for failed")?></td>
         </tr>
         
         <tr>
          <td><?php echo t("msg")?></td>
          <td><?php echo t("successful message or error message")?></td>
         </tr>
         
         <tr>
          <td><?php echo t("details")?></td>
          <td><?php echo t("array containing task_token")?></td>
         </tr>
         
        </table>
        
      </div> <!--body-->
    </li>
    
   </ul>
   
     <div class="card-action" style="margin-top:20px;">
     <button class="btn waves-effect waves-light" type="submit" name="action">
       <?php echo t("Save settings")?>
     </button>
    </div>
   
  </form>
  
  
 </div> <!--card-content-->
</div> <!--card-->