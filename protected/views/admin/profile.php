
<div class="card">
 <div class="card-content">
   
  <div class="row">
    <div class="col s6">
      <h5><?php echo t("Profile")?></h5>
    </div>    
  </div> <!--row-->
  
  <form id="frm" method="POST" onsubmit="return false;">
   <?php echo CHtml::hiddenField('action','updateProfile')?>
   
  <div class="row">
    <div class="col s6">
         <div class="input-field">
	      <?php echo CHtml::textField('first_name',
	      isset($data['first_name'])?$data['first_name']:''
	      ,
	       array(
	        'class'=>"validate",
	        'data-validation'=>"required"
	      ))?>
	      <label><?php echo t("First name")?></label>
	   </div>   
    </div>
    <div class="col s6">
        <div class="input-field">
	      <?php echo CHtml::textField('last_name',
	      isset($data['last_name'])?$data['last_name']:''
	      ,
	       array(
	        'class'=>"validate",	   
	        'data-validation'=>"required"    
	      ))?>
	      <label><?php echo t("Last name")?></label>
	   </div>
    </div>
  </div>
  
  
  <div class="row">
    <div class="col s6">
         <div class="input-field">
	      <?php echo CHtml::textField('username',
	      isset($data['username'])?$data['username']:''
	      ,
	       array(
	        'class'=>"validate",
	        'data-validation'=>"required"
	      ))?>
	      <label><?php echo t("Username")?></label>
	   </div>   
    </div>   
    
    <div class="col s6">
         <div class="input-field">
	      <?php echo CHtml::textField('email_address',
	      isset($data['email_address'])?$data['email_address']:''
	      ,
	       array(
	        'class'=>"validate",
	        'data-validation'=>"required"
	      ))?>
	      <label><?php echo t("Email address")?></label>
	   </div>   
    </div>   
    
  </div>  
  
  <div class="row">

     <div class="col s6">
        <div class="input-field">
	      <?php echo CHtml::passwordField('password',
	      ''
	      ,
	       array(
	        'class'=>"validate",	   
	        //'data-validation'=>"required"    
	      ))?>
	      <label><?php echo t("Password")?></label>
	   </div>
    </div>
    
     <div class="col s6">
        <div class="input-field">
	      <?php echo CHtml::passwordField('cpassword',
	      ''
	      ,
	       array(
	        'class'=>"validate",	   
	        //'data-validation'=>"required"    
	      ))?>
	      <label><?php echo t("Confirm Password")?></label>
	   </div>
    </div> 
  
  </div>
  
    <div class="card-action" style="margin-top:20px;">
     <button class="btn waves-effect waves-light" type="submit" name="action">
       <?php echo t("Submit")?>
     </button>
     </div>
     
  </form>
  
 </div> <!--card-content-->
</div> <!--card-->