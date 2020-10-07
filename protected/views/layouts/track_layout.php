<?php $this->renderPartial('/layouts/header');?>

<body class="<?php echo isset($this->body_class)?$this->body_class:'';?>">

<?php echo $content;?>

<div class="main-preloader">
   <div class="inner">
   <div class="ploader"></div>
   </div>
</div> 

</body>
<?php $this->renderPartial('/layouts/footer');?>