<?php

class ScriptManageUser {

    public static function scripts() {
        $ajaxurl = Yii::app()->getBaseUrl(true) . '/ajaxUser';
        $site_url = Yii::app()->getBaseUrl(true) . '/';
        $home_url = Yii::app()->getBaseUrl(true) . '/user';
        $website_url = Yii::app()->getBaseUrl(true);

        Yii::app()->clientScript->scriptMap = array(
            'jquery.js' => false,
            'jquery.min.js' => false
        );

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

        $default_country = Yii::app()->functions->getOption('drv_default_location', '');
        if (empty($default_country)) {
            $default_country = 'US';
        }


        /** START Set general settings */
        $cs->registerScript(
                'default_country', "var default_country='$default_country';", CClientScript::POS_HEAD
        );

        /** END Set general settings */
        /* JS FILE */
        Yii::app()->clientScript->registerScriptFile(
                //Yii::app()->baseUrl . '/assets/jquery-1.10.2.min.js',
                Yii::app()->baseUrl . '/assets/jQuery-3.4.1.js',
                //Yii::app()->baseUrl . '/assets/jquery.min.js',
                CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/dashboard/vendors/popper/dist/umd/popper.js', CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                //Yii::app()->baseUrl . '/assets/bootstrap/js/bootstrap.min.js', CClientScript::POS_END
                Yii::app()->baseUrl . '/assets/dashboard/vendors/bootstrap/dist/js/bootstrap.min.js', CClientScript::POS_END
        );



        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/dashboard/vendors/metisMenu/dist/metisMenu.min.js', CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/dashboard/vendors/jquery-slimscroll/jquery.slimscroll.min.js', CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/dashboard/vendors/chart.js/dist/Chart.min.js', CClientScript::POS_END
        );



        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/dashboard/js/app.min.js', CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/chosen/chosen.jquery.min.js', CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/noty-2.3.7/js/noty/packaged/jquery.noty.packaged.min.js', CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/DataTables/jquery.dataTables.min.js', CClientScript::POS_END
        );
        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/DataTables/fnReloadAjax.js', CClientScript::POS_END
        );
        Yii::app()->clientScript->registerScriptFile(
                'https://cdn.datatables.net/responsive/2.2.5/js/dataTables.responsive.js', CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/jquery.sticky2.js', CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/SimpleAjaxUploader.min.js', CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/summernote/summernote.min.js', CClientScript::POS_END
        );


        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/markercluster.js?ver=1.0', CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/form-validator/jquery.form-validator.min.js', CClientScript::POS_END
        );

//        Yii::app()->clientScript->registerScriptFile(
//               // Yii::app()->baseUrl . '/assets/intel/build/js/intlTelInput.js?ver=2.1.5', CClientScript::POS_END
//        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/nprogress/nprogress.js', CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/datetimepicker/jquery.datetimepicker.full.min.js', CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/moment.js', CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/js-date-format.min.js', CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/switch/bootstrap-switch.min.js', CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                "//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js", CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/jplayer/jquery.jplayer.min.js', CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/js.kookie.js', CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/raty/jquery.raty.js', CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/sweetalert/sweetalert2.js', CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/user.js?ver=1.0', CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/busqueda-avanzada/js/extention/choices.js', CClientScript::POS_END
        );

        /* CSS FILE */
        $baseUrl = Yii::app()->baseUrl . "";
        $cs = Yii::app()->getClientScript();
        //$cs->registerCssFile("//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css");		
        //$cs->registerCssFile($baseUrl . "/assets/bootstrap/css/bootstrap.min.css");
        $cs->registerCssFile($baseUrl . "/assets/dashboard/vendors/bootstrap/dist/css/bootstrap.min.css");

        $cs->registerCssFile($baseUrl . "/assets/chosen/chosen.min.css");
        $cs->registerCssFile($baseUrl . "/assets/animate.css");
        $cs->registerCssFile($baseUrl . "/assets/summernote/summernote.css");
        $cs->registerCssFile("//cdn.datatables.net/1.10.9/css/jquery.dataTables.min.css");
        $cs->registerCssFile("//cdn.datatables.net/responsive/2.2.5/css/responsive.dataTables.css");
        //$cs->registerCssFile($baseUrl."/assets/DataTables");	
        // $cs->registerCssFile("//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css");
        $cs->registerCssFile($baseUrl . "/assets/dashboard/vendors/font-awesome/css/font-awesome.min.css");
        $cs->registerCssFile($baseUrl . "/assets/chosen/chosen.min.css");
        $cs->registerCssFile($baseUrl . "/assets/dashboard/vendors/themify-icons/css/themify-icons.css");
        $cs->registerCssFile($baseUrl . "/assets/dashboard/css/main.min.css");
        $cs->registerCssFile("//code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css");

        $cs->registerCssFile($baseUrl . "/assets/intel/build/css/intlTelInput.css");
        $cs->registerCssFile($baseUrl . "/assets/nprogress/nprogress.css");
        $cs->registerCssFile($baseUrl . "/assets/datetimepicker/jquery.datetimepicker.css");
        $cs->registerCssFile($baseUrl . "/assets/switch/bootstrap-switch.min.css");
        $cs->registerCssFile("//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css");

        $cs->registerCssFile($baseUrl . "/assets/raty/jquery.raty.css");

        $cs->registerCssFile($baseUrl . "/assets/user.css?ver=1.0");

        $cs->registerCssFile($baseUrl . "/assets/app-responsive.css?ver=1.0");

        $cs->registerCssFile($baseUrl . "/assets/sweetalert/sweetalert2.css");
//
//        $cs->registerCssFile($baseUrl . "/assets/menu/css/style.css");

        $cs->registerCssFile($baseUrl . "/assets/busqueda-avanzada/css/main.css");
    }

}

/*END CLASS*/