<?php $this->renderPartial('/layouts/header');?>

<body class="<?php echo isset($this->body_class)?$this->body_class:'';?>">

<?php if ($this->is_newupdate):?>
<div style="background:#f3989b;padding:5px;color:#fff;text-align:center;">
<?php echo t("Your database needs update")?> 
<a href="<?php echo Yii::app()->createUrl('/update/index')?>" target="_blank"><?php echo t("click here")?></a> 
<?php echo t("to update your database")?>
</div>
<?php endif;?>

<?php if (AdminFunctions::islogin()):?>
<?php $this->renderPartial('/admin/menu',array(
 'language'=>getOptionA('language_list'),
 'current_lang'=>Yii::app()->language
));?>
<?php endif;?>


<?php if (AdminFunctions::islogin()):?>
<div class="container">
  <div class="section">
    <?php echo $content;?>
  </div>
</div>
<?php else :?>
<?php echo $content;?>
<?php endif;?>

</body>

<?php $this->renderPartial('/layouts/admin_footer');?>