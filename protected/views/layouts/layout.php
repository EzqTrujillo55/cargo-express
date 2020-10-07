<?php $this->renderPartial('/layouts/header');?>

<body class="fixed-navbar <?php echo isset($this->body_class)?$this->body_class:'';?>">
 <div class="page-wrapper">
<?php echo $content;?>
 </div>

 <!-- BEGIN PAGA BACKDROPS
    <div class="sidenav-backdrop backdrop"></div>
    <div class="preloader-backdrop">
        <div class="page-preloader">Loading</div>
    </div>
    <!-- END PAGA BACKDROPS-->
</body>
<?php $this->renderPartial('/layouts/footer');?>