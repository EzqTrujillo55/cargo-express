<?php

class ScriptManagerAdmin {

    public static function scripts() {
        $ajaxurl = Yii::app()->baseUrl . '/ajaxadmin';
        $site_url = Yii::app()->baseUrl . '/';
        $home_url = Yii::app()->baseUrl . '/admin';

        Yii::app()->clientScript->scriptMap = array(
            'jquery.js' => false,
            'jquery.min.js' => false
        );

        $cs = Yii::app()->getClientScript();
        $cs->registerScript(
                'ajaxurl', "var ajax_url='$ajaxurl';", CClientScript::POS_HEAD
        );
        $cs->registerScript(
                'site_url', "var site_url='$site_url';", CClientScript::POS_HEAD
        );
        $cs->registerScript(
                'home_url', "var home_url='$home_url';", CClientScript::POS_HEAD
        );


        $default_country = getOptionA('website_default_country');
        if (empty($default_country)) {
            $default_country = 'US';
        }

        $cs->registerScript(
                'default_country', "var default_country='$default_country';", CClientScript::POS_HEAD
        );

        /* JS FILE */
        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/jquery-1.10.2.min.js',
                //Yii::app()->baseUrl . '/assets/jQuery-3.4.1.js', 
                CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                "//code.jquery.com/ui/1.12.1/jquery-ui.js", CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/bootstrap/js/bootstrap.min.js', CClientScript::POS_END
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
                Yii::app()->baseUrl . '/assets/jquery.sticky2.js', CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/SimpleAjaxUploader.min.js', CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/summernote/summernote.min.js', CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/form-validator/jquery.form-validator.min.js', CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/switch/bootstrap-switch.min.js', CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                "//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js", CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/js.kookie.js', CClientScript::POS_END
        );

        /* Yii::app()->clientScript->registerScriptFile(
          "//cdnjs.cloudflare.com/ajax/libs/materialize/0.97.6/js/materialize.min.js",
          CClientScript::POS_END
          ); */

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/intel/build/js/intlTelInput.js?ver=2.1.5', CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/materialize/js/materialize.min.js', CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/sweetalert/sweetalert2.js', CClientScript::POS_END
        );

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/admin.js?ver=1.0', CClientScript::POS_END
        );

        /* CSS FILE */
        $baseUrl = Yii::app()->baseUrl . "";
        $cs = Yii::app()->getClientScript();
        //$cs->registerCssFile($baseUrl."/assets/bootstrap/css/bootstrap.min.css");		

        $cs->registerCssFile($baseUrl . "/assets/chosen/chosen.min.css");
        $cs->registerCssFile($baseUrl . "/assets/animate.css");
        $cs->registerCssFile($baseUrl . "/assets/summernote/summernote.css");
        $cs->registerCssFile($baseUrl . "/assets/DataTables/jquery.dataTables.min.css");
        //$cs->registerCssFile("//cdn.datatables.net/1.10.9/css/jquery.dataTables.min.css");		
        //$cs->registerCssFile($baseUrl."/assets/DataTables");	
        $cs->registerCssFile("//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css");
        $cs->registerCssFile("//code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css");

        $cs->registerCssFile($baseUrl . "/assets/intel/build/css/intlTelInput.css");
        $cs->registerCssFile($baseUrl . "/assets/nprogress/nprogress.css");
        $cs->registerCssFile($baseUrl . "/assets/datetimepicker/jquery.datetimepicker.css");
        $cs->registerCssFile($baseUrl . "/assets/switch/bootstrap-switch.min.css");

        $cs->registerCssFile("//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css");

        //$cs->registerCssFile("//cdnjs.cloudflare.com/ajax/libs/materialize/0.97.6/css/materialize.min.css");
        $cs->registerCssFile($baseUrl . "/assets/materialize/css/materialize.min.0.97.6.css");
        $cs->registerCssFile("//fonts.googleapis.com/icon?family=Material+Icons");

        $cs->registerCssFile($baseUrl . "/assets/sweetalert/sweetalert2.css");
        
        $cs->registerCssFile($baseUrl . "/assets/admin.css?ver=1.0");
    }

}

/*END CLASS*/