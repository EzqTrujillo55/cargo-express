<?php

if (!isset($_SESSION)) {
    session_start();
}

class UserController extends CController {

    public $layout = 'layout';
    public $body_class = '';

    public function init() {
        // set website timezone
        $website_timezone = Yii::app()->functions->getOptionAdmin("website_timezone");
        if (!empty($website_timezone)) {
            Yii::app()->timeZone = $website_timezone;
        }

        $customer_timezone = getOption(Driver::getUserId(), 'customer_timezone');
        if (!empty($customer_timezone)) {
            Yii::app()->timeZone = $customer_timezone;
        }

        /* dump(Yii::app()->timeZone);
          dump(date('c'));
          die(); */

        if (isset($_GET['lang'])) {
            Yii::app()->language = $_GET['lang'];
        }
    }

    public function beforeAction($action) {
        /* if (Yii::app()->controller->module->require_login){
          if(! DriverModule::islogin() ){
          $this->redirect(Yii::app()->createUrl('/admin/noaccess'));
          Yii::app()->end();
          }
          } */
        $action_name = $action->id;
        $accept_controller = array('login', 'ajax', 'resetpassword');
        if (!Driver::isloginCliente()) {
            if (!in_array($action_name, $accept_controller)) {
                $this->redirect(Yii::app()->createUrl('/user/login'));
            }
        }


        /* check user status */
        $status = Driver::getUserStatus();
        if ($status == "expired") {
            $this->redirect(Yii::app()->createUrl('/user/login'));
            Yii::app()->end();
//            if ($action_name != "profile") {
//                if ($action_name != "logout") {
//                    $this->redirect(Yii::app()->createUrl('/user/profile', array(
//                                'tabs' => 2
//                    )));
//                    Yii::app()->end();
//                }
//            }
        }

        ScriptManageUser::scripts();

        $cs = Yii::app()->getClientScript();
        $jslang = json_encode(Driver::jsLang());
        $cs->registerScript(
                'jslang', "var jslang=$jslang;", CClientScript::POS_HEAD
        );

        $js_lang_validator = Yii::app()->functions->jsLanguageValidator();
        $js_lang = Yii::app()->functions->jsLanguageAdmin();
        $cs->registerScript(
                'jsLanguageValidator', 'var jsLanguageValidator = ' . json_encode($js_lang_validator) . '
		  ', CClientScript::POS_HEAD
        );
        $cs->registerScript(
                'js_lang', 'var js_lang = ' . json_encode($js_lang) . ';
		  ', CClientScript::POS_HEAD
        );

        $cs->registerScript(
                'account_status', "var account_status='$status';", CClientScript::POS_HEAD
        );

        $language = Yii::app()->language;
        $cs->registerScript(
                'language', "var language='$language';", CClientScript::POS_HEAD
        );

        $calendar_language = getOption(Driver::getUserId(), 'calendar_language');
        $cs->registerScript(
                'calendar_language', "var calendar_language='$calendar_language';", CClientScript::POS_HEAD
        );

        return true;
    }

    public function actionLogin() {

        $encryption_type = Yii::app()->params->encryption_type;
        if (empty($encryption_type)) {
            $encryption_type = 'yii';
        }

        if (Driver::isloginCliente()) {
            $this->redirect(Yii::app()->createUrl('/user'));
            Yii::app()->end();
        }

        $this->body_class = 'login-body';

        /* unset(Yii::app()->request->cookies['kt_username']);
          unset(Yii::app()->request->cookies['kt_password']); */

        $kt_username = isset(Yii::app()->request->cookies['kt_usernameUser']) ? Yii::app()->request->cookies['kt_usernameUser']->value : '';
        $kt_password = isset(Yii::app()->request->cookies['kt_passwordUser']) ? Yii::app()->request->cookies['kt_passwordUser']->value : '';

        if ($encryption_type == "yii") {
            if (!empty($kt_password) && !empty($kt_username)) {
                $kt_password = Yii::app()->securityManager->decrypt($kt_password);
            }
        } else
            $kt_password = '';

        $this->render('login', array(
            'email' => $kt_username,
            'password' => $kt_password
        ));
    }

    public function actionLogout() {
        unset($_SESSION['xpress']);
        $this->redirect(Yii::app()->createUrl('/user/login'));
    }

