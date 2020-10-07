<?php $this->renderPartial('/layouts/header');?>

<body class="<?php echo isset($this->body_class)?$this->body_class:'';?>">

<?php echo $content;?>

</body>
<?php $this->renderPartial('/layouts/footer');?>