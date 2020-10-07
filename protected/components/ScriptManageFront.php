<?php

class ScriptManageFront {

    public static function scripts() {
        $ajaxurl = Yii::app()->baseUrl . '/ajaxfront';
        $site_url = Yii::app()->baseUrl . '/';
        $home_url = Yii::app()->baseUrl . '/front';

        $website_url = websiteUrl();

//        Yii::app()->clientScript->scriptMap = array(
//            'jquery.js' => false,
//            'jquery.min.js' => false
//        );

        $cs = Yii::app()->getClientScript();
        $cs->registerScript(
                'ajaxurl', "var ajax_url='$ajaxurl';", CClientScript::POS_HEAD
        );
        $cs->registerScript(
                'siteurl', "var site_url='$site_url';", CClientScript::POS_HEAD
        );
        $cs->registerScript(
                'homeurl', "var home_url='$home_url';", CClientScript::POS_HEAD
        );
        $cs->registerScript(
                'websiteurl', "var website_url='$website_url';", CClientScript::POS_HEAD
        );


        $default_country = getOptionA('website_default_country');
        if (empty($default_country)) {
            $default_country = 'US';
        }

        $cs->registerScript(
                'default_country', "var default_country='$default_country';", CClientScript::POS_HEAD
        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/front/js/vendor/modernizr-3.5.0.min.js', CClientScript::POS_END
        );
        Yii::app()->clientScript->registerScriptFile(
                //Yii::app()->baseUrl . '/assets/jquery-1.10.2.min.js',
                Yii::app()->baseUrl . '/assets/jQuery-3.4.1.js',
                //Yii::app()->baseUrl . '/assets/jquery.min.js', 
                CClientScript::POS_END
        );
        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/front/js/popper.min.js', CClientScript::POS_END
        );
        Yii::app()->clientScript->registerScriptFile(
                //Yii::app()->baseUrl . '/assets/bootstrap/js/bootstrap.min.js', CClientScript::POS_END
                Yii::app()->baseUrl . '/assets/dashboard/vendors/bootstrap/dist/js/bootstrap.min.js', CClientScript::POS_END
        );
        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/front/js/jquery.slicknav.min.js', CClientScript::POS_END
        );
        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/front/js/owl.carousel.min.js', CClientScript::POS_END
        );
        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/front/js/slick.min.js', CClientScript::POS_END
        );
        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/front/js/wow.min.js', CClientScript::POS_END
        );
        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/front/js/animated.headline.js', CClientScript::POS_END
        );
        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/front/js/jquery.magnific-popup.js', CClientScript::POS_END
        );
        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/front/js/jquery.nice-select.min.js', CClientScript::POS_END
        );
        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/front/js/jquery.sticky.js', CClientScript::POS_END
        );
        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/front/js/jquery.form.js', CClientScript::POS_END
        );
        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/front/js/jquery.validate.min.js', CClientScript::POS_END
        );
        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/front/js/plugins.js', CClientScript::POS_END
        );
        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/front/js/main.js', CClientScript::POS_END
        );



        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/front.js', CClientScript::POS_END
        );


        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/form-validator/jquery.form-validator.min.js', CClientScript::POS_END
        );


//        Yii::app()->clientScript->registerScriptFile(
//                Yii::app()->baseUrl . '/assets/intel/build/js/intlTelInput.js?ver=2.1.5', CClientScript::POS_END
//        );
//        Yii::app()->clientScript->registerScriptFile(
//                Yii::app()->baseUrl . '/assets/front.js?ver=1.0', CClientScript::POS_END
//        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/sweetalert/sweetalert2.js', CClientScript::POS_END
        );

        /* CSS FILE */
        $baseUrl = Yii::app()->baseUrl . "";
        $cs = Yii::app()->getClientScript();
        // $cs->registerCssFile($baseUrl . "/assets/front/css/bootstrap.min.css");
        $cs->registerCssFile($baseUrl . "/assets/dashboard/vendors/bootstrap/dist/css/bootstrap.min.css");

        $cs->registerCssFile($baseUrl . "/assets/front/css/owl.carousel.min.css");
        $cs->registerCssFile($baseUrl . "/assets/front/css/slicknav.css");
        $cs->registerCssFile($baseUrl . "/assets/front/css/flaticon.css");
        $cs->registerCssFile($baseUrl . "/assets/front/css/animate.min.css");
        $cs->registerCssFile($baseUrl . "/assets/front/css/magnific-popup.css");
        $cs->registerCssFile($baseUrl . "/assets/dashboard/vendors/font-awesome/css/font-awesome.min.css");
        $cs->registerCssFile($baseUrl . "/assets/front/css/themify-icons.css");
        $cs->registerCssFile($baseUrl . "/assets/front/css/slick.css");
        $cs->registerCssFile($baseUrl . "/assets/front/css/nice-select.css");
        $cs->registerCssFile($baseUrl . "/assets/front/css/style.css");
        $cs->registerCssFile("//code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css");
        $cs->registerCssFile($baseUrl . "/assets/front.css?ver=1.0");
        $cs->registerCssFile($baseUrl . "/assets/sweetalert/sweetalert2.css");
    }

}

/*END CLASS*/