<?php

if (!isset($_SESSION)) {
    session_start();
}

class FrontController extends CController {

    public $layout = 'front_layout';
    public $body_class = '';
    public $action_name = '';

    public function init() {
       

        // set website timezone
        $website_timezone = Yii::app()->functions->getOptionAdmin("website_timezone");
        if (!empty($website_timezone)) {
            Yii::app()->timeZone = $website_timezone;
        }

        if (isset($_GET['lang'])) {
            Yii::app()->language = $_GET['lang'];
        }
    }

    public function beforeAction($action) {
        $action_name = $action->id;
        $this->body_class = "page-$action_name";

        ScriptManageFront::scripts();

        $cs = Yii::app()->getClientScript();
        $jslang = json_encode(AdminFunctions::jsLang());
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

        $language = Yii::app()->language;
        $cs->registerScript(
                'language', "var language='$language';", CClientScript::POS_HEAD
        );

        return true;
    }

    public function actionIndex() {

        $title = getOptionA('home_seo_title');
        $meta = getOptionA('home_seo_meta');
        if (!empty($title)) {
            $this->pageTitle = $title;
        }
        AdminFunctions::setSEO($title, $meta);

        $this->render('index', array(
            'pricing' => '',
            'services' => AdminFunctions::servicesFullList(0, 'published')
        ));
    }

    
    private function includeMaterial() {
        $cs = Yii::app()->getClientScript();
        $baseUrl = Yii::app()->baseUrl . "";
        Yii::app()->clientScript->registerScriptFile(
                "//cdnjs.cloudflare.com/ajax/libs/materialize/0.97.6/js/materialize.min.js", CClientScript::POS_END
        );
        $cs->registerCssFile("//cdnjs.cloudflare.com/ajax/libs/materialize/0.97.6/css/materialize.min.css");
        $cs->registerCssFile("//fonts.googleapis.com/icon?family=Material+Icons");
    }

    public function actionRegistro() {
       
                $this->body_class .= " page-material";
                //$this->includeMaterial();

                $this->render('registro', array(
                    'email_address' => isset($_GET['email']) ? $_GET['email'] : ''
                ));
    }

   
    /* public function missingAction($action_name)
      {
      dump($action_name);
      } */

    public function actionPage() {
        $url = isset($_SERVER['REQUEST_URI']) ? explode("/", $_SERVER['REQUEST_URI']) : false;
        if (is_array($url) && count($url) >= 1) {
            $page_slug = $url[count($url) - 1];
            $page_slug = str_replace('page-', '', $page_slug);
            if (isset($_GET)) {
                $c = strpos($page_slug, '?');
                if (is_numeric($c)) {
                    $page_slug = substr($page_slug, 0, $c);
                }
            }
            //dump($page_slug);
            if ($res = AdminFunctions::getCustomPageByPageSlug($page_slug, 'published')) {
                $this->render('page', array(
                    'data' => $res
                ));
            } else
                $this->render('error', array(
                    'msg' => t("Sorry but we cannot find what you are looking for")
                ));
        } else
            $this->render('error', array(
                'msg' => t("Sorry but we cannot find what you are looking for")
            ));
    }

    public function actionsetlang() {
        if (!empty($_GET['action'])) {
            $url = Yii::app()->createUrl("front/" . $_GET['action'], array(
                'lang' => $_GET['lang']
            ));
        } else {
            $url = Yii::app()->createUrl("front/dashboard", array(
                'lang' => $_GET['lang']
            ));
        }
        $this->redirect($url);
    }

   
   

}

/*end class*/