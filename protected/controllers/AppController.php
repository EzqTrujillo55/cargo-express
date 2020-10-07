<?php

if (!isset($_SESSION)) {
    session_start();
}

class AppController extends CController {

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
        if (!Driver::islogin()) {
            if (!in_array($action_name, $accept_controller)) {
                $this->redirect(Yii::app()->createUrl('/app/login'));
            }
        }


        /* check user status */
        $status = Driver::getUserStatus();
        if ($status == "expired") {
            if ($action_name != "profile") {
                if ($action_name != "logout") {
                    $this->redirect(Yii::app()->createUrl('/app/profile', array(
                                'tabs' => 2
                    )));
                    Yii::app()->end();
                }
            }
        }

        ScriptManager::scripts();

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



        if ($action_name == "index" || $action_name == "contacts") {
            $map_provider = getOptionA('map_provider');
            if ($map_provider == "mapbox") {
                $site_url = Yii::app()->baseUrl . '/';
                Yii::app()->clientScript->registerCssFile($site_url . "/assets/leaflet/plugin/routing/leaflet-routing-machine.css");
                Yii::app()->clientScript->registerScriptFile($site_url . "/assets/leaflet/plugin/routing/leaflet-routing-machine.min.js"
                        , CClientScript::POS_END);
            }
        }

