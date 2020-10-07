<?php $this->renderPartial('/layouts/header');?>

<body class="<?php echo isset($this->body_class)?$this->body_class:'';?>">

<?php $this->renderPartial('/front/top-menu');?>

<?php echo $content;?>

<?php $this->renderPartial('/front/footer',array(
  'custom_footer'=>getOptionA('website_custom_footer'),
  'company_address'=>getOptionA('company_address'),
  'contact_number'=>getOptionA('contact_number'),
  'email_address'=>getOptionA('email_address'),
  'website_default_country'=>getOptionA('website_default_country'),
  'follow_fb'=>getOptionA('follow_fb'),
  'follow_google'=>getOptionA('follow_google'),
  'follow_twitter'=>getOptionA('follow_twitter'),
  'language'=>Yii::app()->language,
  'action_name'=>Yii::app()->controller->action->id,
  'language_list'=>getOptionA('language_list')
));?>

</body>
<?php $this->renderPartial('/layouts/footer');?>