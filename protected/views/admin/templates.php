
<div class="card">
 <div class="card-content">
 
  <h5><?php echo t("Templates")?></h5>
  
  <div class="top30"></div>
  
  <form id="frm" method="POST" onsubmit="return false;">
   <?php echo CHtml::hiddenField('action','saveTemplates')?>
  
  <ul class="collapsible">
     <li>
      <div class="collapsible-header"><i class="material-icons">reorder</i>
       <?php echo t("Welcome Signup")?>
      </div>
      <div class="collapsible-body" style="padding:20px;">
      
      <div style="padding:0 20px;">     
      
      <div class="input-field">
	      <?php echo CHtml::textField('welcome_tpl_subject',
	      getOptionA('welcome_tpl_subject')
	      ,array('class'=>"validate",	      
	      ))?>
	      <label for="welcome_tpl_subject"><?php echo t("Subject")?></label>      
      </div> 
      
      <div class="input-field">
	      <?php echo CHtml::textArea('welcome_tpl',
	      getOptionA('welcome_tpl')
	      ,array('class'=>"materialize-textarea",	      
	      ))?>
	      <label for="welcome_tpl"><?php echo t("Welcome Templates")?></label>      
      </div>
      </div>
            
      <p><?php echo t("Available tags")?>:</p>
      <div style="padding:0 20px 20px;">      
         <div class="chip">[first_name]</div>
         <div class="chip">[last_name]</div>
         <div class="chip">[username]</div>
         <div class="chip">[login_link]</div>
         <div class="chip">[company_name]</div>  
      </div>
      
      </div> <!--col-->
    </li>
    
     <li>
      <div class="collapsible-header"><i class="material-icons">reorder</i>
       <?php echo t("Welcome Users Signup")?>
      </div>
      <div class="collapsible-body" style="padding:20px;">
      
      <div style="padding:0 20px;">     
      
      <div class="input-field">
	      <?php echo CHtml::textField('welcome_tpl_subject',
	      getOptionA('welcome_tpl_subject')
	      ,array('class'=>"validate",	      
	      ))?>
	      <label for="welcome_tpl_subject"><?php echo t("Subject")?></label>      
      </div> 
      
      <div class="input-field">
	      <?php echo CHtml::textArea('welcome_user_tpl',
	      getOptionA('welcome_user_tpl')
	      ,array('class'=>"materialize-textarea",	      
	      ))?>
	      <label for="welcome_user_tpl"><?php echo t("Welcome Templates")?></label>      
      </div>
      </div>
            
      <p><?php echo t("Available tags")?>:</p>
      <div style="padding:0 20px 20px;">      
         <div class="chip">[nombres]</div>
         <div class="chip">[password]</div>
         <div class="chip">[mail]</div>
         <div class="chip">[login_link]</div>
      </div>
      
      </div> <!--col-->
    </li>
    
    
    
    <li>
      <div class="collapsible-header"><i class="material-icons">reorder</i>
       <?php echo t("Approved Account")?>
      </div>
      <div class="collapsible-body" style="padding:20px;">
      
      <div style="padding:0 20px;">     
      
      <div class="input-field">
	      <?php echo CHtml::textField('approved_tpl_subject',
	      getOptionA('approved_tpl_subject')
	      ,array('class'=>"validate",	      
	      ))?>
	      <label for="approved_tpl_subject"><?php echo t("Subject")?></label>      
      </div> 
      
      <div class="input-field">
	      <?php echo CHtml::textArea('approved_tpl',
	      getOptionA('approved_tpl')
	      ,array('class'=>"materialize-textarea",	      
	      ))?>
	      <label for="approved_tpl"><?php echo t("Approved Templates")?></label>      
      </div>
      </div>
            
      <p><?php echo t("Available tags")?>:</p>
      <div style="padding:0 20px 20px;">      
         <div class="chip">[first_name]</div>
         <div class="chip">[last_name]</div>
         <div class="chip">[username]</div>
         <div class="chip">[login_link]</div>
         <div class="chip">[company_name]</div>  
      </div>
      
      </div> <!--col-->
    </li>
    
    <li>
      <div class="collapsible-header"><i class="material-icons">reorder</i>
        <?php echo t("Forgot password")?>
      </div>
      <div class="collapsible-body" style="padding:20px;">
      
      <div style="padding:0 20px;">     
      
      <div class="input-field">
	      <?php echo CHtml::textField('forgot_password_subject',
	      getOptionA('forgot_password_subject')
	      ,array('class'=>"validate",	      
	      ))?>
	      <label for="forgot_password_subject"><?php echo t("Subject")?></label>      
      </div> 
      
      <div class="input-field">
	      <?php echo CHtml::textArea('forgot_password_tpl',
	      getOptionA('forgot_password_tpl')
	      ,array('class'=>"materialize-textarea",	      
	      ))?>
	      <label for="forgot_password_tpl"><?php echo t("Template")?></label>      
      </div>
      </div>
      
       <p><?php echo t("Available tags")?>:</p>
      <div style="padding:0 20px 20px;">      
         <div class="chip">[first_name]</div>
         <div class="chip">[last_name]</div>         
         <div class="chip">[username]</div>                 
         <div class="chip">[verification_code]</div>        
         <div class="chip">[company_name]</div>  
      </div>
      
      </div>
    </li>
    
    
     <li>
      <div class="collapsible-header"><i class="material-icons">reorder</i>
        <?php echo t("Signup Verification")?> - <?php echo t("SMS")?>
      </div>
      <div class="collapsible-body" style="padding:20px;">
      
       <div style="padding:0 20px;">     
      <div class="input-field">
	      <?php echo CHtml::textArea('signup_tpl_sms',
	      getOptionA('signup_tpl_sms')
	      ,array('class'=>"materialize-textarea",	      
	      ))?>
	      <label for="signup_tpl_sms"><?php echo t("Signup Verification")?></label>      
      </div>
      </div>
      
       <p><?php echo t("Available tags")?>:</p>
      <div style="padding:0 20px 20px;">      
         <div class="chip">[first_name]</div>
         <div class="chip">[last_name]</div>         
         <div class="chip">[verification_code]</div>         
         <div class="chip">[company_name]</div>  
      </div>
      
      </div>
    </li>
    
    <li>
      <div class="collapsible-header"><i class="material-icons">reorder</i>
        <?php echo t("Signup Verification")?> - <?php echo t("Email")?>
      </div>
      <div class="collapsible-body" style="padding:20px;">
      
      <div style="padding:0 20px;">     
      
      <div class="input-field">
	      <?php echo CHtml::textField('signup_tpl_email_subject',
	      getOptionA('signup_tpl_email_subject')
	      ,array('class'=>"validate",	      
	      ))?>
	      <label for="signup_tpl_email_subject"><?php echo t("Subject")?></label>      
      </div> 
       
      <div class="input-field">
	      <?php echo CHtml::textArea('signup_tpl_email',
	      getOptionA('signup_tpl_email')
	      ,array('class'=>"materialize-textarea",	      
	      ))?>
	      <label for="signup_tpl_email"><?php echo t("Template")?></label>      
      </div>
      </div>
      
       <p><?php echo t("Available tags")?>:</p>
      <div style="padding:0 20px 20px;">      
         <div class="chip">[first_name]</div>
         <div class="chip">[last_name]</div>         
         <div class="chip">[verification_code]</div>    
         <div class="chip">[company_name]</div>       
      </div>
      
      </div>
    </li>
    
    
  </ul>
  
   <div class="card-action" style="margin-top:20px;">
     <button class="btn waves-effect waves-light" type="submit" name="action">
       <?php echo t("Save Templates")?>
     </button>
   </div>
   
    </form>  
 
    
 </div>
</div> 