    public function actionIndex() {
        $this->body_class = "dashboard";
        $this->render('dashboard');
    }

    public function actionDashboard() {
        $this->body_class = "dashboard";
        $this->render('dashboard');
    }

    public function actionMisOrdenes() {
        $this->body_class = "page-single";
        $this->render('mis-ordenes');
    }

    public function actionMisContactos() {
        $this->body_class = "page-single";
        $this->render('mis-contactos');
    }

    public function actionComprobante() {
        $this->body_class = "page-single";

        $orden_id = isset($_GET['orden_id']) ? $_GET['orden_id'] : '';
        if (!empty($orden_id)) {

            $orden_id = isset($_GET['orden_id']) ? $_GET['orden_id'] : '';
            $this->render('comprobante', array(
                'orden_id' => $orden_id,
            ));
        } else {
            $this->render('error', array(
                'msg' => t("No hay un id de orden válido")
            ));
        }
    }

    public function actionComprobantes() {
        $this->body_class = "page-single";
        $params_data = array();
        foreach ($_GET as $name => $value) {
            if (strpos($name, 'seleccionado_') !== false) {
                array_push($params_data, $value);
            }
        }

        $this->render('comprobantes', array(
            'data' => $params_data,
        ));
    }

    public function actionSettings() {

        $country_list = require_once('CountryCode.php');
        $this->body_class = 'settings-page';

        if (Driver::getUserType() == "merchant") {
            $this->render('error', array(
                'msg' => Driver::t("Sorry but you don't have access to this page")
            ));
        } else {

            $language_list = getOptionA('language_list');
            if (!empty($language_list)) {
                $language_list = json_decode($language_list, true);
            }
            $action_name = Yii::app()->controller->action->id;

            if (is_array($language_list) && count($language_list) >= 1) {
                array_unshift($language_list, t("Please select"));
            }

            $this->render('settings', array(
                'country_list' => $country_list,
                'language_list' => $language_list,
                'action_name' => $action_name
            ));
        }
    }

    public function actionlanguage() {
        $lang = Driver::availableLanguages();
        $dictionary = require_once('MobileTranslation.php');

        $mobile_dictionary = getOptionA('driver_mobile_dictionary');
        if (!empty($mobile_dictionary)) {
            $mobile_dictionary = json_decode($mobile_dictionary, true);
        } else
            $mobile_dictionary = false;

        $this->render('language', array(
            'lang' => $lang,
            'dictionary' => $dictionary,
            'mobile_dictionary' => $mobile_dictionary
        ));
    }

    public function actionNotifications() {
        $this->body_class = "page-single";
        $this->render('notifications');
    }

    public function actionPushlogs() {
        $this->body_class = "page-single";
        $this->render('push-logs', array(
            'broadcast_id' => isset($_GET['broadcast_id']) ? $_GET['broadcast_id'] : ""
        ));
    }

    public function actionReports() {
        $this->body_class = "page-single";
        $cs = Yii::app()->getClientScript();

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/amcharts/amcharts.js'
                , CClientScript::POS_END);

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/amcharts/serial.js', CClientScript::POS_END);

        Yii::app()->clientScript->registerScriptFile(
                Yii::app()->baseUrl . '/assets/amcharts/themes/light.js', CClientScript::POS_END);



        $start = date('Y-m-d', strtotime("-7 day"));
        $end = date("Y-m-d", strtotime("+1 day"));

        $this->render('reports', array(
            'team_list' => $team_list,
            'all_driver' => $all_driver,
            'start_date' => $start,
            'end_date' => $end
        ));
    }

    public function actionResetPassword() {
        $this->body_class = 'login-body';
        $this->render('resetpassword', array(
            'hash' => isset($_GET['hash']) ? $_GET['hash'] : ''
        ));
    }

    public function actionsetlang() {
        if (!empty($_GET['action'])) {
            $url = Yii::app()->createUrl("app/" . $_GET['action'], array(
                'lang' => $_GET['lang']
            ));
        } else {
            $url = Yii::app()->createUrl("app/dashboard", array(
                'lang' => $_GET['lang']
            ));
        }
        $this->redirect($url);
    }

}

/* end class*/