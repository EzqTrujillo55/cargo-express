<!-- Header Start -->
<div class="header-area">
    <div class="main-header ">
        <div class="header-top d-none d-lg-block">
            <div class="container">
                <div class="col-xl-12">
                    <div class="row d-flex justify-content-between align-items-center">
                        <div class="header-info-left">
                            <ul>     
                                <li>Teléfonos: 0999979075 02-2443178</li>
                                <li>Email: supervisor@web-cargoxpress.com</li>
                            </ul>
                        </div>
                        <div class="header-info-right">
                            <ul class="header-social">    
                                <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                <li><a href="https://www.facebook.com/cargoxpress2020"><i class="fab fa-facebook-f"></i></a></li>
                                <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-bottom  header-sticky">
            <div class="container">
                <div class="row align-items-center">
                    <!-- Logo -->
                    <div class="col-xl-2 col-lg-2">
                        <div class="logo">
                            <a href="index.php"><img height="100" width="auto" src="<?php echo Yii::app()->request->baseUrl; ?>/assets/front/img/logo/logo_largo.png" alt=""/></a>
                        </div>
                    </div>
                    <div class="col-xl-10 col-lg-10">
                        <div class="menu-wrapper  d-flex align-items-center justify-content-end">
                            <!-- Main-menu -->
                            <div class="main-menu d-none d-lg-block">
                                <nav> 
                                    <ul id="navigation">                                                                                          
                                        <li><a href="index.php">Home</a></li>

                                    </ul>
                                </nav>
                            </div>
                            <!-- Header-btn -->
                            <div class="header-right-btn d-none d-lg-block ml-20">
                                <a href="<?php
                                echo Yii::app()->createUrl('/user/login', array())
                                ?>" class="btn btn-warning">  <i class="fa fa-user"></i>Ingreso Clientes</a>
                                <a href="<?php
                                echo Yii::app()->createUrl('/front/registro', array())
                                ?>" class="btn btn-info"><i class="fa fa-user-plus"></i>Afíliate</a>
                            </div>
                        </div>
                    </div> 
                    <!-- Mobile Menu -->
                    <div class="col-12">
                        <div class="mobile_menu d-block d-lg-none"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Header End -->