        return true;
    }

    public function actionLogin() {

        $encryption_type = Yii::app()->params->encryption_type;
        if (empty($encryption_type)) {
            $encryption_type = 'yii';
        }

        if (Driver::islogin()) {
            $this->redirect(Yii::app()->createUrl('/app'));
            Yii::app()->end();
        }

        $this->body_class = 'login-body';

        /* unset(Yii::app()->request->cookies['kt_username']);
          unset(Yii::app()->request->cookies['kt_password']); */

        $kt_username = isset(Yii::app()->request->cookies['kt_username']) ? Yii::app()->request->cookies['kt_username']->value : '';
        $kt_password = isset(Yii::app()->request->cookies['kt_password']) ? Yii::app()->request->cookies['kt_password']->value : '';

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
        $this->redirect(Yii::app()->createUrl('/app/login'));
    }

    public function actionIndex() {
        $this->body_class = "dashboard";
        $this->render('dashboard');
    }

    public function actionDashboard() {
        $this->body_class = "dashboard";
        $this->render('dashboard');
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

        $team_list = Driver::teamList(Driver::getUserId());
        if ($team_list) {
            $team_list = Driver::toList($team_list, 'team_id', 'team_name', Driver::t("All Team")
            );
        }

        $all_driver = Driver::getAllDriver(
                        Driver::getUserId()
        );

        $start = date('Y-m-d', strtotime("-7 day"));
        $end = date("Y-m-d", strtotime("+1 day"));

        $this->render('reports', array(
            'team_list' => $team_list,
            'all_driver' => $all_driver,
            'start_date' => $start,
            'end_date' => $end
        ));
    }

    public function actionAssignment() {
        $this->body_class = "page-single";
        $this->render('assignment');
    }

    public function actionResetPassword() {
        $this->body_class = 'login-body';
        $this->render('resetpassword', array(
            'hash' => isset($_GET['hash']) ? $_GET['hash'] : ''
        ));
    }

    public function actionordenesSinProcesar() {
        $this->body_class = "page-single";
        $this->render('ordenes-sin-procesar');
    }

    public function actiontodasOrdenes() {
        $this->body_class = "page-single";
        $this->render('todas-las-ordenes');
    }

    public function actionMensajeros() {
        $this->body_class = "page-single";
        $this->render('mensajeros');
    }

    public function actionRutas() {
        $this->body_class = "page-single";
        $this->render('rutas');
    }

    public function actionZonas() {
        $this->body_class = "page-single";
        $this->render('zonas');
    }

    public function actionClientes() {
        $this->body_class = "page-single";
        $this->render('clientes');
    }

    public function actionprofile() {

        FrontFunctions::ClearPromoCode();

        $this->body_class = "page-single";
        if ($data = AdminFunctions::getCustomerByID(Driver::getUserId())) {
            $plans = Driver::getPlansByID($data['plan_id']);
            $this->render('profile', array(
                'data' => $data,
                'plans' => $plans,
                'tabs' => isset($_GET['tabs']) ? $_GET['tabs'] : 1,
                'history' => AdminFunctions::getCustomerPaymentLogs(Driver::getUserId()),
                'sms_balance' => Driver::getSMSBalance(Driver::getUserId())
            ));
        } else {
            $this->render('error', array(
                'msg' => t("Profile not available")
            ));
        }
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

    public function actionContacts() {
        $this->body_class = "page-single";
        $this->render('contact-list');
    }

    public function actionUsuarios() {
        $this->body_class = "page-single";
        $this->render('usuarios');
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
            'data'=>$params_data,
            
        ));
    }

    public function actionServices() {
        $customer_id = Driver::getUserId();

        $this->render('services', array(
            'services' => AdminFunctions::servicesFullList(0, 'published'),
            'data' => AdminFunctions::getCustomerByID($customer_id)
        ));
    }

    public function actioncargaMasiva() {
        $msg = '';
        $error = 0;
        $line_processing = array();
        $params = '';
        $params_data = array();

        if (isset($_POST) && $_SERVER['REQUEST_METHOD'] == 'POST') {
            $filename = $_FILES['file']['name'];
            if (preg_match("/.csv/i", $filename)) {
                ini_set('auto_detect_line_endings', TRUE);
                $handle = fopen($_FILES['file']['tmp_name'], "r");
                $x = 1;
                $prefijo = "";
                while (($data = @fgetcsv($handle)) !== FALSE) {
                    //$line_processing[] = t("Procesando línea") . " ($x)";
                    array_push($line_processing, t("Procesando línea") . " ($x)");
                    if (count($data) >= 7) {

                        if (empty($data[0])) {
                            //$line_processing[] = $line_processing[] = t("Error en línea") . " ($x)" . " " . t("Tipo Servicio está vacío");
                            array_push($line_processing, t("Error en línea") . " ($x)" . " " . t("Tipo Servicio está vacío"));
                            $error++;
                            //continue;
                        }
                        if (empty($data[1])) {
                            // $line_processing[] = $line_processing[] = t("Error en línea") . " ($x)" . " " . t("Origen está vacío");
                            array_push($line_processing, t("Error en línea") . " ($x)" . " " . t("Origen está vacío"));
                            $error++;
                            //continue;
                        }
                        if (empty($data[2])) {
                            //$line_processing[] = $line_processing[] = t("Error en línea") . " ($x)" . " " . t("Dirección origen está vacía");
                            array_push($line_processing, t("Error en línea") . " ($x)" . " " . t("Dirección origen está vacía"));
                            $error++;
                            //continue;
                        }
                        if (empty($data[3])) {
                            //$line_processing[] = $line_processing[] = t("Error en línea") . " ($x)" . " " . t("Zona origen está vacía");
                            array_push($line_processing, t("Error en línea") . " ($x)" . " " . t("Zona origen está vacía"));
                            $error++;
                            //continue;
                        } else {
                            if ($resZona = Driver::getZonaPorNombre($data[3]))
                                $data[3] = $resZona['id'];
                            else {
                                //  $line_processing[] = $line_processing[] = t("Error en línea") . " ($x)" . " " . t("Nombre de Zona origen no encontrado");
                                array_push($line_processing, t("Error en línea") . " ($x)" . " " . t("Nombre de Zona origen no encontrado"));
                                $error++;
                            }
                        }
                        if (empty($data[4])) {
                            // $line_processing[] = $line_processing[] = t("Error en línea") . " ($x)" . " " . t("Remitente está vacío");
                            array_push($line_processing, t("Error en línea") . " ($x)" . " " . t("Remitente está vacío"));
                            $error++;
                            //continue;
                        }
                        if (empty($data[5])) {
                            // $line_processing[] = $line_processing[] = t("Error en línea") . " ($x)" . " " . t("Teléfono remitente está vacío");
                            array_push($line_processing, t("Error en línea") . " ($x)" . " " . t("Teléfono remitente está vacío"));
                            $error++;
                            //continue;
                        }
                        if (empty($data[6])) {
                            // $line_processing[] = $line_processing[] = t("Error en línea") . " ($x)" . " " . t("Destino está vacío");
                            array_push($line_processing, t("Error en línea") . " ($x)" . " " . t("Destino está vacío"));
                            $error++;
                            //continue;
                        }
                        if (empty($data[7])) {
                            // $line_processing[] = $line_processing[] = t("Error en línea") . " ($x)" . " " . t("Dirección destino está vacía");
                            array_push($line_processing, t("Error en línea") . " ($x)" . " " . t("Dirección destino está vacía"));
                            $error++;
                            //continue;
                        }
                        if (empty($data[8])) {
                            //$line_processing[] = $line_processing[] = t("Error en línea") . " ($x)" . " " . t("Zona destino está vacía");
                            array_push($line_processing, t("Error en línea") . " ($x)" . " " . t("Zona destino está vacía"));
                            $error++;
                            //continue;
                        } else {
                            if ($resZona = Driver::getZonaPorNombre($data[8]))
                                $data[8] = $resZona['id'];
                            else {
                                //$line_processing[] = $line_processing[] = t("Error en línea") . " ($x)" . " " . t("Nombre de Zona destino no encontrado");
                                array_push($line_processing, t("Error en línea") . " ($x)" . " " . t("Nombre de Zona destino no encontrado"));
                                $error++;
                            }
                        }
                        if (empty($data[9])) {
                            // $line_processing[] = $line_processing[] = t("Error en línea") . " ($x)" . " " . t("Recipiente está vacío");
                            array_push($line_processing, t("Error en línea") . " ($x)" . " " . t("Recipiente está vacío"));
                            $error++;
                            //continue;
                        }
                        if (empty($data[10])) {
                            // $line_processing[] = $line_processing[] = t("Error en línea") . " ($x)" . " " . t("Teléfono recipiente está vacío");
                            array_push($line_processing, t("Error en línea") . " ($x)" . " " . t("Teléfono recipiente está vacío"));
                            $error++;
                            //continue;
                        }
                        if (empty($data[11])) {
                            //$line_processing[] = $line_processing[] = t("Error en línea") . " ($x)" . " " . t("PREFIJO_CLIENTE está vacío");
                            array_push($line_processing, t("Error en línea") . " ($x)" . " " . t("PREFIJO_CLIENTE está vacío"));
                            $error++;
                            //continue;
                        } else {
                            if ($resCliente = Driver::getClientePorPrefijo($data[11])) {
                                $prefijo = $data[11];
                                $data[11] = $resCliente['id_cliente'];
                            } else {
                                //  $line_processing[] = $line_processing[] = t("Error en línea") . " ($x)" . " " . t("Cliente no encontrado");
                                array_push($line_processing, t("Error en línea") . " ($x)" . " " . t("Cliente no encontrado"));
                                $error++;
                            }
                        }
                        if (empty($data[12])) {
                            // $line_processing[] = $line_processing[] = t("Error en línea") . " ($x)" . " " . t("detalle está vacío");
                            array_push($line_processing, t("Error en línea") . " ($x)" . " " . t("detalle está vacío"));
                            $error++;
                            //continue;
                        }
                        $params = array(
                            'tipo_servicio' => $data[0],
                            'origen' => $data[1],
                            'direccion_origen' => !empty($data[2]) ? $data[2] : '',
                            'zona_origen' => !empty($data[3]) ? $data[3] : '',
                            'remitente' => !empty($data[4]) ? $data[4] : '',
                            'telefono_remitente' => !empty($data[5]) ? $data[5] : '',
                            'destino' => $data[6],
                            'direccion_destino' => !empty($data[7]) ? $data[7] : '',
                            'zona_destino' => !empty($data[8]) ? $data[8] : '',
                            'recibe' => !empty($data[9]) ? $data[9] : '',
                            'telefono_recibe' => !empty($data[10]) ? $data[10] : '',
                            'id_cliente' => !empty($data[11]) ? $data[11] : '',
                            'detalle' => !empty($data[12]) ? $data[12] : '',
                            'ip_address' => $_SERVER['REMOTE_ADDR'],
                            'date_created' => AdminFunctions::dateNow(),
                            'estado' => 'Creado',
                            'origen_orden' => 'MASIVA',
                            'prefijo' => $prefijo,
                        );
                    } else {
                        $error++;
                        $line_processing[] = t("Error en línea") . " ($x)";
                    }
                    array_push($params_data, array(
                        'data' => $params,
                        'line' => $line_processing,
                            )
                    );

                    $line_processing = '';

                    $x++;
                }
                ini_set('auto_detect_line_endings', FALSE);
            } else {
                $error = 1;
                $msg = t("Por favor cargue un archivo csv válido");
            }
        }
        $this->render('carga-masiva', array(
            'msg' => $msg,
            'error' => $error,
            'data' => $params_data
        ));
    }

    public function actionUploadProfilePhoto() {
        require_once('Uploader.php');
        $path_to_upload = Driver::driverUploadPath();
        $valid_extensions = array('jpeg', 'jpg', 'png', 'gif');
        if (!file_exists($path_to_upload)) {
            if (!@mkdir($path_to_upload, 0777)) {
                $this->msg = Driver::t("Error has occured cannot create upload directory");
                $this->jsonResponse();
            }
        }

        $Upload = new FileUpload('uploadfile');
        $ext = $Upload->getExtension();
        //$Upload->newFileName = mktime().".".$ext;
        $result = $Upload->handleUpload($path_to_upload, $valid_extensions);
        if (!$result) {
            $this->msg = $Upload->getErrorMsg();
        } else {
            $this->code = 1;
            $this->msg = Driver::t("upload done");
            $this->details = Yii::app()->getBaseUrl(true) . "/upload/photo/" . $_GET['uploadfile'];
        }
        $this->jsonResponse();
    }

    public function actionuploadcv() {
        require_once('Uploader.php');
        $path_to_upload = Driver::driverUploadPath();
        $valid_extensions = array('pdf', 'docx', 'doc');
        if (!file_exists($path_to_upload)) {
            if (!@mkdir($path_to_upload, 0777)) {
                $this->msg = Driver::t("Error has occured cannot create upload directory");
                $this->jsonResponse();
            }
        }

        $Upload = new FileUpload('uploadfile');
        $ext = $Upload->getExtension();
        //$Upload->newFileName = mktime().".".$ext;
        $result = $Upload->handleUpload($path_to_upload, $valid_extensions);
        if (!$result) {
            $this->msg = $Upload->getErrorMsg();
        } else {
            $this->code = 1;
            $this->msg = Driver::t("upload done");
            $this->details = Yii::app()->getBaseUrl(true) . "/upload/cv/" . $_GET['uploadfile'];
        }
        $this->jsonResponse();
    }

    public function actionSmslogs() {
        $this->body_class = "page-single";
        $this->render('sms-logs');
    }

    public function actionEmailLogs() {
        $this->body_class = "page-single";
        $this->render('email-logs');
    }

    public function actionseleccionMultipleOrdenes() {
        $this->body_class = "page-single";
        $this->render('cambio-masivo-estado-ordenes');
    }

    public function actionContactos() {
        $this->body_class = "page-single";
        $this->render('contactos');
    }

    public function actionBulkPush() {

        if (Driver::customerCanBroadcast(Driver::getUserId())) {
            if ($team_list = Driver::teamList(Driver::getUserId())) {
                $team_list = Driver::toList($team_list, 'team_id', 'team_name', Driver::t("Please select a team from a list"));
            }
            $this->body_class = "page-single";
            $this->render('bulk-push', array(
                'team_list' => $team_list
            ));
        } else
            $this->render('error', array(
                'msg' => t("Your account does not have access to Push Broadcast"),
                'upgrade_plan' => true
            ));
    }

    public function actionBulkLogs() {
        $this->body_class = "page-single";
        $this->render('bulk-push-logs');
    }

    public function actionTrackBack() {
        $this->body_class = "page-single";
        $this->render('track-back', array(
            'driver_list' => Driver::driverDropDownList(Driver::getUserId()),
                //'track_list'=>Driver::backTrackList( Driver::getUserId() )
        ));
    }

    public function actionSmsPurchase() {
        $this->render('payment-options', array(
            'transaction_type' => 'sms'
        ));
    }

    public function actionexport_pedidos_por_ruta() {
        $data = array();

        $stmt = isset($_SESSION['xpress_stmt_pedidosPorRutaList']) ? $_SESSION['xpress_stmt_pedidosPorRutaList'] : '';


        if (!empty($stmt)) {
            // $pos = strpos($stmt, "LIMIT");
            // $stmt = substr($stmt, 0, $pos);
            $DbExt = new DbExt;
            // $DbExt->qry("SET SQL_BIG_SELECTS=1");
            if ($res = $DbExt->rst($stmt)) {

                foreach ($res as $val) {
                    $fecha_envio = Yii::app()->functions->prettyDate($val['fecha_envio'], true);
                    $date_created = Yii::app()->functions->prettyDate($val['o.date_created'], true);
                    $fecha_entrega = Yii::app()->functions->prettyDate($val['fecha_entrega'], true);
                    $data[] = array(
                        $val['codigo_orden'],
                        $val['tipo_servicio'],
                        $val['nombres'],
                        $val['origen'],
                        $val['remitente'],
                        $val['telefono_remitente'],
                        $val['ciudad_origen'],
                        $val['direccion_origen'],
                        $val['zona_origen_nombre'],
                        $val['destino'],
                        $val['recibe'],
                        $val['telefono_recibe'],
                        $val['ciudad_destino'],
                        $val['direccion_destino'],
                        $val['zona_destino_nombre'],
                        $date_created,
                        $fecha_envio,
                        $fecha_entrega,
                        $val['detalle'],
                        $val['estado']
                    );
                }

                $header = array(
                    driver::t("ID"),
                    driver::t("Tipo Servicio"),
                    driver::t("Cliente"),
                    driver::t("Origen"),
                    driver::t("Remitente"),
                    driver::t("Telefono Remitente"),
                    driver::t("Ciudad Origen"),
                    driver::t("Direccion Origen"),
                    driver::t("Zona Origen"),
                    driver::t("Destino"),
                    driver::t("Recibe"),
                    driver::t("Telefono Recibe"),
                    driver::t("Ciudad Destino"),
                    driver::t("Direccion Destino"),
                    driver::t("Zona Destino"),
                    driver::t("Fecha Creacion"),
                    driver::t("Fecha Envio"),
                    driver::t("Fecha Entrega"),
                    driver::t("Detalle"),
                    driver::t("Estado"),
                );

                $filename = 'ordenes-' . date('c') . '.csv';
                $excel = new ExcelFormat($filename);
                $excel->addHeaders($header);
                $excel->setData($data);
                $excel->prepareExcel();
            }
        }
    }

    public function actionexport_pedidos_todos() {
        $data = array();

        $stmt = isset($_SESSION['xpress_stmt_pedidosTodosList']) ? $_SESSION['xpress_stmt_pedidosTodosList'] : '';

        if (!empty($stmt)) {
            $DbExt = new DbExt;
            if ($res = $DbExt->rst($stmt)) {

                foreach ($res as $val) {
                    $fecha_envio = Yii::app()->functions->prettyDate($val['fecha_envio'], true);
                    $date_created = Yii::app()->functions->prettyDate($val['o.date_created'], true);
                    $fecha_entrega = Yii::app()->functions->prettyDate($val['fecha_entrega'], true);
                    $data[] = array(
                        $val['codigo_orden'],
                        $val['origen'],
                        $val['direccion_origen'],
                        $val['destino'],
                        $val['direccion_destino'],
                        $val['ciudad_destino'],
                        $val['detalle'],
                        $date_created,
                        $val['estado'],
                        'mensajero',
                        $val['no_gestiones'], 
                        $val['peso']
                        /*$val['tipo_servicio'],
                        $val['nombres'],
                        $val['remitente'],
                        $val['telefono_remitente'],
                        $val['ciudad_origen'],
                        $val['zona_origen_nombre'],
                        $val['recibe'],
                        $val['telefono_recibe'],
                        $val['zona_destino_nombre'],
                        $fecha_envio,
                        $fecha_entrega,
            */
                    );
                }

                $header = array(
                    driver::t("ID"),
                    driver::t("Origen"),
                    driver::t("Direccion Origen"),
                    driver::t("Destino"),
                    driver::t("Direccion Destino"),
                    driver::t("Ciudad Destino"),
                    driver::t("Detalle"),
                    driver::t("Fecha Creacion"),
                    driver::t("Estado"),
                    /*driver::t("Tipo Servicio"),
                    driver::t("Cliente"),*/
                    driver::t("Mensajero"),
                    driver::t("Num de gestiones"),
                    driver::t("Peso"),
                );

                $filename = 'ordenes-' . date('c') . '.csv';
                $excel = new ExcelFormat($filename);
                $excel->addHeaders($header);
                $excel->setData($data);
                $excel->prepareExcel();
            }
        }
    }

    public function actionexport_pedidos_masivo_estado_todos() {
        $data = array();

        $stmt = isset($_SESSION['xpress_stmt_pedidosTodosMasivoEstadoList']) ? $_SESSION['xpress_stmt_pedidosTodosMasivoEstadoList'] : '';


        if (!empty($stmt)) {
            // $pos = strpos($stmt, "LIMIT");
            // $stmt = substr($stmt, 0, $pos);
            $DbExt = new DbExt;
            // $DbExt->qry("SET SQL_BIG_SELECTS=1");
            if ($res = $DbExt->rst($stmt)) {

                foreach ($res as $val) {
                    $fecha_envio = Yii::app()->functions->prettyDate($val['fecha_envio'], true);
                    $date_created = Yii::app()->functions->prettyDate($val['o.date_created'], true);
                    $fecha_entrega = Yii::app()->functions->prettyDate($val['fecha_entrega'], true);
                    $data[] = array(
                        $val['codigo_orden'],
                        $val['tipo_servicio'],
                        $val['nombres'],
                        $val['origen'],
                        $val['remitente'],
                        $val['telefono_remitente'],
                        $val['ciudad_origen'],
                        $val['direccion_origen'],
                        $val['zona_origen_nombre'],
                        $val['destino'],
                        $val['recibe'],
                        $val['telefono_recibe'],
                        $val['ciudad_destino'],
                        $val['direccion_destino'],
                        $val['zona_destino_nombre'],
                        $date_created,
                        $fecha_envio,
                        $fecha_entrega,
                        $val['detalle'],
                        $val['estado']
                    );
                }

                $header = array(
                    driver::t("ID"),
                    driver::t("Tipo Servicio"),
                    driver::t("Cliente"),
                    driver::t("Origen"),
                    driver::t("Remitente"),
                    driver::t("Telefono Remitente"),
                    driver::t("Ciudad Origen"),
                    driver::t("Direccion Origen"),
                    driver::t("Zona Origen"),
                    driver::t("Destino"),
                    driver::t("Recibe"),
                    driver::t("Telefono Recibe"),
                    driver::t("Ciudad Destino"),
                    driver::t("Direccion Destino"),
                    driver::t("Zona Destino"),
                    driver::t("Fecha Creacion"),
                    driver::t("Fecha Envio"),
                    driver::t("Fecha Entrega"),
                    driver::t("Detalle"),
                    driver::t("Estado"),
                );

                $filename = 'ordenes-' . date('c') . '.csv';
                $excel = new ExcelFormat($filename);
                $excel->addHeaders($header);
                $excel->setData($data);
                $excel->prepareExcel();
            }
        }
    }

    public function actionOrdenesPorRuta() {
        $this->body_class = "page-single";

        $id_ruta = isset($_GET['id_ruta']) ? $_GET['id_ruta'] : '';
        if (!empty($id_ruta)) {

            $id_mensajero = isset($_GET['id_mensajero']) ? $_GET['id_mensajero'] : '';
            $fecha_orden = isset($_GET['fecha_orden']) ? $_GET['fecha_orden'] : '';
            $detalle = isset($_GET['detalle']) ? $_GET['detalle'] : '';
            $this->render('ordenes-por-ruta', array(
                'id_ruta' => $id_ruta,
                'id_mensajero' => $id_mensajero,
                'fecha_orden' => $fecha_orden,
                'detalle' => $detalle,
            ));
        } else {
            $this->render('error', array(
                'msg' => t("No hay un id de ruta válido")
            ));
        }
    }

}

/* end class*/