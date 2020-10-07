

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
      
      <a href="<?php echo Yii::app()->createUrl('/admin/plan-list')?>" class="btn-floating btn-small waves-effect waves-light">
        <i class="material-icons">replay</i>
      </a>
    </div>
  </div> <!--row-->
 
  
   <form id="frm" method="POST" onsubmit="return false;">
   <?php echo CHtml::hiddenField('action','addPlans')?>
   <?php if (isset($data['plan_id'])):?>
   <?php echo CHtml::hiddenField('id',$data['plan_id'])?>
   <?php endif;?>   
   
   <?php if (isset($_GET['msg'])):?>
   <?php echo CHtml::hiddenField('msg',$_GET['msg'])?>
   <?php endif;?>   
   
   <div class="input-field">
	      <?php echo CHtml::textField('plan_name',
	      isset($data['plan_name'])?$data['plan_name']:''
	      ,array('class'=>"validate",
	      'data-validation'=>"required"
	      ))?>
	      <label for="plan_name"><?php echo t("Plan name")?></label>
   </div>
   
    <div class="input-field">
	      <?php echo CHtml::textArea('plan_name_description',
	      isset($data['plan_name_description'])?$data['plan_name_description']:''
	      ,array('class'=>"materialize-textarea",
	      //'data-validation'=>"required"
	      ))?>
	      <label for="plan_name_description"><?php echo t("Description")?></label>
   </div>
   
   <div class="row">
    <div class="col s6">
   
	   <div class="input-field">
	      <?php echo CHtml::textField('price',
	      isset($data['price'])?normalPrettyPrice($data['price']):''
	      ,
	       array(
	        'class'=>"validate numeric_only",
	        'data-validation'=>"required"
	      ))?>
	      <label><?php echo t("Price")?></label>
	   </div>
   
    </div>
   
    <div class="col s6">

	   <div class="input-field">
	      <?php echo CHtml::textField('promo_price',
	      isset($data['promo_price'])?$data['promo_price']>0?normalPrettyPrice($data['promo_price']):'':''
	      ,
	       array(
	        'class'=>"validate numeric_only",	       
	      ))?>
	      <label><?php echo t("Promo Price")?></label>
	   </div>

    </div>
   </div><!-- row-->
   
    <div class="row">
     <div class="col s6">
         <div class="input-field">
            <?php echo CHtml::dropDownList('plan_type',
            isset($data['plan_type'])?$data['plan_type']:''
            ,array(
              'days'=>t("Day"),
              'month'=>t("Month"),
              'year'=>t("Year"),
            ))?>
		    <label><?php echo t("Type")?></label>
         </div>
     </div> <!--col-->
     <div class="col s6">
         <div class="input-field">
	      <?php echo CHtml::textField('expiration',
	      isset($data['expiration'])?$data['expiration']:''
	      ,
	       array(
	        'class'=>"validate numeric_only",
	        'data-validation'=>"required"
	      ))?>
	      <label><?php echo t("Expiration")." ".t("number of days/month/year")?></label>
	   </div>
     </div> <!--col-->
    </div> <!--row-->
    
    <div class="row">
	    <div class="col s6">
	    <div class="input-field">
	        <?php echo CHtml::dropDownList('allowed_driver',
	        isset($data['allowed_driver'])?$data['allowed_driver']:''
	        ,array(
	          'unlimited'=>t("unlimited"),
	          1=>1,
	          2=>2,
	          3=>3,
	          4=>4,         
	          5=>5,
	          6=>6,
	          7=>7,
	          8=>8,
	          9=>9,
	          10=>10,
	        ))?>
		    <label><?php echo t("Number of allowed driver")?></label>
	    </div>
	    </div>
	    
	    <div class="col s6">
	    <div class="input-field">
	        <?php echo CHtml::dropDownList('allowed_task',
	        isset($data['allowed_task'])?$data['allowed_task']:''
	        ,array(
	          'unlimited'=>t("unlimited"),
	          1=>1,
	          2=>2,
	          3=>3,
	          4=>4,
	          5=>5,
	          10=>10,
	          20=>20,
	          30=>30,
	          40=>40,         
	          50=>50,
	          60=>60,
	          70=>70,
	          80=>80,
	          90=>90,
	          100=>100,     
	          200=>200,
	          300=>300,
	          400=>400,
	          500=>500,
	        ))?>
		    <label><?php echo t("Number of allowed task")?></label>
	    </div>
	    </div>
    </div> <!--row-->
    
    
    <div class="row">
    <div class="col s6">
    <p>
      <?php echo CHtml::checkBox('with_sms',
      isset($data['with_sms'])?$data['with_sms']==1?true:false:''
      ,array(
       'value'=>1
      ))?>      
      <label for="with_sms"><?php echo t("With SMS Notification")?></label>
    </p>
    </div>
    
    <div class="col s6">
      <div class="input-field">
	      <?php echo CHtml::textField('sms_limit',
	      isset($data['sms_limit'])?$data['sms_limit']:''
	      ,
	       array(
	        'class'=>"validate numeric_only",
	        //'data-validation'=>"required"
	      ))?>
	      <label><?php echo t("SMS Limit")?></label>
	   </div>
    </div>    
    </div>
    
    <div class="row">
    <div class="col s6">
    <p>
      <?php echo CHtml::checkBox('with_broadcast',
      isset($data['with_broadcast'])?$data['with_broadcast']==1?true:false:''
      ,array(
       'value'=>1
      ))?>      
      <label for="with_broadcast"><?php echo t("With Push Broadcast")?></label>
    </p>
    </div>
    </div>
    
    <div class="row">
    <div class="col s12">
    <div class="input-field">
        <?php echo CHtml::dropDownList('status',
        isset($data['status'])?$data['status']:''
        ,AdminFunctions::statusList())?>
	    <label><?php echo t("Status")?></label>
    </div>
    </div>
    </div>
    
     <div class="card-action" style="margin-top:20px;">
     <button class="btn waves-effect waves-light" type="submit" name="action">
       <?php echo t("Submit")?>
     </button>
     </div>
         
   
   </form>
         
  
 </div>
</div> 