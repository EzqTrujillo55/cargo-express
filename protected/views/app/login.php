

<div class="container">
    <div class="login-wrap rounded">
        <img src="<?php echo Yii::app()->baseUrl . '/assets/images-front/logo.png'; ?>" alt="" class="" height="55">

        <form id="frm" class="frm rounded3" method="POST" onsubmit="return false;">
            <div class="inner">
                <?php echo CHtml::hiddenField('action', 'login') ?>
                <div>
                    <?php
                    echo CHtml::textField('email', $email_address
                            , array(
                        'placeholder' => Driver::t("Email"),
                        'class' => "lightblue-fields rounded",
                        'required' => true
                    ));
                    ?>
                </div>

                <div class="top20">
                    <?php
                    echo CHtml::passwordField('password', $password
                            , array(
                        'placeholder' => Driver::t("Password"),
                        'class' => "lightblue-fields rounded",
                        'required' => true
                    ));
                    ?>
                </div>

                <div class="top20">
                    <button class="yellow-button large rounded3 relative">
                        <?php echo Driver::t("Ingresar") ?> <i class="ion-ios-arrow-thin-right"></i>
                    </button>
                </div>

            </div> <!--inner-->

            <div class="sub-section">
                <div class="row">
                    <div class="col-md-6">
                        <?php echo CHtml::checkBox('remember', true, array('value' => 1)) ?>
                        <?php echo t("Recordarme") ?>
                    </div> <!--col-->
                    <div class="col-md-6 text-right">
                        <a href="#no" class="show-forgot-pass"><?php echo t("Olvidó su password") ?>?</a>
                    </div> <!--col-->
                </div> <!--row-->
            </div> <!--sub-section-->

        </form> <!--login-->

        <form id="frm-forgotpass" class="frm rounded3" method="POST" onsubmit="return false;">
            <?php echo CHtml::hiddenField('action', 'forgotPassword') ?>
            <div class="inner">

                <p class="center">
                    <?php echo t("Ingrese su email y le será enviado un link para resetear su password") ?>
                </p>

                <div class="top20">
                    <?php
                    echo CHtml::textField('email', '', array(
                        'placeholder' => Driver::t("Email"),
                        'class' => "lightblue-fields rounded",
                        'required' => true
                    ));
                    ?>
                </div>  

                <div class="top20">
                    <button class="yellow-button large rounded3 relative">
                        <?php echo Driver::t("Enviar") ?> <i class="ion-ios-arrow-thin-right"></i>
                    </button>
                </div>	    
            </div> <!--inner-->

            <div class="sub-section">
                <a href="#no" class="show-login"><?php echo t("Back") ?></a>
            </div>

        </form> <!--forgot pass-->

        <hr/>

    </div> <!--login-wrap-->
</div> <!--container-->