<?php

if (!isset($_SESSION)) {
    session_start();
}

class AjaxController extends CController {

    public $code = 2;
    public $msg;
    public $details;
    public $data;

    public function __construct() {
        $this->data = $_POST;
    }

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

        if (isset($this->data['language'])) {
            Yii::app()->language = $this->data['language'];
        }
        if (isset($_GET['language'])) {
            Yii::app()->language = $_GET['language'];
        }
        unset($this->data['language']);
    }

    public function beforeAction($action) {
        $action = Yii::app()->controller->action->id;
        $continue = true;

        $action = strtolower($action);
        if ($action == "login" || $action == "forgotpassword" || $action == "resetpassword") {
            $continue = false;
        }

        if ($continue) {
            if (!Driver::islogin()) {
                $this->msg = Driver::t("Authentication failed");
                $this->jsonResponse();
            }
        }
        return true;
    }

    private function jsonResponse() {
        $resp = array('code' => $this->code, 'msg' => $this->msg, 'details' => $this->details);
        echo CJSON::encode($resp);
        Yii::app()->end();
    }

    private function otableNodata() {
        if (isset($_GET['sEcho'])) {
            $feed_data['sEcho'] = $_GET['sEcho'];
        } else
            $feed_data['sEcho'] = 1;

        $feed_data['iTotalRecords'] = 0;
        $feed_data['iTotalDisplayRecords'] = 0;
        $feed_data['aaData'] = array();
        echo json_encode($feed_data);
        die();
    }

    private function otableOutput($feed_data = '') {
        echo json_encode($feed_data);
        die();
    }

    public function actionLogin() {
        $req = array(
            'email' => Driver::t("Email es requerido"),
            'password' => Driver::t("password es requerido"),
        );
        $Validator = new Validator;
        $Validator->required($req, $this->data);
        if ($Validator->validate()) {


            if ($res = Driver::Login(trim($this->data['email']), trim($this->data['password']))) {

                if ($res['status'] == "active" || $res['status'] == "expired") {

                    $_SESSION['xpress'] = $res;
                    $this->code = 1;
                    $this->msg = t("Login Exitoso");
                    $this->details = Yii::app()->createUrl('/app/dashboard');

                    if (isset($this->data['remember'])) {
                        Yii::app()->request->cookies['kt_username'] = new CHttpCookie('kt_username', $this->data['email_address']);
                        $runtime_path = Yii::getPathOfAlias('webroot') . "/protected/runtime";
                        if (!file_exists($runtime_path)) {
                            mkdir($runtime_path, 0777);
                        }

                        $encryption_type = Yii::app()->params->encryption_type;
                        if (empty($encryption_type)) {
                            $encryption_type = 'yii';
                        }

                        if ($encryption_type == "yii") {
                            try {
                                $password = Yii::app()->securityManager->encrypt($this->data['password']);
                                Yii::app()->request->cookies['kt_password'] = new CHttpCookie('kt_password', $password);
                            } catch (Exception $e) {
                                $this->msg = t("Path is not writable by the server") . " $runtime_path";
                                $this->code = 2;
                            }
                        }
                    } else {
                        unset(Yii::app()->request->cookies['kt_username']);
                        unset(Yii::app()->request->cookies['kt_password']);
                    }
                } else
                    $this->msg = t("Login fallido. su cuenta está") . " " . $res['status'];
            } else
                $this->msg = t("Login fallido. usuario o password no válidos");
        } else
            $this->msg = $Validator->getErrorAsHTML();
        $this->jsonResponse();
    }

    private function userType() {
        return Driver::getUserType();
    }

    private function userId() {
        return Driver::getUserId();
    }

    public function actionpedidosTodosList() {
        $aColumns = array(
            'codigo_orden',
            'tipo_servicio',
            'c.nombre',
            'c.apellido',
            'l_destino.nombre',
            'direccion_destino',
            'z_destino.zona',
            'fecha_envio',
            'estado'
        );


        $t = AjaxDataTables::AjaxData($aColumns);
        if (isset($_GET['debug'])) {
            dump($t);
        }

        if (is_array($t) && count($t) >= 1) {
            $sWhere = $t['sWhere'];
            $sOrder = $t['sOrder'];
            $sLimit = $t['sLimit'];
        }

        $and = '';

        $stmt = "SELECT SQL_CALC_FOUND_ROWS o.*,z_destino.zona AS 'zona_destino_nombre',z_origen.zona AS 'zona_origen_nombre',l_destino.nombre AS 'ciudad_destino',l_origen.nombre AS 'ciudad_origen'
		,CONCAT(c.nombre,' ',c.apellido) AS nombres
                FROM
		{{orden}} o
                INNER JOIN {{zonas}} z_destino on o.zona_destino=z_destino.id
                INNER JOIN {{locacion}} l_destino on z_destino.id_locacion=l_destino.id
                INNER JOIN {{zonas}} z_origen on o.zona_origen=z_origen.id
                INNER JOIN {{locacion}} l_origen on z_origen.id_locacion=l_origen.id
                LEFT OUTER JOIN {{clientes}} c on c.id_cliente=o.id_cliente
		WHERE 1		
		$and
		$sWhere
                ORDER BY o.date_created DESC 
                $sLimit;
		";
        if (isset($_GET['debug'])) {
            dump($stmt);
        }
        $_SESSION['xpress_stmt_pedidosTodosList'] = $stmt;

        $DbExt = new DbExt;
        $DbExt->qry("SET SQL_BIG_SELECTS=1");

        if ($res = $DbExt->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $DbExt->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;
            $feed_data['iDisplayStart'] = $iTotalRecords;
            foreach ($res as $val) {
                $date_created = AdminFunctions::prettyDate($val['date_created'], true);
                $date_created = Yii::app()->functions->translateDate($date_created);
                $status = "<span class=\"tag " . $val['estado'] . " \">" . Driver::t($val['estado']) . "</span>";
                $action = "<div class=\"table-action\">";
                $action1 = "<a class=\"btn btn-primary details\" data-hidden_id='orden_id' data-modal_detalle='detalle-orden-modal'
			    	data-id=\"" . $val['orden_id'] . "\" href=\"#no\">" . t("Detalle") . "</a>";


                $action .= $action1;
                $action .= "&nbsp;|&nbsp;";

                $action .= "</div>";


                $feed_data['aaData'][] = array(
                    $val['codigo_orden'] . $action,
                    $val['origen'],
                    $val['direccion_origen'],
                    $val['destino'],
                    $val['direccion_destino'],
                    $val['tipo_servicio'],
                    $date_created,
                    $status,
                    $action1
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actionpedidosTodosFilteredList() {
        $aColumns = array(
            'codigo_orden',
            'tipo_servicio',
            'c.nombre',
            'c.apellido',
            'l_destino.nombre',
            'direccion_destino',
            'z_destino.zona',
            'fecha_envio',
            'estado'
        );


        $t = AjaxDataTables::AjaxData($aColumns);
        if (isset($_GET['debug'])) {
            dump($t);
        }

        if (is_array($t) && count($t) >= 1) {
            $sWhere = $t['sWhere'];
            $sOrder = $t['sOrder'];
            $sLimit = $t['sLimit'];
        }



        $and = '';


        if (isset($_GET["search"]) && $_GET["search"] != "") {
            $and .= " AND (UPPER(o.origen) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(c.nombre) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(c.apellido) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(o.direccion_origen) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(l_origen.nombre) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(z_origen.zona) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(o.destino) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(o.direccion_destino) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(l_destino.nombre) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(z_destino.zona) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(o.recibe) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(o.remitente) like  '%" . $_GET["search"] . "%' )";
        }

        if (isset($_GET["fecha_creacion_desde"]) && $_GET["fecha_creacion_desde"] != "") {
            $and .= " AND o.date_created >=  '" . $_GET["fecha_creacion_desde"] . " 00:00:00' ";
        }
        if (isset($_GET["fecha_creacion_hasta"]) && $_GET["fecha_creacion_hasta"] != "") {
            $and .= " AND o.date_created <=  '" . $_GET["fecha_creacion_hasta"] . " 23:59:59' ";
        }
        if (isset($_GET["fecha_envio_desde"]) && $_GET["fecha_envio_desde"] != "") {
            $and .= " AND o.fecha_envio >=  '" . $_GET["fecha_envio_desde"] . " 00:00:00' ";
        }
        if (isset($_GET["fecha_envio_hasta"]) && $_GET["fecha_envio_hasta"] != "") {
            $and .= " AND o.fecha_envio <=  '" . $_GET["fecha_envio_hasta"] . " 23:59:59' ";
        }
        if (isset($_GET["fecha_entrega_desde"]) && $_GET["fecha_entrega_desde"] != "") {
            $and .= " AND o.fecha_entrega >=  '" . $_GET["fecha_entrega_desde"] . " 00:00:00' ";
        }
        if (isset($_GET["fecha_entrega_hasta"]) && $_GET["fecha_entrega_hasta"] != "") {
            $and .= " AND o.fecha_entrega <=  '" . $_GET["fecha_entrega_hasta"] . " 23:59:59' ";
        }
        if (isset($_GET["codigo_orden"]) && $_GET["codigo_orden"] != "") {
            $and .= "  AND UPPER(o.codigo_orden) like  '%" . $_GET["codigo_orden"] . "%' ";
        }
        if (isset($_GET["estado"]) && $_GET["estado"] != "") {
            $and .= "  AND o.estado =  '" . $_GET["estado"] . "' ";
        }
        if (isset($_GET["tipo_servicio"]) && $_GET["tipo_servicio"] != "") {
            $and .= "  AND o.tipo_servicio =  '" . $_GET["tipo_servicio"] . "' ";
        }
        if (isset($_GET["id_mensajero"]) && $_GET["id_mensajero"] != "" && $_GET["id_mensajero"] != "0") {
            $and .= "  AND m.id_mensajero =  '" . $_GET["id_mensajero"] . "' ";
        }
        if (isset($_GET["id_ruta"]) && $_GET["id_ruta"] != "" && $_GET["id_ruta"] != "0") {
            $and .= "  AND r.id_ruta =  '" . $_GET["id_ruta"] . "' ";
        }
        if (isset($_GET["id_cliente"]) && $_GET["id_cliente"] != "" && $_GET["id_cliente"] != "0") {
            $and .= "  AND c.id_cliente =  '" . $_GET["id_cliente"] . "' ";
        }

        $stmt = "SELECT SQL_CALC_FOUND_ROWS o.*,z_destino.zona AS 'zona_destino_nombre',z_origen.zona AS 'zona_origen_nombre',l_destino.nombre AS 'ciudad_destino',l_origen.nombre AS 'ciudad_origen'
		,CONCAT(c.nombre,' ',c.apellido) AS nombres,m.id_mensajero, m.nombre, m.apellido 
                FROM
		{{orden}} o
                INNER JOIN {{zonas}} z_destino on o.zona_destino=z_destino.id
                INNER JOIN {{locacion}} l_destino on z_destino.id_locacion=l_destino.id
                INNER JOIN {{zonas}} z_origen on o.zona_origen=z_origen.id
                INNER JOIN {{locacion}} l_origen on z_origen.id_locacion=l_origen.id
                 LEFT OUTER JOIN {{clientes}} c on c.id_cliente=o.id_cliente
                 LEFT OUTER JOIN {{ruta}} r on o.id_ruta=r.id_ruta  
                 LEFT OUTER JOIN {{mensajero}} m on r.id_mensajero=m.id_mensajero  
		WHERE 1		
		$and
		$sWhere
		ORDER BY o.date_created DESC 
                $sLimit;
		";
        if (isset($_GET['debug'])) {
            dump($stmt);
        }

        $_SESSION['xpress_stmt_pedidosTodosList'] = $stmt;

        $DbExt = new DbExt;
        $DbExt->qry("SET SQL_BIG_SELECTS=1");

        if ($res = $DbExt->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $DbExt->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {
                $date_created = AdminFunctions::prettyDate($val['date_created'], true);
                $date_created = Yii::app()->functions->translateDate($date_created);

                $status = "<span class=\"tag " . $val['estado'] . " \">" . Driver::t($val['estado']) . "</span>";
                $action = "<div class=\"table-action\">";
                $action1 = "<a class=\"btn btn-primary details\" data-hidden_id='orden_id' data-modal_detalle='detalle-orden-modal' 
			    	data-id=\"" . $val['orden_id'] . "\" href=\"#no\">" . t("Detalle") . "</a>";


                $action .= $action1;
                $action .= "&nbsp;|&nbsp;";

                $action .= "</div>";

                    
                $feed_data['aaData'][] = array(
                    $val['codigo_orden'] . $action,
                    $val['origen'],
                    $val['direccion_origen'],
                    $val['destino'],
                    $val['direccion_destino'],
                    $val['tipo_servicio'],
                    $date_created,
                    $val['nombre'] . ' ' . $val['apellido'],
                    $status,
                    $action1
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actionrepedidosTodosFilteredList() {
        $aColumns = array(
            'codigo_orden',
            'tipo_servicio',
            'c.nombre',
            'c.apellido',
            'l_destino.nombre',
            'direccion_destino',
            'z_destino.zona',
            'fecha_envio',
            'estado'
        );


        $t = AjaxDataTables::AjaxData($aColumns);

        $stmt = $_SESSION['xpress_stmt_pedidosTodosList'];

        if (isset($_GET['debug'])) {
            dump($stmt);
        }



        $DbExt = new DbExt;
        $DbExt->qry("SET SQL_BIG_SELECTS=1");

        if ($res = $DbExt->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $DbExt->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {
                $date_created = AdminFunctions::prettyDate($val['date_created'], true);
                $date_created = Yii::app()->functions->translateDate($date_created);

                $status = "<span class=\"tag " . $val['estado'] . " \">" . Driver::t($val['estado']) . "</span>";
                $action = "<div class=\"table-action\">";
                $action1 = "<a class=\"btn btn-primary details\" data-hidden_id='orden_id' data-modal_detalle='detalle-orden-modal' 
			    	data-id=\"" . $val['orden_id'] . "\" href=\"#no\">" . t("Detalle") . "</a>";


                $action .= $action1;
                $action .= "&nbsp;|&nbsp;";

                $action .= "</div>";


                $feed_data['aaData'][] = array(
                    $val['codigo_orden'] . $action,
                    $val['origen'],
                    $val['direccion_origen'],
                    $val['destino'],
                    $val['direccion_destino'],
                    $val['tipo_servicio'],
                    $date_created,
                    $status,
                    $action1
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actionpedidosTodosMasivoEstadoList() {
        $aColumns = array(
            'codigo_orden',
            'tipo_servicio',
            'c.nombre',
            'c.apellido',
            'l_destino.nombre',
            'direccion_destino',
            'z_destino.zona',
            'fecha_envio',
            'estado'
        );


        $t = AjaxDataTables::AjaxData($aColumns);
        if (isset($_GET['debug'])) {
            dump($t);
        }

        if (is_array($t) && count($t) >= 1) {
            $sWhere = $t['sWhere'];
            $sOrder = $t['sOrder'];
            $sLimit = $t['sLimit'];
        }

        $and = '';

        $stmt = "SELECT SQL_CALC_FOUND_ROWS o.*,z_destino.zona AS 'zona_destino_nombre',z_origen.zona AS 'zona_origen_nombre',l_destino.nombre AS 'ciudad_destino',l_origen.nombre AS 'ciudad_origen'
		,CONCAT(c.nombre,' ',c.apellido) AS nombres
                FROM
		{{orden}} o
                INNER JOIN {{zonas}} z_destino on o.zona_destino=z_destino.id
                INNER JOIN {{locacion}} l_destino on z_destino.id_locacion=l_destino.id
                INNER JOIN {{zonas}} z_origen on o.zona_origen=z_origen.id
                INNER JOIN {{locacion}} l_origen on z_origen.id_locacion=l_origen.id
                LEFT OUTER JOIN {{clientes}} c on c.id_cliente=o.id_cliente
		WHERE 1		
		$and
		$sWhere
		ORDER BY o.date_created DESC 
                $sLimit;
		";
        if (isset($_GET['debug'])) {
            dump($stmt);
        }
        $_SESSION['xpress_stmt_pedidosTodosMasivoEstadoList'] = $stmt;

        $DbExt = new DbExt;
        $DbExt->qry("SET SQL_BIG_SELECTS=1");

        if ($res = $DbExt->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $DbExt->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {
                $date_created = AdminFunctions::prettyDate($val['date_created'], true);
                $date_created = Yii::app()->functions->translateDate($date_created);
                $nombre_selecciona = "seleccionado_" . $val['codigo_orden'];
                $selecciona = "<div>";
                $selecciona .= CHtml::checkBox($nombre_selecciona, false, array(
                            'class' => "seleccionado",
                            'value' => $val['orden_id']
                ));
                $selecciona .= "</div>";

                $status = "<span class=\"tag " . $val['estado'] . " \">" . Driver::t($val['estado']) . "</span>";
                $action = "<div class=\"table-action\">";
                $action1 = "<a class=\"btn btn-primary details\" data-hidden_id='orden_id' data-modal_detalle='detalle-orden-modal'
			    	data-id=\"" . $val['orden_id'] . "\" href=\"#no\">" . t("Detalle") . "</a>";
                $clipboard = "<a title='Click para copiar en el portapapeles datos de orden' class=\"btn btn-success clipboard\" data-id=\"" . $val['orden_id'] . "\" href=\"#no\"><i class='fa fa-copy'></i></a>"; 

                $action .= $action1;
                $action1 .= "&nbsp;|&nbsp;";
                $action1 .= $clipboard;
                $action .= "&nbsp;|&nbsp;";
                $action .= "</div>";


                $feed_data['aaData'][] = array(
                    $selecciona,
                    $val['codigo_orden'] . $action,
                    $val['origen'],
                    $val['direccion_origen'],
                    $val['destino'],
                    $val['direccion_destino'],
                    $val['tipo_servicio'],
                    $date_created,
                    $status,
                    $action1
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actionpedidosTodosMasivoEstadoFilteredList() {
        $aColumns = array(
            'codigo_orden',
            'tipo_servicio',
            'c.nombre',
            'c.apellido',
            'l_destino.nombre',
            'direccion_destino',
            'z_destino.zona',
            'fecha_envio',
            'estado'
        );


        $t = AjaxDataTables::AjaxData($aColumns);
        if (isset($_GET['debug'])) {
            dump($t);
        }

        if (is_array($t) && count($t) >= 1) {
            $sWhere = $t['sWhere'];
            $sOrder = $t['sOrder'];
            $sLimit = $t['sLimit'];
        }



        $and = '';


        if (isset($_GET["search"]) && $_GET["search"] != "") {
            $and .= " AND (UPPER(o.origen) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(c.nombre) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(c.apellido) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(o.direccion_origen) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(l_origen.nombre) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(z_origen.zona) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(o.destino) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(o.direccion_destino) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(l_destino.nombre) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(z_destino.zona) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(o.recibe) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(o.remitente) like  '%" . $_GET["search"] . "%' )";
        }

        if (isset($_GET["fecha_creacion_desde"]) && $_GET["fecha_creacion_desde"] != "") {
            $and .= " AND o.date_created >=  '" . $_GET["fecha_creacion_desde"] . " 00:00:00' ";
        }
        if (isset($_GET["fecha_creacion_hasta"]) && $_GET["fecha_creacion_hasta"] != "") {
            $and .= " AND o.date_created <=  '" . $_GET["fecha_creacion_hasta"] . " 23:59:59' ";
        }
        if (isset($_GET["fecha_envio_desde"]) && $_GET["fecha_envio_desde"] != "") {
            $and .= " AND o.fecha_envio >=  '" . $_GET["fecha_envio_desde"] . " 00:00:00' ";
        }
        if (isset($_GET["fecha_envio_hasta"]) && $_GET["fecha_envio_hasta"] != "") {
            $and .= " AND o.fecha_envio <=  '" . $_GET["fecha_envio_hasta"] . " 23:59:59' ";
        }
        if (isset($_GET["fecha_entrega_desde"]) && $_GET["fecha_entrega_desde"] != "") {
            $and .= " AND o.fecha_entrega >=  '" . $_GET["fecha_entrega_desde"] . " 00:00:00' ";
        }
        if (isset($_GET["fecha_entrega_hasta"]) && $_GET["fecha_entrega_hasta"] != "") {
            $and .= " AND o.fecha_entrega <=  '" . $_GET["fecha_entrega_hasta"] . " 23:59:59' ";
        }
        if (isset($_GET["codigo_orden"]) && $_GET["codigo_orden"] != "") {
            $and .= "  AND UPPER(o.codigo_orden) like  '%" . $_GET["codigo_orden"] . "%' ";
        }
        if (isset($_GET["estado"]) && $_GET["estado"] != "") {
            $and .= "  AND o.estado =  '" . $_GET["estado"] . "' ";
        }
        if (isset($_GET["tipo_servicio"]) && $_GET["tipo_servicio"] != "") {
            $and .= "  AND o.tipo_servicio =  '" . $_GET["tipo_servicio"] . "' ";
        }
        if (isset($_GET["id_mensajero"]) && $_GET["id_mensajero"] != "" && $_GET["id_mensajero"] != "0") {
            $and .= "  AND m.id_mensajero =  '" . $_GET["id_mensajero"] . "' ";
        }
        if (isset($_GET["id_ruta"]) && $_GET["id_ruta"] != "" && $_GET["id_ruta"] != "0") {
            $and .= "  AND r.id_ruta =  '" . $_GET["id_ruta"] . "' ";
        }
        if (isset($_GET["id_cliente"]) && $_GET["id_cliente"] != "" && $_GET["id_cliente"] != "0") {
            $and .= "  AND c.id_cliente =  '" . $_GET["id_cliente"] . "' ";
        }

        $stmt = "SELECT SQL_CALC_FOUND_ROWS o.*,z_destino.zona AS 'zona_destino_nombre',z_origen.zona AS 'zona_origen_nombre',l_destino.nombre AS 'ciudad_destino',l_origen.nombre AS 'ciudad_origen'
		,CONCAT(c.nombre,' ',c.apellido) AS nombres,m.id_mensajero 
                FROM
		{{orden}} o
                INNER JOIN {{zonas}} z_destino on o.zona_destino=z_destino.id
                INNER JOIN {{locacion}} l_destino on z_destino.id_locacion=l_destino.id
                INNER JOIN {{zonas}} z_origen on o.zona_origen=z_origen.id
                INNER JOIN {{locacion}} l_origen on z_origen.id_locacion=l_origen.id
                 LEFT OUTER JOIN {{clientes}} c on c.id_cliente=o.id_cliente
                 LEFT OUTER JOIN {{ruta}} r on o.id_ruta=r.id_ruta  
                 LEFT OUTER JOIN {{mensajero}} m on r.id_mensajero=m.id_mensajero 
		WHERE 1		
		$and
		$sWhere
		ORDER BY o.date_created DESC 
                $sLimit;
		";
        if (isset($_GET['debug'])) {
            dump($stmt);
        }
        $_SESSION['xpress_stmt_pedidosTodosMasivoEstadoList'] = $stmt;

        $DbExt = new DbExt;
        $DbExt->qry("SET SQL_BIG_SELECTS=1");

        if ($res = $DbExt->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $DbExt->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {
                $date_created = AdminFunctions::prettyDate($val['date_created'], true);
                $date_created = Yii::app()->functions->translateDate($date_created);

                $status = "<span class=\"tag " . $val['estado'] . " \">" . Driver::t($val['estado']) . "</span>";
                $nombre_selecciona = "seleccionado_" . $val['codigo_orden'];
                $selecciona = "<div>";
                $selecciona .= CHtml::checkBox($nombre_selecciona, false, array(
                            'class' => "seleccionado",
                            'value' => $val['orden_id']
                ));
                $selecciona .= "</div>";
                $action = "<div>";
                $action1 = "<a class=\"btn btn-primary details\" data-hidden_id='orden_id' data-modal_detalle='detalle-orden-modal' 
			    	data-id=\"" . $val['orden_id'] . "\" href=\"#no\">" . t("Detalle") . "</a>";


                $clipboard = "<a title='Click para copiar en el portapapeles datos de orden' class=\"btn btn-success clipboard\" data-id=\"" . $val['orden_id'] . "\" href=\"#no\"><i class='fa fa-copy'></i></a>"; //aqui

                $action .= $action1;
                $action1 .= "&nbsp;|&nbsp;";
                $action1 .= $clipboard;
                $action .= "&nbsp;|&nbsp;";
                $action .= "</div>";


                $feed_data['aaData'][] = array(
                    $selecciona,
                    $val['codigo_orden'] . $action,
                    $val['origen'],
                    $val['direccion_origen'],
                    $val['destino'],
                    $val['direccion_destino'],
                    $val['tipo_servicio'],
                    $date_created,
                    $status,
                    $action1
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actionrepedidosTodosMasivoEstadoFilteredList() {
        $aColumns = array(
            'codigo_orden',
            'tipo_servicio',
            'c.nombre',
            'c.apellido',
            'l_destino.nombre',
            'direccion_destino',
            'z_destino.zona',
            'fecha_envio',
            'estado'
        );
        $stmt = $_SESSION['xpress_stmt_pedidosTodosMasivoEstadoList'];


        $t = AjaxDataTables::AjaxData($aColumns);
        if (isset($_GET['debug'])) {
            dump($t);
        }


        if (isset($_GET['debug'])) {
            dump($stmt);
        }


        $DbExt = new DbExt;
        $DbExt->qry("SET SQL_BIG_SELECTS=1");

        if ($res = $DbExt->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $DbExt->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {
                $date_created = AdminFunctions::prettyDate($val['date_created'], true);
                $date_created = Yii::app()->functions->translateDate($date_created);

                $status = "<span class=\"tag " . $val['estado'] . " \">" . Driver::t($val['estado']) . "</span>";
                $nombre_selecciona = "seleccionado_" . $val['codigo_orden'];
                $selecciona = "<div>";
                $selecciona .= CHtml::checkBox($nombre_selecciona, false, array(
                            'class' => "seleccionado",
                            'value' => $val['orden_id']
                ));
                $selecciona .= "</div>";
                $action = "<div>";
                $action1 = "<a class=\"btn btn-primary details\" data-hidden_id='orden_id' data-modal_detalle='detalle-orden-modal' 
			    	data-id=\"" . $val['orden_id'] . "\" href=\"#no\">" . t("Detalle") . "</a>";


                $clipboard = "<a title='Click para copiar en el portapapeles datos de orden' class=\"btn btn-success clipboard\" data-id=\"" . $val['orden_id'] . "\" href=\"#no\"><i class='fa fa-copy'></i></a>"; //aqui

                $action .= $action1;
                $action1 .= "&nbsp;|&nbsp;";
                $action1 .= $clipboard;
                $action .= "&nbsp;|&nbsp;";
                $action .= "</div>";


                $feed_data['aaData'][] = array(
                    $selecciona,
                    $val['codigo_orden'] . $action,
                    $val['origen'],
                    $val['direccion_origen'],
                    $val['destino'],
                    $val['direccion_destino'],
                    $val['tipo_servicio'],
                    $date_created,
                    $status,
                    $action1
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actiongetDetalleOrden() {
        if (isset($this->data['orden_id'])) {
            if ($res = Driver::getOrdenId($this->data['orden_id'])) {
                $res['codigo_orden'] = !empty($res['codigo_orden']) ? $res['codigo_orden'] : '';
                $res['estado'] = !empty($res['estado']) ? $res['estado'] : '';
                $res['tipo_servicio'] = !empty($res['tipo_servicio']) ? $res['tipo_servicio'] : '';
                $res['origen'] = !empty($res['origen']) ? $res['origen'] : '';
                $res['direccion_origen'] = !empty($res['direccion_origen']) ? $res['direccion_origen'] : '';
                $res['ciudad_origen'] = !empty($res['ciudad_origen']) ? $res['ciudad_origen'] : '';
                $res['ciudad_origen_id'] = !empty($res['ciudad_origen_id']) ? $res['ciudad_origen_id'] : '0';
                $res['ciudad_destino_id'] = !empty($res['ciudad_destino_id']) ? $res['ciudad_destino_id'] : '0';
                $res['provincia_destino_id'] = !empty($res['provincia_destino_id']) ? $res['provincia_destino_id'] : '0';
                $res['provincia_origen_id'] = !empty($res['provincia_origen_id']) ? $res['provincia_origen_id'] : '0';
                $res['zona_origen'] = !empty($res['zona_origen']) ? $res['zona_origen'] : '0';
                $res['zona_destino'] = !empty($res['zona_destino']) ? $res['zona_destino'] : '0';
                $res['zona_origen_nombre'] = !empty($res['zona_origen_nombre']) ? $res['zona_origen_nombre'] : '';
                $res['remitente'] = !empty($res['remitente']) ? $res['remitente'] : '';
                $res['telefono_remitente'] = !empty($res['telefono_remitente']) ? substr($res['telefono_remitente'],1) : '';
                $res['destino'] = !empty($res['destino']) ? $res['destino'] : '';
                $res['direccion_destino'] = !empty($res['direccion_destino']) ? $res['direccion_destino'] : '';
                $res['ciudad_destino'] = !empty($res['ciudad_destino']) ? $res['ciudad_destino'] : '';
                $res['zona_destino_nombre'] = !empty($res['zona_destino_nombre']) ? $res['zona_destino_nombre'] : '';
                $res['recibe'] = !empty($res['recibe']) ? $res['recibe'] : '';
                $res['date_created'] = !empty($res['date_created']) ? date("Y-m-d g:i a", strtotime($res['date_created'])) : '-';
                $res['fecha_envio'] = !empty($res['fecha_envio']) ? date("Y-m-d g:i a", strtotime($res['fecha_envio'])) : '-';
                $res['fecha_entrega'] = !empty($res['fecha_entrega']) ? date("Y-m-d g:i a", strtotime($res['fecha_entrega'])) : '-';
                $res['telefono_recibe'] = !empty($res['telefono_recibe']) ? substr($res['telefono_recibe'],1) : '';
                $res['id_cliente'] = !empty($res['id_cliente']) ? $res['id_cliente'] : '';
                $res['precio_zona'] = !empty($res['precio_zona']) ? $res['precio_zona'] : '';
                $res['nombres'] = !empty($res['nombres']) ? $res['nombres'] : '';
                $res['orden_id'] = !empty($res['orden_id']) ? $res['orden_id'] : '';
                $res['peso'] = !empty($res['peso']) ? $res['peso'] : '';
                $res['no_gestiones'] = !empty($res['no_gestiones']) ? $res['no_gestiones'] : '';

                /* get task history */
                $history_details = array();
                $history_data = array();
                //if ( $info=Driver::getTaskId($this->data['id'])){		

                if ($info = $res) {
                    if ($history_details = Driver::getHistorialOrdenes($this->data['orden_id'])) {
                        foreach ($history_details as $valh) {
                            $valh['estado'] = $valh['estado'];
                            $valh['detalle'] = !empty($valh['detalle']) ? $valh['detalle'] : '';
                            $valh['nombre_c'] = !empty($valh['nombre_c']) ? $valh['nombre_c'] : '';
                            $valh['apellido_c'] = !empty($valh['apellido_c']) ? $valh['apellido_c'] : '';
                            $valh['nombre_u'] = !empty($valh['nombre_u']) ? $valh['nombre_u'] : '';
                            $valh['apellido_u'] = !empty($valh['apellido_u']) ? $valh['apellido_u'] : '';
                            $valh['usuario_id'] = !empty($valh['usuario_id']) ? $valh['usuario_id'] : '';
                            $valh['cliente_id'] = !empty($valh['cliente_id']) ? $valh['cliente_id'] : '';
                            $valh['date_created'] = Yii::app()->functions->FormatDateTime($valh['date_created']);


                            $history_data[] = $valh;
                        }
                    } else {
                        $history_data = '';
                    }
                }

                $res['history_data'] = $history_data;

                // get the order details
                $order_details = '';

                //dump($res);

                $this->code = 1;
                $this->msg = "OK";
                $this->details = $res;
                //dump($this->details);
            } else
                $this->msg = Driver::t("No se encontró el registro");
        } else
            $this->msg = Driver::t("falta el parámetro id");
        $this->jsonResponse();
    }

    public function actiongetEditOrden() {
        $this->actiongetDetalleOrden();
    }

    public function actiongetClipboardOrden() {
        $this->actiongetDetalleOrden();
    }

    public function actiongetEditRuta() {
        $this->actiongetDetalleRuta();
    }

    public function actiongetPagarOrden() {
        $this->actiongetDetalleOrden();
    }

    public function actionbuscarOrden() {
        if (isset($this->data['codigo_orden_busqueda'])) {
            if ($res = Driver::getOrdenIdPorCodigoYaAsignado($this->data['codigo_orden_busqueda'])) {
                $this->msg = "La orden ya está asignada a una ruta, use la funcionalidad de migración de órdenes si desea asignar la ruta mencionada";
                $this->jsonResponse();
            }


            if ($res = Driver::getOrdenIdPorCodigo($this->data['codigo_orden_busqueda'])) {
                $res['orden_id'] = !empty($res['orden_id']) ? $res['orden_id'] : '';


                $this->code = 1;
                $this->msg = "OK";
                $this->details = $res;
            } else
                $this->msg = Driver::t("No se encontró el registro");
        } else
            $this->msg = Driver::t("falta el parámetro id");
        $this->jsonResponse();
    }

    public function actionAddOrden() {

        /* dump($this->data);
          die(); */

        $DbExt = new DbExt;
        $req = array(
            'tipo_servicio' => Driver::t("Por favor ingrese tipo de servicio"),
            'ciudad_origen_id' => Driver::t("Por favor ingrese ciudad origen"),
            'ciudad_destino_id' => Driver::t("Por favor ingrese ciudad destino"),
            'id_cliente' => Driver::t("Por favor ingrese el cliente asociado a la orden")
        );
        $Validator = new Validator;
        $Validator->required($req, $this->data);
        if ($Validator->validate()) {

            $params = array(
                'tipo_servicio' => isset($this->data['tipo_servicio']) ? $this->data['tipo_servicio'] : '',
                'origen' => isset($this->data['origen']) ? $this->data['origen'] : '',
                'direccion_origen' => isset($this->data['direccion_origen']) ? $this->data['direccion_origen'] : '',
                'remitente' => isset($this->data['remitente']) ? $this->data['remitente'] : '',
                'telefono_remitente' => isset($this->data['telefono_remitente']) ? $this->data['telefono_remitente'] : '',
                'destino' => isset($this->data['destino']) ? $this->data['destino'] : '',
                'estado' => isset($this->data['estado']) ? $this->data['estado'] : '',
                'detalle' => isset($this->data['detalle']) ? $this->data['detalle'] : '',
                'direccion_destino' => isset($this->data['direccion_destino']) ? $this->data['direccion_destino'] : '',
                'origen_orden' => 'ADMIN',
                'recibe' => isset($this->data['recibe']) ? $this->data['recibe'] : '',
                'telefono_recibe' => isset($this->data['telefono_recibe']) ? $this->data['telefono_recibe'] : '',
                'date_created' => AdminFunctions::dateNow(),
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'id_cliente' => isset($this->data['id_cliente']) ? $this->data['id_cliente'] : null,
                'peso' => isset($this->data['peso']) ? $this->data['peso'] : '',
                'no_gestiones' => isset($this->data['no_gestiones']) ? $this->data['no_gestiones'] : '',
                'link_ubicacion_origen'=> isset($this->data['link_ubicacion_origen']) ? $this->data['link_ubicacion_origen']:'',
                'link_ubicacion_destino'=> isset($this->data['link_ubicacion_destino']) ? $this->data['link_ubicacion_destino']:'',
            );
            try {
                if (is_numeric($this->data['orden_id'])) {

                    unset($params['date_created']);
                    unset($params['user_id']);
                    unset($params['task_token']);
                    $params['date_modified'] = AdminFunctions::dateNow();


                    if ($DbExt->updateData("{{orden}}", $params, 'orden_id', $this->data['orden_id'])) {
                        $this->code = 1;
                        $this->msg = Driver::t("Operación exitosa");


                        $params_history = array(
                            'orden_id' => $this->data['orden_id'],
                            'detalle' => $params['detalle'],
                            'estado' => $params['estado'],
                            'date_created' => AdminFunctions::dateNow(),
                            'ip_address' => $_SERVER['REMOTE_ADDR'],
                            'cliente_id' => $params['id_cliente']
                        );
                        $DbExt->insertData('{{historial_ordenes}}', $params_history);
                    } else
                        $this->msg = Driver::t("Problema al actualizar");
                } else {

                    $params['estado'] = 'Creado';

                    if ($params['id_cliente'] == null || $params['id_cliente'] == "") {
                        $this->msg = Driver::t("Debe estar asignado a un cliente");
                        $this->jsonResponse();
                    }

                    if ($resCli = AdminFunctions::getClienteById($params['id_cliente'])) {

                        if ($resCli['prefijo'] == null || $resCli['prefijo'] == "") {
                            $this->msg = Driver::t("El cliente debe tener un prefijo asignado");
                            $this->jsonResponse();
                        }

                        if ($DbExt->insertData("{{secuencial}}", array('prefijo' => $resCli['prefijo']))) {
                            $secuencial = Yii::app()->db->getLastInsertID();
                            $params['codigo_orden'] = $resCli['prefijo'] . $secuencial;
                            if ($DbExt->insertData("{{orden}}", $params)) {
                                $orden_id = Yii::app()->db->getLastInsertID();

                                $params_history = array(
                                    'orden_id' => $orden_id,
                                    'detalle' => $params['detalle'],
                                    'estado' => 'Creado',
                                    'date_created' => AdminFunctions::dateNow(),
                                    'ip_address' => $_SERVER['REMOTE_ADDR'],
                                    'cliente_id' => $params['id_cliente']
                                );
                                $DbExt->insertData('{{historial_ordenes}}', $params_history);
                                $this->code = 1;
                                $this->msg = Driver::t("Operación exitosa");
                            } else
                                $this->msg = Driver::t("Error al insertar");
                        } else
                            $this->msg = Driver::t("Error al insertar");
                    } else
                        $this->msg = Driver::t("No existe el cliente");
                }
            } catch (Exception $e) {
                $this->msg = $e->getMessage();
            }
        } else
            $this->msg = $Validator->getErrorAsHTML();
        $this->jsonResponse();
    }

    public function actionAddOrden2() {

        /* dump($this->data);
          die(); */

        $DbExt = new DbExt;
        $req = array(
            'tipo_servicio' => Driver::t("Por favor ingrese tipo de servicio"),
            'zona_origen' => Driver::t("Por favor ingrese zona origen"),
            'zona_destino' => Driver::t("Por favor ingrese zona destino"),
            'provincia_origen_id' => Driver::t("Por favor ingrese provincia origen"),
            'ciudad_origen_id' => Driver::t("Por favor ingrese ciudad origen"),
            'provincia_destino_id' => Driver::t("Por favor ingrese provincia destino"),
            'ciudad_destino_id' => Driver::t("Por favor ingrese ciudad destino"),
            'id_cliente' => Driver::t("Por favor ingrese el cliente asociado a la orden")
        );
        $Validator = new Validator;
        $Validator->required($req, $this->data);
        if ($Validator->validate()) {

            $params = array(
                'tipo_servicio' => isset($this->data['tipo_servicio']) ? $this->data['tipo_servicio'] : '',
                'origen' => isset($this->data['origen']) ? $this->data['origen'] : '',
                'direccion_origen' => isset($this->data['direccion_origen']) ? $this->data['direccion_origen'] : '',
                'zona_origen' => is_numeric($this->data['zona_origen']) ? $this->data['zona_origen'] : '',
                'remitente' => isset($this->data['remitente']) ? $this->data['remitente'] : '',
                'telefono_remitente' => isset($this->data['telefono_remitente']) ? $this->data['telefono_remitente'] : '',
                'destino' => isset($this->data['destino']) ? $this->data['destino'] : '',
                'estado' => isset($this->data['estado']) ? $this->data['estado'] : '',
                'detalle' => isset($this->data['detalle']) ? $this->data['detalle'] : '',
                'direccion_destino' => isset($this->data['direccion_destino']) ? $this->data['direccion_destino'] : '',
                'zona_destino' => is_numeric($this->data['zona_destino']) ? $this->data['zona_destino'] : '',
                'origen_orden' => 'ADMIN',
                'recibe' => isset($this->data['recibe']) ? $this->data['recibe'] : '',
                'telefono_recibe' => isset($this->data['telefono_recibe']) ? $this->data['telefono_recibe'] : '',
                'date_created' => AdminFunctions::dateNow(),
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'id_cliente' => isset($this->data['id_cliente']) ? $this->data['id_cliente'] : null,
                'peso' => isset($this->data['peso']) ? $this->data['peso'] : '',
                'no_gestiones' => isset($this->data['no_gestiones']) ? $this->data['no_gestiones'] : '',
            );
            try {
                if (is_numeric($this->data['orden_id'])) {

                    unset($params['date_created']);
                    unset($params['user_id']);
                    unset($params['task_token']);
                    $params['date_modified'] = AdminFunctions::dateNow();


                    if ($DbExt->updateData("{{orden}}", $params, 'orden_id', $this->data['orden_id'])) {
                        $this->code = 1;
                        $this->msg = Driver::t("Operación exitosa");


                        $params_history = array(
                            'orden_id' => $this->data['orden_id'],
                            'detalle' => $params['detalle'],
                            'estado' => $params['estado'],
                            'date_created' => AdminFunctions::dateNow(),
                            'ip_address' => $_SERVER['REMOTE_ADDR'],
                            'cliente_id' => $params['id_cliente']
                        );
                        $DbExt->insertData('{{historial_ordenes}}', $params_history);
                    } else
                        $this->msg = Driver::t("Problema al actualizar");
                } else {

                    $params['estado'] = 'Creado';

                    if ($params['id_cliente'] == null || $params['id_cliente'] == "") {
                        $this->msg = Driver::t("Debe estar asignado a un cliente");
                        $this->jsonResponse();
                    }

                    if ($resCli = AdminFunctions::getClienteById($params['id_cliente'])) {

                        if ($resCli['prefijo'] == null || $resCli['prefijo'] == "") {
                            $this->msg = Driver::t("El cliente debe tener un prefijo asignado");
                            $this->jsonResponse();
                        }

                        if ($DbExt->insertData("{{secuencial}}", array('prefijo' => $resCli['prefijo']))) {
                            $secuencial = Yii::app()->db->getLastInsertID();
                            $params['codigo_orden'] = $resCli['prefijo'] . $secuencial;
                            if ($DbExt->insertData("{{orden}}", $params)) {
                                $orden_id = Yii::app()->db->getLastInsertID();

                                $params_history = array(
                                    'orden_id' => $orden_id,
                                    'detalle' => $params['detalle'],
                                    'estado' => 'Creado',
                                    'date_created' => AdminFunctions::dateNow(),
                                    'ip_address' => $_SERVER['REMOTE_ADDR'],
                                    'cliente_id' => $params['id_cliente']
                                );
                                $DbExt->insertData('{{historial_ordenes}}', $params_history);
                                $this->code = 1;
                                $this->msg = Driver::t("Operación exitosa");
                            } else
                                $this->msg = Driver::t("Error al insertar");
                        } else
                            $this->msg = Driver::t("Error al insertar");
                    } else
                        $this->msg = Driver::t("No existe el cliente");
                }
            } catch (Exception $e) {
                $this->msg = $e->getMessage();
            }
        } else
            $this->msg = $Validator->getErrorAsHTML();
        $this->jsonResponse();
    }

    public function actionDeleteRecords() {
        if (isset($this->data['tbl']) && isset($this->data['whereid'])) {
            try {
                $wherefield = $this->data['whereid'];
                $tbl = $this->data['tbl'];
                $stmt = "
			DELETE FROM
			{{{$tbl}}}
			WHERE
			$wherefield=" . Driver::q($this->data['id']) . "
			";
                //dump($stmt);
                $DbExt = new DbExt;
                $DbExt->qry($stmt);
                $this->code = 1;
                $this->msg = Driver::t("Operación exitosa");
            } catch (Exception $e) {
                $this->msg = 'Error no se puede eliminar, el registro está asignado en otras tablas';
                $this->code = 2;
            }
        } else
            $this->msg = Driver::t("Parámetros faltantes");
        $this->jsonResponse();
    }

    public function actionmensajeroList() {
        $aColumns = array(
            'cedula',
            'a.nombre',
            'a.apellido',
            'email',
            'tipo_vehiculo',
            'placa',
            'status'
        );
        $t = AjaxDataTables::AjaxData($aColumns);
        if (isset($_GET['debug'])) {
            dump($t);
        }

        if (is_array($t) && count($t) >= 1) {
            $sWhere = $t['sWhere'];
            $sOrder = $t['sOrder'];
            $sLimit = $t['sLimit'];
        }

        $and = '';


        $stmt = "SELECT SQL_CALC_FOUND_ROWS a.*,CONCAT(a.nombre,' ',a.apellido) AS nombres 
		FROM
		{{mensajero}} a
		WHERE 1		
		$and
		$sWhere
		$sOrder
		$sLimit
		";
        if (isset($_GET['debug'])) {
            dump($stmt);
        }

        $_SESSION['kartero_stmt_mensajeros'] = $stmt;

        $DbExt = new DbExt;
        if ($res = $DbExt->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $DbExt->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {
                $date_created = Yii::app()->functions->prettyDate($val['date_created'], true);
                $date_created = Yii::app()->functions->translateDate($date_created);
                $cv = '';
                $foto = '';

                if (!empty($val['foto_perfil'])) {
                    if (file_exists(Driver::driverUploadPath() . "/" . $val['foto_perfil'])) {
                        $val['foto_perfil_url'] = websiteUrl() . "/upload/photo/" . $val['foto_perfil'];
                        $foto = "<img style='height:50px; width=50px;' src='" . $val['foto_perfil_url'] . "' />";
                    }
                }

                if (!empty($val['cv'])) {
                    if (file_exists(Driver::driverUploadCVPath() . "/" . $val['cv'])) {
                        $val['cv_url'] = websiteUrl() . "/upload/cv/" . $val['cv'];
                        $cv = "<a target='_blank' href='" . $val['cv_url'] . "'> Descargar CV</a>";
                    }
                }

                $id = $val['id_mensajero'];
                $p = "id=$id" . "&tbl=mensajero&whereid=id_mensajero";
                $status = "<span class=\"tag " . $val['status'] . " \">" . Driver::t($val['status']) . "</span>";
                $action = "<div>";
                $action1 = "<a class=\"btn btn-primary edit-mensajero\"  data-id_mensajero=\"$id\" 
			    class=\"table-edit\" href=\"#no\">" . Driver::t("Editar") . "</a>";
                $action1 .= "&nbsp;|&nbsp;";
                $action1 .= "<a class=\"btn btn-danger delete\"  data-data=\"$p\"  data-id=\"$id\" class=\"table-delete\" href=\"#no\">" . Driver::t("Eliminar") . "</a>";

                $action .= $action1;

                $action .= "</div>";
                $feed_data['aaData'][] = array(
                    $foto,
                    $val['cedula'],
                    $val['nombres'],
                    $val['email'],
                    $val['telefono'],
                    $val['tipo_vehiculo'],
                    $val['placa'],
                    $cv,
                    $status,
                    $action
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actionaddMensajero() {

        $DbExt = new DbExt;
        $params = array(
            'cedula' => isset($this->data['cedula']) ? $this->data['cedula'] : '',
            'nombre' => isset($this->data['nombre']) ? $this->data['nombre'] : '',
            'apellido' => isset($this->data['apellido']) ? $this->data['apellido'] : '',
            'email' => isset($this->data['email']) ? $this->data['email'] : '',
            'telefono' => isset($this->data['telefono']) ? $this->data['telefono'] : '',
            'tipo_vehiculo' => isset($this->data['tipo_vehiculo']) ? $this->data['tipo_vehiculo'] : '',
            'descripcion_vehiculo' => isset($this->data['descripcion_vehiculo']) ? $this->data['descripcion_vehiculo'] : '',
            'placa' => isset($this->data['placa']) ? $this->data['placa'] : '',
            'color' => isset($this->data['color']) ? $this->data['color'] : '',
            'status' => isset($this->data['status']) ? $this->data['status'] : '',
            'date_created' => AdminFunctions::dateNow(),
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'foto_perfil' => isset($this->data['foto_perfil']) ? $this->data['foto_perfil'] : '',
            'cv' => isset($this->data['cv']) ? $this->data['cv'] : ''
        );


        if (!isset($this->data['id_mensajero'])) {
            $this->data['id_mensajero'] = '';
        }

        if (is_numeric($this->data['id_mensajero'])) {
            unset($params['date_created']);
            $params['date_modified'] = AdminFunctions::dateNow();




            if ($DbExt->updateData("{{mensajero}}", $params, 'id_mensajero', $this->data['id_mensajero'])) {
                $this->code = 1;
                $this->msg = Driver::t("Operación exitosa");
                $this->details = 'new-agent';

                /* update team */
                //Driver::updateTeamDriver($this->data['id'],$params['team_id']);
            } else
                $this->msg = Driver::t("Error al actualizar");
        } else {

            if ($DbExt->insertData('{{mensajero}}', $params)) {
                $this->code = 1;
                $this->msg = Driver::t("Operación exitosa");
            } else
                $this->msg = Driver::t("Error al insertar");
        }
        $this->jsonResponse();
    }

    public function actiongetEditMensajero() {

        if (isset($this->data['id_mensajero'])) {
            if ($res = Driver::mensajeroInfo($this->data['id_mensajero'])) {

                if (!empty($res['foto_perfil'])) {
                    if (file_exists(Driver::driverUploadPath() . "/" . $res['foto_perfil'])) {
                        $res['foto_perfil_url'] = websiteUrl() . "/upload/photo/" . $res['foto_perfil'];
                    }
                }

                if (!empty($res['cv'])) {
                    if (file_exists(Driver::driverUploadCVPath() . "/" . $res['cv'])) {
                        $res['cv_url'] = websiteUrl() . "/upload/cv/" . $res['cv'];
                    }
                }

                $this->code = 1;
                $this->msg = Driver::t("Operación exitosa");
                $this->details = $res;
            } else
                $this->msg = Driver::t("Registro no encontrado");
        } else
            $this->msg = Driver::t("Faltan parámetros");
        $this->jsonResponse();
    }

    public function actionGetCiudadList() {
        if ($res = Driver::getLocacionList($this->data['id_padre'])) {
            $this->msg = "OK";
            $this->code = 1;
            $this->details = $res;
        } else
            $this->msg = t("Ciudades no encontradas");
        $this->jsonResponse();
    }

    public function actionGetZonaList() {
        if ($res = Driver::getZonaList($this->data['id_padre'])) {
            $this->msg = "OK";
            $this->code = 1;
            $this->details = $res;
        } else
            $this->msg = t("Zonas no encontradas");
        $this->jsonResponse();
    }

    public function actionrutasList() {
        $aColumns = array(
            'r.fecha_ruta',
            'm.nombre',
            'm.apellido',
            'r.detalle',
            'status'
        );


        $t = AjaxDataTables::AjaxData($aColumns);
        if (isset($_GET['debug'])) {
            dump($t);
        }

        if (is_array($t) && count($t) >= 1) {
            $sWhere = $t['sWhere'];
            $sOrder = $t['sOrder'];
            $sLimit = $t['sLimit'];
        }

        $and = '';

        $stmt = "SELECT SQL_CALC_FOUND_ROWS r.*
		,CONCAT(m.nombre,' ',m.apellido) AS nombres,m.foto_perfil,m.cv 
                FROM
                {{ruta}} r
                LEFT OUTER JOIN {{mensajero}} m on r.id_mensajero=m.id_mensajero
		WHERE 1		
		$and
		$sWhere
		ORDER BY r.fecha_ruta DESC limit 5000;
		";
        if (isset($_GET['debug'])) {
            dump($stmt);
        }
        $stmt=$_SESSION['xpress_stmt_rutasList'];

        $DbExt = new DbExt;
        $DbExt->qry("SET SQL_BIG_SELECTS=1");

        if ($res = $DbExt->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $DbExt->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {
                $fecha_ruta = AdminFunctions::prettyDate($val['fecha_ruta'], false);
                $fecha_ruta = Yii::app()->functions->translateDate($fecha_ruta);
                $status = "<span class=\"tag " . $val['status'] . " \">" . Driver::t($val['status']) . "</span>";
                $action = "<div class=\"table-action\">";
                $action1 = "<a class=\"btn btn-primary details\" data-hidden_id='id_ruta' data-modal_detalle='detalle-ruta-modal'
			    	data-id=\"" . $val['id_ruta'] . "\" href=\"#no\">" . t("Detalle") . "</a>";


                $action .= $action1;
                $action .= "&nbsp;|&nbsp;";

                $action .= "</div>";

                $cv = '';
                $foto = '';

                if (!empty($val['foto_perfil'])) {
                    if (file_exists(Driver::driverUploadPath() . "/" . $val['foto_perfil'])) {
                        $val['foto_perfil_url'] = websiteUrl() . "/upload/photo/" . $val['foto_perfil'];
                        $foto = "<img style='height:50px; width=50px;' src='" . $val['foto_perfil_url'] . "' />";
                    }
                }

                if (!empty($val['cv'])) {
                    if (file_exists(Driver::driverUploadCVPath() . "/" . $val['cv'])) {
                        $val['cv_url'] = websiteUrl() . "/upload/cv/" . $val['cv'];
                        $cv = "<a target='_blank' href='" . $val['cv_url'] . "'> Descargar CV</a>";
                    }
                }


                $feed_data['aaData'][] = array(
                    $fecha_ruta,
                    $val['detalle'],
                    $val['nombres'] . "<br/>" . $foto . "<br/>" . $cv,
                    $status,
                    $action1
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actionrutasFilteredList() {
        $aColumns = array(
            'r.fecha_ruta',
            'm.nombre',
            'm.apellido',
            'r.detalle',
            'status'
        );


        $t = AjaxDataTables::AjaxData($aColumns);
        if (isset($_GET['debug'])) {
            dump($t);
        }

        if (is_array($t) && count($t) >= 1) {
            $sWhere = $t['sWhere'];
            $sOrder = $t['sOrder'];
            $sLimit = $t['sLimit'];
        }

        $and = '';

        if (isset($_GET["search"]) && $_GET["search"] != "") {
            $and .= " AND (UPPER(r.detalle) like  '%" . $_GET["search"] . "%' )";
        }

        if (isset($_GET["fecha_creacion_desde"]) && $_GET["fecha_creacion_desde"] != "") {
            $and .= " AND r.date_created >=  '" . $_GET["fecha_creacion_desde"] . " 00:00:00' ";
        }
        if (isset($_GET["fecha_creacion_hasta"]) && $_GET["fecha_creacion_hasta"] != "") {
            $and .= " AND r.date_created <=  '" . $_GET["fecha_creacion_hasta"] . " 23:59:59' ";
        }
        if (isset($_GET["fecha_ruta_desde"]) && $_GET["fecha_ruta_desde"] != "") {
            $and .= " AND r.fecha_ruta >=  '" . $_GET["fecha_ruta_desde"] . " 00:00:00' ";
        }
        if (isset($_GET["fecha_ruta_hasta"]) && $_GET["fecha_ruta_hasta"] != "") {
            $and .= " AND r.fecha_ruta <=  '" . $_GET["fecha_ruta_hasta"] . " 23:59:59' ";
        }

        if (isset($_GET["status"]) && $_GET["status"] != "") {
            $and .= "  AND r.status =  '" . $_GET["status"] . "' ";
        }

        if (isset($_GET["id_mensajero"]) && $_GET["id_mensajero"] != "" && $_GET["id_mensajero"] != "0") {
            $and .= "  AND m.id_mensajero =  '" . $_GET["id_mensajero"] . "' ";
        }

        $stmt = "SELECT SQL_CALC_FOUND_ROWS r.*
		,CONCAT(m.nombre,' ',m.apellido) AS nombres,m.foto_perfil,m.cv 
                FROM
                {{ruta}} r
                LEFT OUTER JOIN {{mensajero}} m on r.id_mensajero=m.id_mensajero
		WHERE 1		
		$and
		$sWhere
		ORDER BY r.fecha_ruta DESC limit 5000;
		";
        if (isset($_GET['debug'])) {
            dump($stmt);
        }
        $_SESSION['xpress_stmt_rutasList'] = $stmt;

        $DbExt = new DbExt;
        $DbExt->qry("SET SQL_BIG_SELECTS=1");

        if ($res = $DbExt->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $DbExt->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {
                $fecha_ruta = AdminFunctions::prettyDate($val['fecha_ruta'], false);
                $fecha_ruta = Yii::app()->functions->translateDate($fecha_ruta);
                $status = "<span class=\"tag " . $val['status'] . " \">" . Driver::t($val['status']) . "</span>";
                $action = "<div class=\"table-action\">";
                $action1 = "<a class=\"btn btn-primary details\" data-hidden_id='id_ruta' data-modal_detalle='detalle-ruta-modal'
			    	data-id=\"" . $val['id_ruta'] . "\" href=\"#no\">" . t("Detalle") . "</a>";


                $action .= $action1;
                $action .= "&nbsp;|&nbsp;";

                $action .= "</div>";

                $cv = '';
                $foto = '';

                if (!empty($val['foto_perfil'])) {
                    if (file_exists(Driver::driverUploadPath() . "/" . $val['foto_perfil'])) {
                        $val['foto_perfil_url'] = websiteUrl() . "/upload/photo/" . $val['foto_perfil'];
                        $foto = "<img style='height:50px; width=50px;' src='" . $val['foto_perfil_url'] . "' />";
                    }
                }

                if (!empty($val['cv'])) {
                    if (file_exists(Driver::driverUploadCVPath() . "/" . $val['cv'])) {
                        $val['cv_url'] = websiteUrl() . "/upload/cv/" . $val['cv'];
                        $cv = "<a target='_blank' href='" . $val['cv_url'] . "'> Descargar CV</a>";
                    }
                }


                $feed_data['aaData'][] = array(
                    $fecha_ruta,
                    $val['detalle'],
                    $val['nombres'] . "<br/>" . $foto . "<br/>" . $cv,
                    $status,
                    $action1
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actiongetDetalleRuta() {
        if (isset($this->data['id_ruta'])) {
            if ($res = Driver::getRutaId($this->data['id_ruta'])) {
                if (!empty($res['foto_perfil'])) {
                    if (file_exists(Driver::driverUploadPath() . "/" . $res['foto_perfil'])) {
                        $res['foto_perfil_url'] = websiteUrl() . "/upload/photo/" . $res['foto_perfil'];
                        $res['foto_perfil_url'] = "<img style='height:100px; width=100px;' src='" . $res['foto_perfil_url'] . "' />";
                    }
                }

                if (!empty($res['cv'])) {
                    if (file_exists(Driver::driverUploadCVPath() . "/" . $res['cv'])) {
                        $res['cv_url'] = websiteUrl() . "/upload/cv/" . $res['cv'];
                        $res['cv_url'] = "<a target='_blank' href='" . $res['cv_url'] . "'> Descargar CV</a>";
                    }
                }
                $this->code = 1;
                $this->msg = "OK";
                $this->details = $res;
            } else
                $this->msg = Driver::t("No se encontró el registro");
        } else
            $this->msg = Driver::t("falta el parámetro id_ruta");
        $this->jsonResponse();
    }

    public function actionpedidosPorRutaList() {
        $aColumns = array(
            'codigo_orden',
            'tipo_servicio',
            'c.nombre',
            'c.apellido',
            'l_destino.nombre',
            'direccion_destino',
            'z_destino.zona',
            'fecha_envio',
            'estado'
        );


        $t = AjaxDataTables::AjaxData($aColumns);
        if (isset($_GET['debug'])) {
            dump($t);
        }

        if (is_array($t) && count($t) >= 1) {
            $sWhere = $t['sWhere'];
            $sOrder = $t['sOrder'];
            $sLimit = $t['sLimit'];
        }

        $and = '';
        // $and = " AND id_cliente =" . Driver::q(Driver::getClienteId()) . "  ";
        $and = " AND o.id_ruta =" . $_GET["id_ruta"];

        $stmt = "SELECT SQL_CALC_FOUND_ROWS o.*,z_destino.zona AS 'zona_destino_nombre',z_origen.zona AS 'zona_origen_nombre',l_destino.nombre AS 'ciudad_destino',l_origen.nombre AS 'ciudad_origen'
		,CONCAT(c.nombre,' ',c.apellido) AS nombres
                FROM
		{{orden}} o
                INNER JOIN {{zonas}} z_destino on o.zona_destino=z_destino.id
                INNER JOIN {{locacion}} l_destino on z_destino.id_locacion=l_destino.id
                INNER JOIN {{zonas}} z_origen on o.zona_origen=z_origen.id
                INNER JOIN {{locacion}} l_origen on z_origen.id_locacion=l_origen.id
                LEFT OUTER JOIN {{clientes}} c on c.id_cliente=o.id_cliente
		WHERE 1		
		$and
		$sWhere
		ORDER BY o.date_created DESC 
                $sLimit;
		";
        if (isset($_GET['debug'])) {
            dump($stmt);
        }
        $_SESSION['xpress_stmt_pedidosPorRutaList'] = $stmt;

        $DbExt = new DbExt;
        $DbExt->qry("SET SQL_BIG_SELECTS=1");

        if ($res = $DbExt->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $DbExt->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {
                $date_created = AdminFunctions::prettyDate($val['date_created'], true);
                $date_created = Yii::app()->functions->translateDate($date_created);
                $status = "<span class=\"tag " . $val['estado'] . " \">" . Driver::t($val['estado']) . "</span>";
                $action = "<div class=\"table-action\">";
                $action1 = "<a class=\"btn btn-primary details\" data-hidden_id='orden_id' data-modal_detalle='detalle-orden-modal'
			    	data-id=\"" . $val['orden_id'] . "\" href=\"#no\">" . t("Detalle") . "</a>";


                $action .= $action1;
                $action .= "&nbsp;|&nbsp;";

                $action .= "</div>";


                $feed_data['aaData'][] = array(
                    $val['codigo_orden'] . $action,
                    $val['origen'],
                    $val['direccion_origen'],
                    $val['destino'],
                    $val['direccion_destino'],
                    $val['tipo_servicio'],
                    $date_created,
                    $status,
                    $action1
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actionpedidosPorRutaListFilteredList() {
        $aColumns = array(
            'codigo_orden',
            'tipo_servicio',
            'c.nombre',
            'c.apellido',
            'l_destino.nombre',
            'direccion_destino',
            'z_destino.zona',
            'fecha_envio',
            'estado'
        );


        $t = AjaxDataTables::AjaxData($aColumns);
        if (isset($_GET['debug'])) {
            dump($t);
        }

        if (is_array($t) && count($t) >= 1) {
            $sWhere = $t['sWhere'];
            $sOrder = $t['sOrder'];
            $sLimit = $t['sLimit'];
        }

        $and = '';
        // $and = " AND id_cliente =" . Driver::q(Driver::getClienteId()) . "  ";
        $and = " AND o.id_ruta =" . $_GET["id_ruta"];


        if (isset($_GET["search"]) && $_GET["search"] != "") {
            $and .= " AND (UPPER(o.origen) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(c.nombre) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(c.apellido) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(o.direccion_origen) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(l_origen.nombre) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(z_origen.zona) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(o.destino) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(o.direccion_destino) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(l_destino.nombre) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(z_destino.zona) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(o.recibe) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(o.remitente) like  '%" . $_GET["search"] . "%' )";
        }

        if (isset($_GET["fecha_creacion_desde"]) && $_GET["fecha_creacion_desde"] != "") {
            $and .= " AND o.date_created >=  '" . $_GET["fecha_creacion_desde"] . " 00:00:00' ";
        }
        if (isset($_GET["fecha_creacion_hasta"]) && $_GET["fecha_creacion_hasta"] != "") {
            $and .= " AND o.date_created <=  '" . $_GET["fecha_creacion_hasta"] . " 23:59:59' ";
        }
        if (isset($_GET["fecha_envio_desde"]) && $_GET["fecha_envio_desde"] != "") {
            $and .= " AND o.fecha_envio >=  '" . $_GET["fecha_envio_desde"] . " 00:00:00' ";
        }
        if (isset($_GET["fecha_envio_hasta"]) && $_GET["fecha_envio_hasta"] != "") {
            $and .= " AND o.fecha_envio <=  '" . $_GET["fecha_envio_hasta"] . " 23:59:59' ";
        }
        if (isset($_GET["fecha_entrega_desde"]) && $_GET["fecha_entrega_desde"] != "") {
            $and .= " AND o.fecha_entrega >=  '" . $_GET["fecha_entrega_desde"] . " 00:00:00' ";
        }
        if (isset($_GET["fecha_entrega_hasta"]) && $_GET["fecha_entrega_hasta"] != "") {
            $and .= " AND o.fecha_entrega <=  '" . $_GET["fecha_entrega_hasta"] . " 23:59:59' ";
        }
        if (isset($_GET["codigo_orden"]) && $_GET["codigo_orden"] != "") {
            $and .= "  AND UPPER(o.codigo_orden) like  '%" . $_GET["codigo_orden"] . "%' ";
        }
        if (isset($_GET["estado"]) && $_GET["estado"] != "") {
            $and .= "  AND o.estado =  '" . $_GET["estado"] . "' ";
        }
        if (isset($_GET["tipo_servicio"]) && $_GET["tipo_servicio"] != "" && $_GET["tipo_servicio"] != "0") {
            $and .= "  AND o.tipo_servicio =  '" . $_GET["tipo_servicio"] . "' ";
        }
        if (isset($_GET["id_mensajero"]) && $_GET["id_mensajero"] != "" && $_GET["id_mensajero"] != "0") {
            $and .= "  AND m.id_mensajero =  '" . $_GET["id_mensajero"] . "' ";
        }

        $stmt = "SELECT SQL_CALC_FOUND_ROWS o.*,z_destino.zona AS 'zona_destino_nombre',z_origen.zona AS 'zona_origen_nombre',l_destino.nombre AS 'ciudad_destino',l_origen.nombre AS 'ciudad_origen'
		,CONCAT(c.nombre,' ',c.apellido) AS nombres,m.id_mensajero
                FROM
		{{orden}} o
                INNER JOIN {{zonas}} z_destino on o.zona_destino=z_destino.id
                INNER JOIN {{locacion}} l_destino on z_destino.id_locacion=l_destino.id
                INNER JOIN {{zonas}} z_origen on o.zona_origen=z_origen.id
                INNER JOIN {{locacion}} l_origen on z_origen.id_locacion=l_origen.id
                LEFT OUTER JOIN {{clientes}} c on c.id_cliente=o.id_cliente
                INNER JOIN {{ruta}} r on o.id_ruta=r.id_ruta  
                INNER JOIN {{mensajero}} m on r.id_mensajero=m.id_mensajero 
		WHERE 1		
		$and
		$sWhere
		ORDER BY o.date_created DESC 
                $sLimit;
		";
        if (isset($_GET['debug'])) {
            dump($stmt);
        }

        $_SESSION['xpress_stmt_pedidosPorRutaList'] = $stmt;

        $DbExt = new DbExt;
        $DbExt->qry("SET SQL_BIG_SELECTS=1");

        if ($res = $DbExt->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $DbExt->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {
                $status = "<span class=\"tag " . $val['estado'] . " \">" . Driver::t($val['estado']) . "</span>";
                $action = "<div class=\"table-action\">";
                $action1 = "<a class=\"btn btn-primary details\" data-hidden_id='orden_id' data-modal_detalle='detalle-orden-modal'
			    	data-id=\"" . $val['orden_id'] . "\" href=\"#no\">" . t("Detalle") . "</a>";


                $action .= $action1;
                $action .= "&nbsp;|&nbsp;";

                $action .= "</div>";


                $feed_data['aaData'][] = array(
                    $val['codigo_orden'] . $action,
                    $val['origen'],
                    $val['direccion_origen'],
                    $val['destino'],
                    $val['direccion_destino'],
                    $val['tipo_servicio'],
                    $date_created,
                    $status,
                    $action1
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actionchangeStatusRuta() {
        $req = array(
            'id_ruta' => Driver::t("Id de ruta es requerida"),
            'status' => Driver::t("Por favor escoja el estado"),
        );
        $Validator = new Validator;
        $Validator->required($req, $this->data);
        if ($Validator->validate()) {
            if ($res = Driver::getRutaId($this->data['id_ruta'])) {
                $status_pretty = Driver::prettyStatus($res['status'], $this->data['status']);
                $params = array(
                    'status' => $this->data['status'],
                    'ip_address' => $_SERVER['REMOTE_ADDR'],
                );
                $DbExt = new DbExt;

                /* update the status */
                $DbExt->updateData("{{ruta}}", array(
                    'status' => $this->data['status']
                        ), 'id_ruta', $this->data['id_ruta']);
                $this->code = 1;
                $this->msg = Driver::t("Estado de Ruta cambiado exitosamente");
                $this->details = 'change-status-ruta-modal';
            } else
                $this->msg = Driver::t("Id de ruta no encontrada");
        } else
            $this->msg = $Validator->getErrorAsHTML();
        $this->jsonResponse();
    }

    public function actionchangeMigrarOrden() {
        $req = array(
            'orden_id' => Driver::t("ID de orden es obligatoria"),
            'id_ruta' => Driver::t("Ruta es obligatoria"),
        );
        $Validator = new Validator;
        $Validator->required($req, $this->data);
        if ($Validator->validate()) {
            if ($res = Driver::getOrdenId($this->data['orden_id'])) {

                $params = array(
                    'detalle' => "Ruta migrada a: " . $this->data['id_ruta'],
                    'estado' => $res['estado'],
                    'date_created' => AdminFunctions::dateNow(),
                    'ip_address' => $_SERVER['REMOTE_ADDR'],
                    'orden_id' => isset($this->data['orden_id']) ? $this->data['orden_id'] : '',
                    'usuario_id' => Driver::getUserId(),
                );
                $DbExt = new DbExt;
                if ($DbExt->insertData("{{historial_ordenes}}", $params)) {
                    $this->code = 1;
                    $this->msg = Driver::t("Orden migrada de ruta exitosamente");
                    $this->details = 'migrar-de-ruta-orden-modal';

                    /* update the status */
                    $DbExt->updateData("{{orden}}", array(
                        'id_ruta' => $this->data['id_ruta'],
                        'estado' => 'Recolectado'
                            ), 'orden_id', $this->data['orden_id']);
                } else
                    $this->msg = Driver::t("Error al actualizar registro");
            } else
                $this->msg = Driver::t("Orden no encontrada");
        } else
            $this->msg = $Validator->getErrorAsHTML();
        $this->jsonResponse();
    }

    public function actionchangeFechaEnvioOrden() {
        $req = array(
            'orden_id' => Driver::t("ID de orden es obligatoria"),
            'fecha_envio' => Driver::t("fecha envío es obligatoria"),
        );
        $Validator = new Validator;
        $Validator->required($req, $this->data);
        if ($Validator->validate()) {
            if ($res = Driver::getOrdenId($this->data['orden_id'])) {

                $params = array(
                    'detalle' => "Establece fecha envío: " . $this->data['fecha_envio'],
                    'estado' => $res['estado'],
                    'date_created' => AdminFunctions::dateNow(),
                    'ip_address' => $_SERVER['REMOTE_ADDR'],
                    'orden_id' => isset($this->data['orden_id']) ? $this->data['orden_id'] : '',
                    'usuario_id' => Driver::getUserId(),
                );
                $DbExt = new DbExt;
                if ($DbExt->insertData("{{historial_ordenes}}", $params)) {
                    $this->code = 1;
                    $this->msg = Driver::t("Fecha envío cambiada exitosamente");
                    $this->details = 'cambiar-fecha-envio-orden-modal';

                    /* update the status */
                    $DbExt->updateData("{{orden}}", array(
                        'fecha_envio' => $this->data['fecha_envio']
                            ), 'orden_id', $this->data['orden_id']);
                } else
                    $this->msg = Driver::t("Error al actualizar registro");
            } else
                $this->msg = Driver::t("Orden no encontrada");
        } else
            $this->msg = $Validator->getErrorAsHTML();
        $this->jsonResponse();
    }

    public function actionchangeFechaEntregaOrden() {
        $req = array(
            'orden_id' => Driver::t("ID de orden es obligatoria"),
            'fecha_entrega' => Driver::t("Fecha es obligatoria"),
        );
        $Validator = new Validator;
        $Validator->required($req, $this->data);
        if ($Validator->validate()) {
            if ($res = Driver::getOrdenId($this->data['orden_id'])) {

                $params = array(
                    'detalle' => "Establece fecha entrega: " . $this->data['fecha_entrega'],
                    'estado' => $res['estado'],
                    'date_created' => AdminFunctions::dateNow(),
                    'ip_address' => $_SERVER['REMOTE_ADDR'],
                    'orden_id' => isset($this->data['orden_id']) ? $this->data['orden_id'] : '',
                    'usuario_id' => Driver::getUserId(),
                );
                $DbExt = new DbExt;
                if ($DbExt->insertData("{{historial_ordenes}}", $params)) {
                    $this->code = 1;
                    $this->msg = Driver::t("fecha entrega cambiada exitosamente");
                    $this->details = 'cambiar-fecha-entrega-orden-modal';

                    /* update the status */
                    $DbExt->updateData("{{orden}}", array(
                        'fecha_entrega' => $this->data['fecha_entrega']
                            ), 'orden_id', $this->data['orden_id']);
                } else
                    $this->msg = Driver::t("Error al actualizar registro");
            } else
                $this->msg = Driver::t("Orden no encontrada");
        } else
            $this->msg = $Validator->getErrorAsHTML();
        $this->jsonResponse();
    }

    public function actionchangeStatusOrden() {
        $req = array(
            'orden_id' => Driver::t("ID de orden es obligatoria"),
            'status' => Driver::t("Status es obligatorio"),
        );
        $Validator = new Validator;
        $Validator->required($req, $this->data);
        if ($Validator->validate()) {
            if ($res = Driver::getOrdenId($this->data['orden_id'])) {
                $status_pretty = Driver::prettyStatus($res['estado'], $this->data['status']);
                $params = array(
                    'detalle' => $status_pretty . " " . $this->data['detalle'],
                    'estado' => $this->data['status'],
                    'date_created' => AdminFunctions::dateNow(),
                    'ip_address' => $_SERVER['REMOTE_ADDR'],
                    'orden_id' => isset($this->data['orden_id']) ? $this->data['orden_id'] : '',
                    'usuario_id' => Driver::getUserId(),
                );
                $DbExt = new DbExt;
                if ($DbExt->insertData("{{historial_ordenes}}", $params)) {
                    $this->code = 1;
                    $this->msg = Driver::t("Estado cambiado exitosamente");
                    $this->details = 'change-status-orden-modal';

                    /* update the status */
                    $DbExt->updateData("{{orden}}", array(
                        'estado' => $this->data['status']
                            ), 'orden_id', $this->data['orden_id']);

                    if ($params['estado'] == "No-Efectivo") {
                        $resEnvio = Driver::getClienteId($res['cliente_id']);
                        $params_envio = array(
                            'email' => $resEnvio['email'],
                            'nombres' => $resEnvio['nombre'] . " " . $resEnvio['apellido'],
                        );
                        AdminFunctions::sendNoEfectivoCliente($params_envio);
                    }
                } else
                    $this->msg = Driver::t("Error al actualizar registro");
            } else
                $this->msg = Driver::t("Orden no encontrada");
        } else
            $this->msg = $Validator->getErrorAsHTML();
        $this->jsonResponse();
    }

    public function actionactualizarMasivoOrdenes() {
        $connection = Yii::app()->db;

        $transaction = $connection->beginTransaction();
        try {

            //$arr = json_decode( $this->data);
            $params = array(
                'date_created' => AdminFunctions::dateNow(),
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'usuario_id' => Driver::getUserId(),
            );

            $params_orden = array(
                'date_modified' => AdminFunctions::dateNow(),
                'ip_address' => $_SERVER['REMOTE_ADDR'],
            );


            $detalle = "";
            foreach ($this->data as $name => $value) {
                if ($name == "fecha_envio" && isset($value) && $value != "") {
                    $detalle .= "Establece fecha envío: " . $value;
                    $params_orden['fecha_envio'] = $value;
                }
                if ($name == "fecha_entrega" && isset($value) && $value != "") {
                    $detalle .= "Establece fecha entrega: " . $value;
                    $params_orden['fecha_entrega'] = $value;
                }
                if ($name == "detalle" && isset($value) && $value != "") {
                    $detalle .= $value;
                }
                if ($name == "status" && isset($value) && $value != "") {
                    $params['estado'] = $value;
                    $params_orden['estado'] = $value;
                    $detalle .= $value;
                }
                $params['detalle'] = $detalle;
            }

            if ($detalle == "") {
                $transaction->rollBack();
                $this->msg = "No ha definido ni estado, ni fechas ni detalle a actualizar";
                $this->jsonResponse();
            }

            foreach ($this->data as $name => $value) {
                if (strpos($name, 'seleccionado_') !== false) {
                    if ($res = Driver::getOrdenId($value)) {
                        $params['orden_id'] = $value;

                        $command = $connection->createCommand();

                        if ($command->insert("{{historial_ordenes}}", $params)) {


                            $command = $connection->createCommand();
                            /* update the status */
                            $resupdate = $command->update("{{orden}}", $params_orden, "orden_id=:orden_id", array(":orden_id" => $value));

                            if (!$resupdate) {
                                throw new Exception('Orden no se puede actualizar id: ' . $res['orden_id']);
                            }
                            if ($params_orden['estado'] == "No-Efectivo") {
                                $resEnvio = Driver::getClienteId($res['cliente_id']);
                                $params_envio = array(
                                    'email' => $resEnvio['email'],
                                    'nombres' => $resEnvio['nombre'] . " " . $resEnvio['apellido'],
                                );
                                AdminFunctions::sendNoEfectivoCliente($params_envio);
                            }
                        } else {
                            throw new Exception('Historial no se puede actualizar id: ' . $res['orden_id']);
                        }
                    } else
                        $this->msg = Driver::t("Orden no encontrada");
                }
            }

            $this->code = 1;
            $this->msg = Driver::t("Operación exitosa");
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            $this->msg = $e->getMessage();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->msg = $e->getMessage();
        }
        $this->jsonResponse();
    }

    public function actionchangeAsignarOrden() {
        $req = array(
            'orden_id' => Driver::t("ID de orden es obligatoria"),
            'id_ruta' => Driver::t("Ruta es obligatoria"),
        );
        $Validator = new Validator;
        $Validator->required($req, $this->data);
        if ($Validator->validate()) {
            if ($res = Driver::getOrdenId($this->data['orden_id'])) {
                if ($res['id_ruta'] == null) {
                    $params = array(
                        'detalle' => "Asigna ruta: " . $this->data['id_ruta'],
                        'estado' => $res['estado'],
                        'date_created' => AdminFunctions::dateNow(),
                        'ip_address' => $_SERVER['REMOTE_ADDR'],
                        'orden_id' => isset($this->data['orden_id']) ? $this->data['orden_id'] : '',
                        'usuario_id' => Driver::getUserId(),
                    );
                    $DbExt = new DbExt;
                    if ($DbExt->insertData("{{historial_ordenes}}", $params)) {
                        $this->code = 1;
                        $this->msg = Driver::t("Ruta asignada exitosamente");
                        $this->details = 'asignar-ruta-orden-modal';

                        /* update the status */

                        $DbExt->updateData("{{orden}}", array(
                            'id_ruta' => $this->data['id_ruta'],
                            'estado' => 'En-Transito'
                                ), 'orden_id', $this->data['orden_id']);
                    } else
                        $this->msg = Driver::t("Error al actualizar registro");
                } else
                    $this->msg = Driver::t("Orden ya asignada a una ruta, utilice la opción de migrar ruta si es el caso");
            } else
                $this->msg = Driver::t("Orden no encontrada");
        } else
            $this->msg = $Validator->getErrorAsHTML();
        $this->jsonResponse();
    }

    public function actionclienteList() {
        $aColumns = array(
            'prefijo',
            'nombre',
            'apellido',
            'empresa',
            'email',
            'status'
        );
        $t = AjaxDataTables::AjaxData($aColumns);
        if (isset($_GET['debug'])) {
            dump($t);
        }

        if (is_array($t) && count($t) >= 1) {
            $sWhere = $t['sWhere'];
            $sOrder = $t['sOrder'];
            $sLimit = $t['sLimit'];
        }

        $and = '';


        $stmt = "SELECT SQL_CALC_FOUND_ROWS c.*
		FROM
		{{clientes}} c 
		WHERE 1		
		$and
		$sWhere
		$sOrder
		$sLimit
		";
        if (isset($_GET['debug'])) {
            dump($stmt);
        }

        $_SESSION['kartero_stmt_clientes'] = $stmt;

        $DbExt = new DbExt;
        if ($res = $DbExt->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $DbExt->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {
                $id = $val['id_cliente'];
                $p = "id=$id" . "&tbl=clientes&whereid=id_cliente";
                $status = "<span class=\"tag " . $val['status'] . " \">" . Driver::t($val['status']) . "</span>";
                $action = "<div>";
                $action1 = "<a class=\"btn btn-primary edit-cliente\"  data-id='" . $id . "' 
			    class=\"table-edit\" href=\"#no\">" . Driver::t("Editar") . "</a>";
                $action1 .= "&nbsp;|&nbsp;<br/>";
                $action1 .= "<a class=\"btn btn-success resetea-password-cliente\"  data-id='" . $id . "' 
			    class=\"table-edit\" href=\"#no\">" . Driver::t("Resetea Password") . "</a>";
                $action1 .= "&nbsp;|&nbsp;<br/>";
                $action1 .= "<a class=\"btn btn-danger delete\"  data-data='" . $p . "'   data-id='" . $id . "'  class=\"table-delete\" href=\"#no\">" . Driver::t("Eliminar") . "</a>";

                $action .= $action1;

                $action .= "</div>";

                $feed_data['aaData'][] = array(
                    $val['prefijo'],
                    $val['nombre'],
                    $val['apellido'],
                    $val['empresa'],
                    $val['email'],
                    $val['telefono'],
                    $status,
                    $action
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actionusuarioList() {
        $aColumns = array(
            'nombre',
            'apellido',
            'email',
            'status'
        );
        $t = AjaxDataTables::AjaxData($aColumns);
        if (isset($_GET['debug'])) {
            dump($t);
        }

        if (is_array($t) && count($t) >= 1) {
            $sWhere = $t['sWhere'];
            $sOrder = $t['sOrder'];
            $sLimit = $t['sLimit'];
        }

        $and = '';


        $stmt = "SELECT SQL_CALC_FOUND_ROWS u.*
		FROM
		{{usuario}} u
		WHERE 1		
		$and
		$sWhere
		$sOrder
		$sLimit
		";
        if (isset($_GET['debug'])) {
            dump($stmt);
        }

        $_SESSION['kartero_stmt_usuarios'] = $stmt;

        $DbExt = new DbExt;
        if ($res = $DbExt->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $DbExt->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {
                $id = $val['id'];
                $p = "id=$id" . "&tbl=usuario&whereid=id";
                $status = "<span class=\"tag " . $val['status'] . " \">" . Driver::t($val['status']) . "</span>";
                $action = "<div>";
                $action1 = "<a class=\"btn btn-primary edit-usuario\"  data-id='" . $id . "' 
			    class=\"table-edit\" href=\"#no\">" . Driver::t("Editar") . "</a>";
                $action1 .= "&nbsp;|&nbsp;<br/>";
                $action1 .= "<a class=\"btn btn-success resetea-password-usuario\"  data-id='" . $id . "' 
			    class=\"table-edit\" href=\"#no\">" . Driver::t("Resetea Password") . "</a>";
                $action1 .= "&nbsp;|&nbsp;<br/>";
                if ($val['email'] != "admin@web-cargoxpress.com")
                    $action1 .= "<a class=\"btn btn-danger delete\"  data-data='" . $p . "'   data-id='" . $id . "'  class=\"table-delete\" href=\"#no\">" . Driver::t("Eliminar") . "</a>";

                $action .= $action1;

                $action .= "</div>";

                $feed_data['aaData'][] = array(
                    $val['nombre'],
                    $val['apellido'],
                    $val['email'],
                    $val['telefono'],
                    $status,
                    $action
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actionaddCliente() {

        $DbExt = new DbExt;
        $params = array(
            'nombre' => isset($this->data['nombre']) ? $this->data['nombre'] : '',
            'apellido' => isset($this->data['apellido']) ? $this->data['apellido'] : '',
            'email' => isset($this->data['email']) ? $this->data['email'] : '',
            'telefono' => isset($this->data['telefono']) ? $this->data['telefono'] : '',
            'prefijo' => isset($this->data['prefijo']) ? $this->data['prefijo'] : '',
            'status' => isset($this->data['status']) ? $this->data['status'] : '',
            'empresa' => isset($this->data['empresa']) ? $this->data['empresa'] : '',
            'date_created' => AdminFunctions::dateNow(),
            'ip_address' => $_SERVER['REMOTE_ADDR']
        );


        if (!isset($this->data['id_cliente'])) {
            $this->data['id_cliente'] = '';
        }

        if (is_numeric($this->data['id_cliente'])) {
            unset($params['date_created']);
            $params['date_modified'] = AdminFunctions::dateNow();
            if ($DbExt->updateData("{{clientes}}", $params, 'id_cliente', $this->data['id_cliente'])) {
                $this->code = 1;
                $this->msg = Driver::t("Operación exitosa");
                $this->details = 'new-cliente';

                /* update team */
                //Driver::updateTeamDriver($this->data['id'],$params['team_id']);
            } else
                $this->msg = Driver::t("Error al actualizar");
        } else {
            if ($res = AdminFunctions::getClienteByEmail($this->data['email'])) {
                $this->code = 0;
                $this->msg = Driver::t("El email ya existe en el Sistema, usuario no creado");
                $this->jsonResponse();
            }

            $token = md5(AdminFunctions::generateCode(10));
            $verification_code = AdminFunctions::generateNumericCode(5);
            $params['token'] = $token;
            $params['verification_code'] = $verification_code;
            $password = AdminFunctions::generateNumericCode(5);
            $encryption_type = Yii::app()->params->encryption_type;
            if (empty($encryption_type)) {
                $encryption_type = 'yii';
            }

            if ($encryption_type == "yii") {
                $params['password'] = CPasswordHelper::hashPassword($password);
            } else
                $params['password'] = md5($password);

            if ($DbExt->insertData('{{clientes}}', $params)) {
                $params['password'] = $password;
                if (AdminFunctions::sendRegistroCliente($params)) {
                    $this->code = 1;
                    $this->msg = Driver::t("Operación exitosa");
                } else
                    $this->msg = t("Cliente creado, No se pudo enviar el mail de registro");
            } else
                $this->msg = Driver::t("Error al insertar");
        }
        $this->jsonResponse();
    }

    public function actionaddRuta() {
        $req = array(
            'id_mensajero' => Driver::t("ID de mensajero es obligatorio"),
            'status' => Driver::t("Status es obligatorio"),
        );
        $Validator = new Validator;
        $Validator->required($req, $this->data);
        if ($Validator->validate()) {

            $DbExt = new DbExt;
            $params = array(
                'fecha_ruta' => isset($this->data['fecha_ruta']) ? $this->data['fecha_ruta'] : '',
                'id_mensajero' => isset($this->data['id_mensajero']) ? $this->data['id_mensajero'] : '',
                'detalle' => isset($this->data['detalle']) ? $this->data['detalle'] : '',
                'status' => isset($this->data['status']) ? $this->data['status'] : '',
                'date_created' => AdminFunctions::dateNow(),
            );
            if (!isset($this->data['id_ruta'])) {
                $this->data['id_ruta'] = '';
            }

            if (is_numeric($this->data['id_ruta'])) {
                unset($params['date_created']);
                $params['date_modified'] = AdminFunctions::dateNow();
                if ($DbExt->updateData("{{ruta}}", $params, 'id_ruta', $this->data['id_ruta'])) {
                    $this->code = 1;
                    $this->msg = Driver::t("Operación exitosa");
                    $this->details = 'new-ruta';
                } else
                    $this->msg = Driver::t("Error al actualizar");
            } else {

                if ($DbExt->insertData('{{ruta}}', $params)) {

                    $this->code = 1;
                    $this->msg = Driver::t("Operación exitosa");
                } else
                    $this->msg = Driver::t("Error al insertar");
            }
        } else
            $this->msg = $Validator->getErrorAsHTML();
        $this->jsonResponse();
    }

    public function actiongetEditCliente() {

        if (isset($this->data['id_cliente'])) {
            if ($res = Driver::clienteInfo($this->data['id_cliente'])) {


                $this->code = 1;
                $this->msg = Driver::t("Operación exitosa");
                $this->details = $res;
            } else
                $this->msg = Driver::t("Registro no encontrado");
        } else
            $this->msg = Driver::t("Faltan parámetros");
        $this->jsonResponse();
    }

    public function actionreseteaPasswordCliente() {
        if ($res = AdminFunctions::getClienteById($this->data['id_cliente'])) {
            $DbExt = new DbExt;
            $password = AdminFunctions::generateNumericCode(5);
            $res['password'] = $password;
            $encryption_type = Yii::app()->params->encryption_type;
            if (empty($encryption_type)) {
                $encryption_type = 'yii';
            }

            if ($encryption_type == "yii") {
                $params['password'] = CPasswordHelper::hashPassword($password);
            } else
                $params['password'] = md5($password);
            $params['date_modified'] = AdminFunctions::dateNow();
            if ($DbExt->updateData("{{clientes}}", $params, 'id_cliente', $this->data['id_cliente'])) {
                $res['password'] = $password;
                if (AdminFunctions::sendResetPasswordCliente($res)) {
                    $this->code = 1;
                    $this->msg = "Tu password ha sido cambiado correctamente, por favor revisa tu correo en la bandeja de entrada o en la bandeja de spam";
                } else
                    $this->msg = t("No se pudo procesar su requerimiento");
            } else
                $this->msg = Driver::t("Error al actualizar");
        } else
            $this->msg = t("La dirección de mail no existe en nuestros registros");
        $this->jsonResponse();
    }

    public function actionaddUsuario() {

        $DbExt = new DbExt;
        $params = array(
            'nombre' => isset($this->data['nombre']) ? $this->data['nombre'] : '',
            'apellido' => isset($this->data['apellido']) ? $this->data['apellido'] : '',
            'email' => isset($this->data['email']) ? $this->data['email'] : '',
            'telefono' => isset($this->data['telefono']) ? $this->data['telefono'] : '',
            'status' => isset($this->data['status']) ? $this->data['status'] : '',
            'date_created' => AdminFunctions::dateNow(),
            'ip_address' => $_SERVER['REMOTE_ADDR']
        );


        if (!isset($this->data['id'])) {
            $this->data['id'] = '';
        }

        if (is_numeric($this->data['id'])) {
            unset($params['date_created']);
            $params['date_modified'] = AdminFunctions::dateNow();
            if ($DbExt->updateData("{{usuario}}", $params, 'id', $this->data['id'])) {
                $this->code = 1;
                $this->msg = Driver::t("Operación exitosa");
                $this->details = 'new-usuario';
            } else
                $this->msg = Driver::t("Error al actualizar");
        } else {
            if ($res = AdminFunctions::getUsuarioByEmail($this->data['email'])) {
                $this->code = 0;
                $this->msg = Driver::t("El email ya existe en el Sistema, usuario no creado");
                $this->jsonResponse();
            }

            $token = md5(AdminFunctions::generateCode(10));
            $verification_code = AdminFunctions::generateNumericCode(5);
            $params['token'] = $token;
            $params['verification_code'] = $verification_code;
            $password = AdminFunctions::generateNumericCode(5);
            $encryption_type = Yii::app()->params->encryption_type;
            if (empty($encryption_type)) {
                $encryption_type = 'yii';
            }

            if ($encryption_type == "yii") {
                $params['password'] = CPasswordHelper::hashPassword($password);
            } else
                $params['password'] = md5($password);

            if ($DbExt->insertData('{{usuario}}', $params)) {
                $params['password'] = $password;
                if (AdminFunctions::sendRegistroUsuario($params)) {
                    $this->code = 1;
                    $this->msg = Driver::t("Operación exitosa");
                } else
                    $this->msg = t("Usuario creado, No se pudo enviar el mail de registro");
            } else
                $this->msg = Driver::t("Error al insertar");
        }
        $this->jsonResponse();
    }

    public function actiongetEditUsuario() {

        if (isset($this->data['id'])) {
            if ($res = Driver::usuarioInfo($this->data['id'])) {
                $this->code = 1;
                $this->msg = Driver::t("Operación exitosa");
                $this->details = $res;
            } else
                $this->msg = Driver::t("Registro no encontrado");
        } else
            $this->msg = Driver::t("Faltan parámetros");
        $this->jsonResponse();
    }

    public function actionreseteaPasswordUsuario() {
        if ($res = AdminFunctions::getUsuarioById($this->data['id'])) {
            $DbExt = new DbExt;
            $password = AdminFunctions::generateNumericCode(5);
            $res['password'] = $password;
            $encryption_type = Yii::app()->params->encryption_type;
            if (empty($encryption_type)) {
                $encryption_type = 'yii';
            }

            if ($encryption_type == "yii") {
                $params['password'] = CPasswordHelper::hashPassword($password);
            } else
                $params['password'] = md5($password);
            $params['date_modified'] = AdminFunctions::dateNow();
            if ($DbExt->updateData("{{usuario}}", $params, 'id', $this->data['id'])) {
                $res['password'] = $password;
                if (AdminFunctions::sendResetPasswordUsuario($res)) {
                    $this->code = 1;
                    $this->msg = "Tu password ha sido cambiado correctamente, por favor revisa tu correo en la bandeja de entrada o en la bandeja de spam";
                } else
                    $this->msg = t("No se pudo procesar su requerimiento");
            } else
                $this->msg = Driver::t("Error al actualizar");
        } else
            $this->msg = t("La dirección de mail no existe en nuestros registros");
        $this->jsonResponse();
    }

    public function actionzonasList() {
        $aColumns = array(
            'zona',
            'l.nombre'
        );
        $t = AjaxDataTables::AjaxData($aColumns);
        if (isset($_GET['debug'])) {
            dump($t);
        }

        if (is_array($t) && count($t) >= 1) {
            $sWhere = $t['sWhere'];
            $sOrder = $t['sOrder'];
            $sLimit = $t['sLimit'];
        }

        $and = '';
        $sOrder = ' ORDER BY z.zona ASC, z.zona_padre ASC ';

        $stmt = "SELECT SQL_CALC_FOUND_ROWS z.*,l.nombre AS ciudad 
		FROM
		{{zonas}} z
                INNER JOIN {{locacion}} l on z.id_locacion=l.id
		WHERE 1		
		$and
		$sWhere
		$sOrder
		$sLimit
		";
        if (isset($_GET['debug'])) {
            dump($stmt);
        }

        $_SESSION['kartero_stmt_zonas'] = $stmt;

        $DbExt = new DbExt;
        if ($res = $DbExt->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $DbExt->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {


                $id = $val['id'];
                $p = "id=$id" . "&tbl=zonas&whereid=id";
                $action = "<div>";
                $action1 = "<a class=\"btn btn-primary edit-zona\"  data-id_zona=\"$id\" 
			    class=\"table-edit\" href=\"#no\">" . Driver::t("Editar") . "</a>";
                $action1 .= "&nbsp;|&nbsp;";
                $action1 .= "<a class=\"btn btn-danger delete\"  data-data=\"$p\"  data-id=\"$id\" class=\"table-delete\" href=\"#no\">" . Driver::t("Eliminar") . "</a>";

                $action .= $action1;

                $action .= "</div>";
                $feed_data['aaData'][] = array(
                    $val['zona'],
                    $val['zona_padre'],
                    $val['ciudad'],
                    $action
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actionaddZona() {

        $DbExt = new DbExt;
        $params = array(
            'zona' => isset($this->data['zona']) ? $this->data['zona'] : '',
            'zona_padre' => isset($this->data['zona_padre']) ? $this->data['zona_padre'] : '',
            'id_locacion' => isset($this->data['id_locacion']) ? $this->data['id_locacion'] : ''
        );


        if (!isset($this->data['id_zona'])) {
            $this->data['id_zona'] = '';
        }

        if (is_numeric($this->data['id_zona'])) {
            if ($DbExt->updateData("{{zonas}}", $params, 'id', $this->data['id_zona'])) {
                $this->code = 1;
                $this->msg = Driver::t("Operación exitosa");
                $this->details = 'new-zona';
            } else
                $this->msg = Driver::t("Error al actualizar");
        } else {
            if ($DbExt->insertData('{{zonas}}', $params)) {
                $this->code = 1;
                $this->msg = Driver::t("Operación exitosa");
            } else
                $this->msg = Driver::t("Error al insertar");
        }
        $this->jsonResponse();
    }

    public function actiongetEditZona() {

        if (isset($this->data['id_zona'])) {
            if ($res = Driver::zonaInfo($this->data['id_zona'])) {
                $this->code = 1;
                $this->msg = Driver::t("Operación exitosa");
                $this->details = $res;
            } else
                $this->msg = Driver::t("Registro no encontrado");
        } else
            $this->msg = Driver::t("Faltan parámetros");
        $this->jsonResponse();
    }

    public function actionlocacionList() {
        $aColumns = array(
            'nombre'
        );
        $t = AjaxDataTables::AjaxData($aColumns);
        if (isset($_GET['debug'])) {
            dump($t);
        }

        if (is_array($t) && count($t) >= 1) {
            $sWhere = $t['sWhere'];
            $sOrder = $t['sOrder'];
            $sLimit = $t['sLimit'];
        }

        $and = '';
        $sOrder = ' ORDER BY l.nombre ASC ';

        $stmt = "SELECT SQL_CALC_FOUND_ROWS l.*, l2.nombre as 'provincia'
		FROM
		{{locacion}} l 
                LEFT OUTER JOIN {{locacion}} l2 on l.id_padre=l2.id
		WHERE 1		
		$and
		$sWhere
		$sOrder
		$sLimit
		";
        if (isset($_GET['debug'])) {
            dump($stmt);
        }

        $_SESSION['kartero_stmt_locacion'] = $stmt;

        $DbExt = new DbExt;
        if ($res = $DbExt->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $DbExt->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {


                $id = $val['id'];
                $p = "id=$id" . "&tbl=locacion&whereid=id";
                $action = "<div>";
                $action1 = "<a class=\"btn btn-primary edit-locacion\"  data-id_locacion=\"$id\" 
			    class=\"table-edit\" href=\"#no\">" . Driver::t("Editar") . "</a>";
                $action1 .= "&nbsp;|&nbsp;";
                $action1 .= "<a class=\"btn btn-danger delete\"  data-data=\"$p\"  data-id=\"$id\" class=\"table-delete\" href=\"#no\">" . Driver::t("Eliminar") . "</a>";

                $action .= $action1;

                $action .= "</div>";
                $feed_data['aaData'][] = array(
                    $val['nombre'],
                    $val['provincia'],
                    $action
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actionaddLocacion() {

        $DbExt = new DbExt;

        if ($this->data['id_padre'] == '0') {
            $this->data['id_padre'] = null;
        }


        $params = array(
            'nombre' => isset($this->data['nombre']) ? $this->data['nombre'] : '',
            'id_padre' => isset($this->data['id_padre']) ? $this->data['id_padre'] : null
        );


        if (!isset($this->data['id_locacion'])) {
            $this->data['id_locacion'] = '';
        }

        if (is_numeric($this->data['id_locacion'])) {
            if ($DbExt->updateData("{{locacion}}", $params, 'id', $this->data['id_locacion'])) {
                $this->code = 1;
                $this->msg = Driver::t("Operación exitosa");
                $this->details = 'new-locacion';
            } else
                $this->msg = Driver::t("Error al actualizar");
        } else {
            if ($DbExt->insertData('{{locacion}}', $params)) {
                $this->code = 1;
                $this->msg = Driver::t("Operación exitosa");
            } else
                $this->msg = Driver::t("Error al insertar");
        }
        $this->jsonResponse();
    }

    public function actiongetEditLocacion() {

        if (isset($this->data['id_locacion'])) {
            if ($res = Driver::locacionInfo($this->data['id_locacion'])) {
                $res['id_padre'] = $res['id_padre'] != null ? $res['id_padre'] : "0";
                $this->code = 1;
                $this->msg = Driver::t("Operación exitosa");
                $this->details = $res;
            } else
                $this->msg = Driver::t("Registro no encontrado");
        } else
            $this->msg = Driver::t("Faltan parámetros");
        $this->jsonResponse();
    }

    public function actionchangeStatus() {
        $req = array(
            'task_id' => Driver::t("Task ID is required"),
            'status' => Driver::t("Status is required"),
        );
        $Validator = new Validator;
        $Validator->required($req, $this->data);
        if ($Validator->validate()) {
            if ($res = Driver::getTaskId($this->data['task_id'])) {
                $status_pretty = Driver::prettyStatus($res['status'], $this->data['status']);
                $params = array(
                    'remarks' => $status_pretty,
                    'status' => $this->data['status'],
                    'date_created' => AdminFunctions::dateNow(),
                    'ip_address' => $_SERVER['REMOTE_ADDR'],
                    'task_id' => $this->data['task_id'],
                    'reason' => isset($this->data['reason']) ? $this->data['reason'] : ''
                );
                $DbExt = new DbExt;
                if ($DbExt->insertData("{{task_history}}", $params)) {
                    $this->code = 1;
                    $this->msg = Driver::t("Task Status Changed Successfully");
                    $this->details = 'task-change-status-modal';

                    /* update the status */
                    $DbExt->updateData("{{driver_task}}", array(
                        'status' => $this->data['status']
                            ), 'task_id', $this->data['task_id']);
                } else
                    $this->msg = Driver::t("failed cannot update record");
            } else
                $this->msg = Driver::t("Task id not found");
        } else
            $this->msg = $Validator->getErrorAsHTML();
        $this->jsonResponse();
    }

    public function actiongetDashboard() {
        $stmt = "SELECT count(o.orden_id) as 'TOTAL_ORDENES' FROM {{orden}} o ";
        $stmt2 = "SELECT count(o.orden_id) as 'TOTAL_ORDENES_CREADAS' FROM {{orden}} o WHERE o.estado='Creado'";
        $stmt3 = "SELECT count(o.orden_id) as 'TOTAL_ORDENES_COMPLETADAS' FROM {{orden}} o where  o.estado='Completado'";
        $stmt4 = "SELECT count(o.orden_id) as 'TOTAL_ORDENES_EN_RUTA' FROM {{orden}} o where o.estado='En-Transito'";

        //dump($stmt);
        $DbExt = new DbExt;
        $TOTAL_ORDENES = $DbExt->rst($stmt);
        $TOTAL_ORDENES_CREADAS = $DbExt->rst($stmt2);
        $TOTAL_ORDENES_COMPLETADAS = $DbExt->rst($stmt3);
        $TOTAL_ORDENES_EN_RUTA = $DbExt->rst($stmt4);

        $dashboard = array(
            'TOTAL_ORDENES' => isset($TOTAL_ORDENES[0]['TOTAL_ORDENES']) ? $TOTAL_ORDENES[0]['TOTAL_ORDENES'] : '',
            'TOTAL_ORDENES_CREADAS' => isset($TOTAL_ORDENES_CREADAS[0]['TOTAL_ORDENES_CREADAS']) ? $TOTAL_ORDENES_CREADAS[0]['TOTAL_ORDENES_CREADAS'] : '',
            'TOTAL_ORDENES_COMPLETADAS' => isset($TOTAL_ORDENES_COMPLETADAS[0]['TOTAL_ORDENES_COMPLETADAS']) ? $TOTAL_ORDENES_COMPLETADAS[0]['TOTAL_ORDENES_COMPLETADAS'] : '',
            'TOTAL_ORDENES_EN_RUTA' => isset($TOTAL_ORDENES_EN_RUTA[0]['TOTAL_ORDENES_EN_RUTA']) ? $TOTAL_ORDENES_EN_RUTA[0]['TOTAL_ORDENES_EN_RUTA'] : '',
        );
        $this->code = 1;
        $this->msg = "OK";
        $this->details = $dashboard;
        $this->jsonResponse();
    }

    public function actionloadAgentDashboard() {

        $data = array();
        $agent_stats = array(
            'active', 'offline', 'total'
        );
        foreach ($agent_stats as $agent_stat) {
            $res = Driver::getDriverByStats(
                            Driver::getUserId(), $agent_stat, isset($this->data['date']) ? $this->data['date'] : date("Y-m-d"), 'active', isset($this->data['team_id']) ? $this->data['team_id'] : ''
            );
            if ($res) {
                $data[$agent_stat] = $res;
            } else
                $data[$agent_stat] = '';
        }

        //dump($data);

        $this->code = 1;
        $this->msg = "OK";
        $this->details = $data;
        $this->jsonResponse();
    }

    public function actiongetDriverDetails() {
        if (isset($this->data['driver_id'])) {
            if ($res = Driver::driverInfo($this->data['driver_id'])) {
                $data['driver_id'] = $res['driver_id'];
                //$data['user_id']=$res['customer_id'];
                $data['name'] = $res['first_name'] . " " . $res['last_name'];
                $data['email'] = $res['email'];
                $data['phone'] = $res['phone'];
                $data['transport_type_id'] = $res['transport_type_id'];
                $data['licence_plate'] = $res['licence_plate'];
                $data['team_name'] = $res['team_name'];
                $data['transport_type_id'] = Driver::t($data['transport_type_id']);

                $data['device_platform'] = $res['device_platform'];
                $data['app_version'] = $res['app_version'];

                $order_details = '';

                $transaction_date = isset($this->data['date']) ? $this->data['date'] : date("Y-m-d");
                if (!$order = Driver::getTaskByDriverID($this->data['driver_id'], $transaction_date)) {
                    $order_details = '';
                } else {
                    foreach ($order as $order_val) {
                        $order_val['status'] = Driver::t($order_val['status']);
                        $order_val['status_raw'] = $order_val['status'];
                        $order_details[] = $order_val;
                    }
                }

                if ($data['device_platform'] == "null") {
                    $data['device_platform'] = '';
                }
                //dump($data);

                $this->code = 1;
                $this->msg = "OK";
                $this->details = array(
                    'info' => $data,
                    'task' => $order_details
                );
            } else
                $this->msg = Driver::t("Driver details not found");
        } else
            $this->msg = Driver::t("Missing parameters");
        $this->jsonResponse();
    }

    public function actiongeneralSettings() {

        Yii::app()->functions->updateOption('drv_default_location', isset($this->data['drv_default_location']) ? $this->data['drv_default_location'] : '', Driver::getUserId()
        );

        Yii::app()->functions->updateOption('drv_map_style', isset($this->data['drv_map_style']) ? $this->data['drv_map_style'] : '', Driver::getUserId()
        );

        Yii::app()->functions->updateOption('drv_delivery_time', isset($this->data['drv_delivery_time']) ? $this->data['drv_delivery_time'] : '', Driver::getUserId()
        );

        if (!empty($this->data['drv_default_location'])) {
            $country_list = require_once('CountryCode.php');
            $country_name = '';
            if (array_key_exists($this->data['drv_default_location'], (array) $country_list)) {
                $country_name = $country_list[$this->data['drv_default_location']];
            } else
                $country_name = $this->data['drv_default_location'];
            if ($res = Driver::addressToLatLong($country_name)) {
                Yii::app()->functions->updateOption("drv_default_location_lat", $res['lat'], Driver::getUserId());
                Yii::app()->functions->updateOption("drv_default_location_lng", $res['long'], Driver::getUserId());
            }
        }

        Yii::app()->functions->updateOption('driver_send_push_to_online', isset($this->data['driver_send_push_to_online']) ? $this->data['driver_send_push_to_online'] : '', Driver::getUserId()
        );

        Yii::app()->functions->updateOption('driver_include_offline_driver_map', isset($this->data['driver_include_offline_driver_map']) ? $this->data['driver_include_offline_driver_map'] : '', Driver::getUserId()
        );

        Yii::app()->functions->updateOption('driver_disabled_auto_refresh', isset($this->data['driver_disabled_auto_refresh']) ? $this->data['driver_disabled_auto_refresh'] : '', Driver::getUserId()
        );

        Yii::app()->functions->updateOption('driver_disabled_contacts_task', isset($this->data['driver_disabled_contacts_task']) ? $this->data['driver_disabled_contacts_task'] : '', Driver::getUserId()
        );

        Yii::app()->functions->updateOption('driver_enabled_notes', isset($this->data['driver_enabled_notes']) ? $this->data['driver_enabled_notes'] : '', Driver::getUserId()
        );

        Yii::app()->functions->updateOption('driver_enabled_signature', isset($this->data['driver_enabled_signature']) ? $this->data['driver_enabled_signature'] : '', Driver::getUserId()
        );

        Yii::app()->functions->updateOption('driver_enabled_photo', isset($this->data['driver_enabled_photo']) ? $this->data['driver_enabled_photo'] : '', Driver::getUserId()
        );

        Yii::app()->functions->updateOption('driver_device_vibration', isset($this->data['driver_device_vibration']) ? $this->data['driver_device_vibration'] : '', Driver::getUserId()
        );

        Yii::app()->functions->updateOption('driver_company_logo', isset($this->data['driver_company_logo']) ? $this->data['driver_company_logo'] : '', Driver::getUserId()
        );

        Yii::app()->functions->updateOption('calendar_language', isset($this->data['calendar_language']) ? $this->data['calendar_language'] : '', Driver::getUserId()
        );

        Yii::app()->functions->updateOption('driver_tracking_options', isset($this->data['driver_tracking_options']) ? $this->data['driver_tracking_options'] : '', Driver::getUserId()
        );

        Yii::app()->functions->updateOption('enabled_critical_task', isset($this->data['enabled_critical_task']) ? $this->data['enabled_critical_task'] : '', Driver::getUserId()
        );

        Yii::app()->functions->updateOption('critical_minutes', isset($this->data['critical_minutes']) ? $this->data['critical_minutes'] : '', Driver::getUserId()
        );

        Yii::app()->functions->updateOption('agents_record_track_Location', isset($this->data['agents_record_track_Location']) ? $this->data['agents_record_track_Location'] : '', Driver::getUserId()
        );

        Yii::app()->functions->updateOption('map_hide_pickup', isset($this->data['map_hide_pickup']) ? $this->data['map_hide_pickup'] : '', Driver::getUserId()
        );

        Yii::app()->functions->updateOption('map_hide_delivery', isset($this->data['map_hide_delivery']) ? $this->data['map_hide_delivery'] : '', Driver::getUserId()
        );

        Yii::app()->functions->updateOption('map_hide_success_task', isset($this->data['map_hide_success_task']) ? $this->data['map_hide_success_task'] : '', Driver::getUserId()
        );

        Yii::app()->functions->updateOption('customer_timezone', isset($this->data['customer_timezone']) ? $this->data['customer_timezone'] : '', Driver::getUserId()
        );

        Yii::app()->functions->updateOption('app_enabled_resize_pic', isset($this->data['app_enabled_resize_pic']) ? $this->data['app_enabled_resize_pic'] : '', Driver::getUserId()
        );

        Yii::app()->functions->updateOption('app_resize_width', isset($this->data['app_resize_width']) ? $this->data['app_resize_width'] : '', Driver::getUserId()
        );

        Yii::app()->functions->updateOption('app_resize_height', isset($this->data['app_resize_height']) ? $this->data['app_resize_height'] : '', Driver::getUserId()
        );

        Yii::app()->functions->updateOption('app_disabled_bg_tracking', isset($this->data['app_disabled_bg_tracking']) ? $this->data['app_disabled_bg_tracking'] : '', Driver::getUserId()
        );

        Yii::app()->functions->updateOption('app_track_interval', isset($this->data['app_track_interval']) ? $this->data['app_track_interval'] : '', Driver::getUserId()
        );

        Yii::app()->functions->updateOption('driver_auto_geo_address', isset($this->data['driver_auto_geo_address']) ? $this->data['driver_auto_geo_address'] : '', Driver::getUserId()
        );

        Yii::app()->functions->updateOption('driver_activity_tracking', isset($this->data['driver_activity_tracking']) ? $this->data['driver_activity_tracking'] : '', Driver::getUserId()
        );

        Yii::app()->functions->updateOption('driver_activity_tracking_interval', isset($this->data['driver_activity_tracking_interval']) ? $this->data['driver_activity_tracking_interval'] : '', Driver::getUserId()
        );

        $this->code = 1;
        $this->msg = Yii::t("default", "Setting saved");
        $this->jsonResponse();
    }

    public function actionSaveTranslation() {
        $mobile_dictionary = '';
        if (is_array($this->data) && count($this->data) >= 1) {
            //$version=str_replace(".",'',phpversion());					
            $mobile_dictionary = json_encode($this->data);
            $unicode = 3;
        }
        Yii::app()->functions->updateOptionAdmin('driver_mobile_dictionary', $mobile_dictionary);
        $this->code = 1;
        $this->msg = Driver::t("translation saved");
        $this->details = $unicode;
        $this->jsonResponse();
    }

    public function actionSaveNotification() {

        $driver_id = Driver::getUserId();

        $delivery = Driver::notificationListDelivery();
        $key = "DELIVERY_";
        foreach ($delivery['DELIVERY'] as $val) {
            foreach ($val as $val2) {
                $_key = $key . $val2;
                updateOption(
                        $_key, isset($this->data[$_key]) ? $this->data[$_key] : '', Driver::getUserId()
                );
            }
        }

        $delivery = Driver::notificationListPickup();
        $key = "PICKUP_";
        foreach ($delivery['PICKUP'] as $val) {
            foreach ($val as $val2) {
                $_key = $key . $val2;
                updateOption(
                        $_key, isset($this->data[$_key]) ? $this->data[$_key] : '', Driver::getUserId()
                );
            }
        }

        updateOption("ASSIGN_TASK_PUSH", isset($this->data['ASSIGN_TASK_PUSH']) ? $this->data['ASSIGN_TASK_PUSH'] : '', $driver_id);

        updateOption("ASSIGN_TASK_SMS", isset($this->data['ASSIGN_TASK_SMS']) ? $this->data['ASSIGN_TASK_SMS'] : '', $driver_id);

        updateOption("ASSIGN_TASK_EMAIL", isset($this->data['ASSIGN_TASK_EMAIL']) ? $this->data['ASSIGN_TASK_EMAIL'] : '', $driver_id);

        updateOption("CANCEL_TASK_PUSH", isset($this->data['CANCEL_TASK_PUSH']) ? $this->data['CANCEL_TASK_PUSH'] : '', $driver_id);

        updateOption("CANCEL_TASK_SMS", isset($this->data['CANCEL_TASK_SMS']) ? $this->data['CANCEL_TASK_SMS'] : '', $driver_id);

        updateOption("CANCEL_TASK_EMAIL", isset($this->data['CANCEL_TASK_EMAIL']) ? $this->data['CANCEL_TASK_EMAIL'] : '', $driver_id);

        updateOption("UPDATE_TASK_PUSH", isset($this->data['UPDATE_TASK_PUSH']) ? $this->data['UPDATE_TASK_PUSH'] : '', $driver_id);

        updateOption("UPDATE_TASK_SMS", isset($this->data['UPDATE_TASK_SMS']) ? $this->data['UPDATE_TASK_SMS'] : '', $driver_id);

        updateOption("UPDATE_TASK_EMAIL", isset($this->data['UPDATE_TASK_EMAIL']) ? $this->data['UPDATE_TASK_EMAIL'] : '', $driver_id);

        updateOption("FAILED_AUTO_ASSIGN_PUSH", isset($this->data['FAILED_AUTO_ASSIGN_PUSH']) ? $this->data['FAILED_AUTO_ASSIGN_PUSH'] : '', $driver_id);

        updateOption("FAILED_AUTO_ASSIGN_SMS", isset($this->data['FAILED_AUTO_ASSIGN_SMS']) ? $this->data['FAILED_AUTO_ASSIGN_SMS'] : '', $driver_id);

        updateOption("FAILED_AUTO_ASSIGN_EMAIL", isset($this->data['FAILED_AUTO_ASSIGN_EMAIL']) ? $this->data['FAILED_AUTO_ASSIGN_EMAIL'] : '', $driver_id);

        updateOption("AUTO_ASSIGN_ACCEPTED_PUSH", isset($this->data['AUTO_ASSIGN_ACCEPTED_PUSH']) ? $this->data['AUTO_ASSIGN_ACCEPTED_PUSH'] : '', $driver_id);

        updateOption("AUTO_ASSIGN_ACCEPTED_SMS", isset($this->data['AUTO_ASSIGN_ACCEPTED_SMS']) ? $this->data['AUTO_ASSIGN_ACCEPTED_SMS'] : '', $driver_id);

        updateOption("AUTO_ASSIGN_ACCEPTED_EMAIL", isset($this->data['AUTO_ASSIGN_ACCEPTED_EMAIL']) ? $this->data['AUTO_ASSIGN_ACCEPTED_EMAIL'] : '', $driver_id);

        $this->code = 1;
        $this->msg = Driver::t("Setting saved");
        $this->jsonResponse();
    }

    public function actionSaveNotificationTemplate() {
        //dump($this->data);
        $key = array('PUSH', 'SMS', 'EMAIL');

        $user_type = Driver::getLoginType();
        if ($user_type == "admin") {

            foreach ($key as $val) {
                $key = $this->data['option_name'] . "_$val" . "_TPL";
                Yii::app()->functions->updateOptionAdmin($key, isset($this->data[$val]) ? $this->data[$val] : ''
                );
            }
        } else {

            $merchant_id = Driver::getUserId();
            foreach ($key as $val) {
                $key = $this->data['option_name'] . "_$val" . "_TPL";
                Yii::app()->functions->updateOption($key, isset($this->data[$val]) ? $this->data[$val] : '', $merchant_id
                );
            }
        }
        $this->code = 1;
        $this->msg = Driver::t("Template saved");
        $this->jsonResponse();
    }

    public function actionGetNotificationTPL() {
        $key = array('PUSH', 'SMS', 'EMAIL');
        $user_type = Driver::getLoginType();
        if ($user_type == "admin") {

            $data = array();
            foreach ($key as $val) {
                $key = $this->data['option_name'] . "_$val" . "_TPL";
                $data[$val] = getOptionA($key);
            }
        } else {

            $merchant_id = Driver::getUserId();
            foreach ($key as $val) {
                $key = $this->data['option_name'] . "_$val" . "_TPL";
                $data[$val] = getOption($merchant_id, $key);
            }
        }
        $this->details = $data;
        $this->code = 1;
        $this->msg = Driver::t("OK");
        $this->jsonResponse();
    }

    public function actionGetNotifications() {
        $data = array();
        $db_ext = new DbExt;
        if ($res = Driver::getNotifications(Driver::getUserId())) {
            foreach ($res as $val) {
                $data[] = array(
                    'title' => Driver::t($val['status']) . " " . Driver::t("Task ID") . ":" . $val['task_id'],
                    'message' => $val['remarks'],
                    'task_id' => $val['task_id'],
                    'status' => Driver::t($val['status'])
                );
                $db_ext->updateData('{{task_history}}', array(
                    'notification_viewed' => 1
                        ), 'id', $val['id']);
            }
            $this->code = 1;
            $this->details = $data;
        } else
            $this->msg = "No notifications";
        $this->jsonResponse();
    }

    public function actiongetInitialNotifications() {
        $data = array();
        $db_ext = new DbExt;
        if ($res = Driver::getNotifications(Driver::getUserId(), 1)) {
            foreach ($res as $val) {
                $data[] = array(
                    'title' => $val['status'] . " " . Driver::t("Task ID") . ":" . $val['task_id'],
                    'message' => $val['remarks'],
                    'task_id' => $val['task_id'],
                    'status' => Driver::t($val['status'])
                );
                $db_ext->updateData('{{task_history}}', array(
                    'notification_viewed' => 1
                        ), 'id', $val['id']);
            }
            $this->code = 1;
            $this->details = $data;
        } else
            $this->msg = "No notifications";
        $this->jsonResponse();
    }

    public function actionPushLogList() {
        $aColumns = array(
            'push_id',
            'driver_id',
            'push_title',
            'push_message',
            'push_type',
            'device_platform',
            'status'
        );
        $t = AjaxDataTables::AjaxData($aColumns);
        if (isset($_GET['debug'])) {
            dump($t);
        }

        if (is_array($t) && count($t) >= 1) {
            $sWhere = $t['sWhere'];
            $sOrder = $t['sOrder'];
            $sLimit = $t['sLimit'];
        }

        $and = " AND customer_id = " . Driver::q(Driver::getUserId()) . " ";

        if (isset($_GET['broadcast_id'])) {
            if ($_GET['broadcast_id'] > 0) {
                $and .= "  AND broadcast_id=" . Driver::q($_GET['broadcast_id']) . " ";
            }
        }


        $stmt = "SELECT SQL_CALC_FOUND_ROWS a.*,
		(
		  select concat(first_name,' ',last_name)
		  from  {{driver}}
		  where
		  driver_id=a.driver_id
		  limit 0,1
		) as driver_name
		FROM
		{{driver_pushlog}} a
		WHERE 1
		$and		
		$sWhere
		$sOrder
		$sLimit
		";
        if (isset($_GET['debug'])) {
            dump($stmt);
        }

        $DbExt = new DbExt;
        if ($res = $DbExt->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $DbExt->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {
                $date_created = Yii::app()->functions->prettyDate($val['date_created'], true);
                $date_created = Yii::app()->functions->translateDate($date_created);

                $status = "<span class=\"tag_push rounded " . $val['status'] . "\">" . Driver::t($val['status']) . "</span>";

                $feed_data['aaData'][] = array(
                    $val['push_id'],
                    //$val['driver_id'],
                    $val['driver_name'] . " (" . $val['driver_id'] . ")",
                    Driver::t($val['push_title']),
                    $val['push_message'],
                    Driver::t($val['push_type']),
                    $val['device_platform'] . "<br><span class=\"concat-text\">" . $val['device_id'] . "</span>",
                    $status . "<br>" . $date_created,
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actionChartReports() {
        //dump($this->data);
        $data = array();
        if ($data = Driver::generateReports($this->data['chart_type'], $this->data['time_selection'], $this->data['team_selection'], $this->data['driver_selection'], $this->data['chart_type_option'], $this->data['start_date'], $this->data['end_date']
                )) {
            
        }

        $new_data = array();

        if (is_array($data) && count($data) >= 1) {

            $first_date = date("Y-m-d", strtotime($data[0]['delivery_date'] . "-1 day"));
            $new_data[] = array(
                'date' => $first_date,
                'successful' => 0,
                'cancelled' => 0,
                'failed' => 0
            );

            foreach ($data as $val) {
                //dump($val);
                switch ($val['status']) {

                    case "successful":
                        $new_data[] = array(
                            'date' => $val['delivery_date'],
                            'successful' => $val['total'],
                            'driver_name' => isset($val['driver_name']) ? $val['driver_name'] : ''
                        );
                        break;

                    case "cancelled":
                        $new_data[] = array(
                            'date' => $val['delivery_date'],
                            'cancelled' => $val['total'],
                            'driver_name' => isset($val['driver_name']) ? $val['driver_name'] : ''
                        );
                        break;

                    case "failed":
                        $new_data[] = array(
                            'date' => $val['delivery_date'],
                            'failed' => $val['total'],
                            'driver_name' => isset($val['driver_name']) ? $val['driver_name'] : ''
                        );
                        break;

                    default:
                        break;
                }
            }
        } else {
            /* $new_data[]=array(
              'date'=>date("Y-m-d"),
              'failed'=>0,
              'driver_name'=>''
              ); */
        }

        $table = '';


        if ($this->data['chart_type_option'] == "agent") {

            ob_start();
            require_once('charts-bar.php');
            $charts = ob_get_contents();
            ob_end_clean();

            ob_start();
            require_once('chart-bar-table.php');
            $table = ob_get_contents();
            ob_end_clean();
        } else {
            ob_start();
            require_once('charts.php');
            $charts = ob_get_contents();
            ob_end_clean();

            ob_start();
            require_once('chart-table.php');
            $table = ob_get_contents();
            ob_end_clean();
        }
        $this->code = 1;
        $this->msg = "OK";
        $this->details = array(
            'charts' => $charts,
            'table' => $table
        );
        $this->jsonResponse();
    }

    public function actionForgotPassword() {
        if ($res = AdminFunctions::getUsuarioByEmail($this->data['email'])) {
            if (AdminFunctions::sendResetPassword($res)) {
                $this->code = 1;
                $this->msg = "OK";
                $this->details = Yii::app()->createUrl('/app/resetpassword', array(
                    'hash' => $res['token']
                ));
            } else
                $this->msg = t("No se pudo procesar su requerimiento");
        } else
            $this->msg = t("La dirección de mail no existe en nuestros registros");
        $this->jsonResponse();
    }

    public function actionresetPassword() {

        if ($this->data['password'] != $this->data['cpassword']) {
            $this->msg = t("Password de confirmación no coincide con password");
            $this->jsonResponse();
            Yii::app()->end();
        }
        if (isset($this->data['hash'])) {
            if ($res = FrontFunctions::getUsuarioByToken($this->data['hash'])) {

                if ($this->data['verification_code'] != $res['verification_code']) {
                    $this->msg = t("Código de verificación está incorrecto");
                    $this->jsonResponse();
                    Yii::app()->end();
                }

                $encryption_type = Yii::app()->params->encryption_type;
                if (empty($encryption_type)) {
                    $encryption_type = 'yii';
                }

                $id = $res['id'];
                if ($encryption_type == "yii") {
                    $params['password'] = CPasswordHelper::hashPassword($this->data['password']);
                } else
                    $params['password'] = md5($this->data['password']);

                $params['date_modified'] = AdminFunctions::dateNow();

                $db = new DbExt;
                if ($db->updateData("{{usuario}}", $params, 'id', $id)) {
                    $this->code = 1;
                    $this->msg = t("Su password ha sido reseteado");
                    $this->details = Yii::app()->createUrl('/app/login');
                } else
                    $this->msg = t("no se puede actualizar el registro");
            } else
                $this->msg = t("Hash no existe en nuestros registros");
        } else
            $this->msg = t("Hash faltante");
        $this->jsonResponse();
    }

    public function actionupdateProfile() {

        $encryption_type = Yii::app()->params->encryption_type;
        if (empty($encryption_type)) {
            $encryption_type = 'yii';
        }

        $params = $this->data;
        unset($params['action']);
        unset($params['cpassword']);
        unset($params['password']);
        $params['date_modified'] = AdminFunctions::dateNow();
        $params['ip_address'] = $_SERVER['REMOTE_ADDR'];

        if (!empty($this->data['password'])) {
            if ($this->data['password'] != $this->data['cpassword']) {
                $this->msg = t("Confirm password does not macth with your new password");
                $this->jsonResponse();
                Yii::app()->end();
            }

            if ($encryption_type == "yii") {
                $params['password'] = CPasswordHelper::hashPassword($this->data['password']);
            } else
                $params['password'] = md5($this->data['password']);
        }

        $customer_id = Driver::getUserId();
        if (FrontFunctions::checkByEmailExist($this->data['email_address'], $customer_id)) {
            $this->msg = t("Email address already exist");
            $this->jsonResponse();
            Yii::app()->end();
        }

        if (is_numeric($customer_id)) {
            $db = new DbExt;
            if ($db->updateData('{{customer}}', $params, 'customer_id', $customer_id)) {
                $this->code = 1;
                $this->msg = t("Profile updated");
            } else
                $this->msg = t("failed cannot update record");
        } else
            $this->msg = t("Your session has expired please re-login");

        $this->jsonResponse();
    }

    public function actionsendPush() {
        if ($res = Driver::driverInfo($this->data['driver_id_push'])) {
            $params = array(
                'customer_id' => $res['customer_id'],
                'device_platform' => $res['device_platform'],
                'device_id' => $res['device_id'],
                'push_title' => $this->data['x_push_title'],
                'push_message' => $this->data['x_push_message'],
                'push_type' => "campaign",
                'actions' => "private",
                'driver_id' => $res['driver_id'],
                'date_created' => AdminFunctions::dateNow(),
                'json_response' => ''
            );
            $db_ext = new DbExt;
            if ($db_ext->insertData("{{driver_pushlog}}", $params)) {
                $push_id = Yii::app()->db->getLastInsertID();
                Driver::fastRequest(Driver::getHostURL() . Yii::app()->createUrl("cron/processpush"));
                $this->code = 1;
                $this->msg = t("Successful");
            } else
                $this->msg = t("Something went wrong cannot insert records");
        } else
            $this->msg = t("Driver info not found");
        $this->jsonResponse();
    }

    #start contactos region

    public function actionmisContactosList() {
        $aColumns = array(
            'identificacion',
            'empresa',
            'email',
            'l.nombre',
            'direccion',
            'z.zona',
            'contacto'
        );


        $t = AjaxDataTables::AjaxData($aColumns);
        if (isset($_GET['debug'])) {
            dump($t);
        }

        if (is_array($t) && count($t) >= 1) {
            $sWhere = $t['sWhere'];
            $sOrder = $t['sOrder'];
            $sLimit = $t['sLimit'];
        }

        $and = '';
        //$and = " AND estado !='entregado' ";

        $stmt = "SELECT SQL_CALC_FOUND_ROWS o.*,z.zona AS 'sector',l.nombre AS 'ciudad',CONCAT(c.nombre,' ',c.apellido,' ',c.prefijo) AS nombres 
		FROM
		{{contactos}} o
                INNER JOIN {{zonas}} z on o.zona=z.id
                INNER JOIN {{locacion}} l on z.id_locacion=l.id 
                INNER JOIN {{clientes}} c on o.id_cliente=c.id_cliente  
		WHERE 1		
		$and
		$sWhere
		ORDER BY o.empresa ASC 
		limit 20;
		";
        if (isset($_GET['debug'])) {
            dump($stmt);
        }


        $_SESSION['xpress_stmt_misContactosList'] = $stmt;

        $DbExt = new DbExt;
        $DbExt->qry("SET SQL_BIG_SELECTS=1");

        if ($res = $DbExt->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $DbExt->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {
                $action = "<div class=\"table-action\">";
                $action1 = "<a class=\"btn btn-primary details\" data-hidden_id='contacto_id' data-modal_detalle='detalle-contacto-modal'
			    	data-id=\"" . $val['contacto_id'] . "\" href=\"#no\">" . t("Detalle") . "</a>";


                $action .= $action1;
                $action .= "&nbsp;|&nbsp;";

                $action .= "</div>";

                $feed_data['aaData'][] = array(
                    $val['identificacion'],
                    $val['empresa'],
                    $val['email'],
                    $val['ciudad'],
                    $val['direccion'],
                    $val['sector'],
                    $val['contacto'],
                    $val['nombres'],
                    $action1
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actionmisContactosFilteredList() {
        $aColumns = array(
            'identificacion',
            'empresa',
            'email',
            'l.nombre',
            'direccion',
            'z.zona',
            'contacto'
        );


        $t = AjaxDataTables::AjaxData($aColumns);
        if (isset($_GET['debug'])) {
            dump($t);
        }

        if (is_array($t) && count($t) >= 1) {
            $sWhere = $t['sWhere'];
            $sOrder = $t['sOrder'];
            $sLimit = $t['sLimit'];
        }



        $and = '';
        //$and .= " AND o.estado !='Completado' ";

        if (isset($_GET["search"]) && $_GET["search"] != "") {
            $and .= " AND (UPPER(o.empresa) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(o.direccion) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(l.nombre) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(z.zona) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(o.contacto) like  '%" . $_GET["search"] . "%' )";
        }

        if (isset($_GET["id_cliente"]) && $_GET["id_cliente"] != "" && $_GET["id_cliente"] != "0") {
            $and .= "  AND c.id_cliente =  '" . $_GET["id_cliente"] . "' ";
        }


        $stmt = "SELECT SQL_CALC_FOUND_ROWS o.*,z.zona AS 'sector',l.nombre AS 'ciudad',CONCAT(c.nombre,' ',c.apellido,' ',c.prefijo) AS nombres 
		FROM
		{{contactos}} o
                INNER JOIN {{zonas}} z on o.zona=z.id
                INNER JOIN {{locacion}} l on z.id_locacion=l.id 
                INNER JOIN {{clientes}} c on o.id_cliente=c.id_cliente  
                WHERE 1 
		$and
		$sWhere
		ORDER BY o.empresa ASC;
		";
        if (isset($_GET['debug'])) {
            dump($stmt);
        }

        $_SESSION['xpress_stmt_misContactosList'] = $stmt;

        $DbExt = new DbExt;
        $DbExt->qry("SET SQL_BIG_SELECTS=1");

        if ($res = $DbExt->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $DbExt->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {
                $action = "<div class=\"table-action\">";
                $action1 = "<a class=\"btn btn-primary details\" data-hidden_id='contacto_id' data-modal_detalle='detalle-contacto-modal'
			    	data-id=\"" . $val['contacto_id'] . "\" href=\"#no\">" . t("Detalle") . "</a>";


                $action .= $action1;
                $action .= "&nbsp;|&nbsp;";

                $action .= "</div>";


                $feed_data['aaData'][] = array(
                    $val['identificacion'],
                    $val['empresa'],
                    $val['email'],
                    $val['ciudad'],
                    $val['direccion'],
                    $val['sector'],
                    $val['contacto'],
                    $val['nombres'],
                    $action1
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actiongetDetalleContacto() {
        if (isset($this->data['contacto_id'])) {
            if ($res = Driver::getContactoId($this->data['contacto_id'])) {
                $res['contacto'] = !empty($res['contacto']) ? $res['contacto'] : '';
                $res['direccion'] = !empty($res['direccion']) ? $res['direccion'] : '';
                $res['ciudad'] = !empty($res['ciudad']) ? $res['ciudad'] : '';
                $res['ciudad_id'] = !empty($res['ciudad_id']) ? $res['ciudad_id'] : '';
                $res['zona'] = !empty($res['zona']) ? $res['zona'] : '';
                $res['sector'] = !empty($res['sector']) ? $res['sector'] : '';
                $res['empresa'] = !empty($res['empresa']) ? $res['empresa'] : '';
                $res['telefono'] = !empty($res['telefono']) ? $res['telefono'] : '';
                $res['identificacion'] = !empty($res['identificacion']) ? $res['identificacion'] : '';
                $res['email'] = !empty($res['email']) ? $res['email'] : '';
                $res['nombres'] = !empty($res['nombres']) ? $res['nombres'] : '';

                $this->code = 1;
                $this->msg = "OK";
                $this->details = $res;
                //dump($this->details);
            } else
                $this->msg = Driver::t("No se encontró el registro");
        } else
            $this->msg = Driver::t("falta el parámetro id");
        $this->jsonResponse();
    }

    public function actiongetEditContacto() {
        $this->actiongetDetalleContacto();
    }

    public function actionAddContacto() {

        /* dump($this->data);
          die(); */

        $DbExt = new DbExt;
        $req = array(
            'identificacion' => Driver::t("Por favor ingrese identificación"),
            'zona' => Driver::t("Por favor ingrese zona"),
            'provincia' => Driver::t("Por favor ingrese provincia"),
            'ciudad_id' => Driver::t("Por favor ingrese ciudad"),
            'id_cliente' => Driver::t("Por favor seleccione el cliente"),
        );
        $Validator = new Validator;
        $Validator->required($req, $this->data);
        if ($Validator->validate() && $this->data['zona'] != '0') {

            $params = array(
                'identificacion' => isset($this->data['identificacion']) ? $this->data['identificacion'] : '',
                'empresa' => isset($this->data['empresa']) ? $this->data['empresa'] : '',
                'direccion' => isset($this->data['direccion']) ? $this->data['direccion'] : '',
                'zona' => is_numeric($this->data['zona']) ? $this->data['zona'] : '',
                'contacto' => isset($this->data['contacto']) ? $this->data['contacto'] : '',
                'telefono' => isset($this->data['telefono']) ? $this->data['telefono'] : '',
                'email' => isset($this->data['email']) ? $this->data['email'] : '',
                'date_created' => AdminFunctions::dateNow(),
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'id_cliente' => isset($this->data['id_cliente']) ? $this->data['id_cliente'] : '',
            );
            try {
                if (is_numeric($this->data['contacto_id'])) {

                    unset($params['date_created']);
                    $params['date_modified'] = AdminFunctions::dateNow();


                    if ($DbExt->updateData("{{contactos}}", $params, 'contacto_id', $this->data['contacto_id'])) {
                        $this->code = 1;
                        $this->msg = Driver::t("Operación exitosa");
                    } else
                        $this->msg = Driver::t("Problema al actualizar");
                } else {

                    if ($DbExt->insertData("{{contactos}}", $params)) {
                        $orden_id = Yii::app()->db->getLastInsertID();

                        $this->code = 1;
                        $this->msg = Driver::t("Operación exitosa");
                    } else
                        $this->msg = Driver::t("Error al insertar");
                }
            } catch (Exception $e) {
                $this->msg = $e->getMessage();
            }
        } else
            $this->msg = $Validator->getErrorAsHTML();
        $this->jsonResponse();
    }

    public function actiongetDeleteContacto() {
        if (isset($this->data['tbl']) && isset($this->data['whereid'])) {
            $wherefield = $this->data['whereid'];
            $tbl = $this->data['tbl'];
            $stmt = "
			DELETE FROM
			{{{$tbl}}}
			WHERE
			$wherefield=" . Driver::q($this->data['id']) . "
			";
            //dump($stmt);
            $DbExt = new DbExt;
            $DbExt->qry($stmt);
            $this->code = 1;
            $this->msg = Driver::t("Successful");
        } else
            $this->msg = Driver::t("Missing parameters");
        $this->jsonResponse();
    }

    /* end contactos region */

    public function actionGetContactInfo() {
        if ($res = Driver::getContactByID($this->data['contact_id'], Driver::getUserId())) {
            $this->msg = "OK";
            $this->code = 1;
            $this->details = $res;
        } else
            $this->msg = t("Contact not found");
        $this->jsonResponse();
    }

    public function actionLoadContactInfo() {
        $this->actionGetContactInfo();
    }

    public function actionLoadContactInfo2() {
        $this->actionGetContactInfo();
    }

    public function actionSaveServices() {
        if (!Driver::islogin()) {
            $this->msg = Driver::t("Sorry but your session has expired");
            $this->jsonResponse();
            Yii::app()->end();
        }

        $customer_id = Driver::getUserId();

        $params = array(
            'services' => isset($this->data['services_id']) ? json_encode($this->data['services_id']) : '',
            'date_modified' => AdminFunctions::dateNow()
        );
        $db = new DbExt;
        $db->updateData("{{customer}}", $params, 'customer_id', $customer_id);

        $this->code = 1;
        $this->msg = Yii::t("default", "Setting saved");
        $this->jsonResponse();
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
        $path_to_upload = Driver::driverUploadCVPath();
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

    public function actionUploadcompanylogo() {
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

    public function actionSmsloglist() {
        $aColumns = array(
            'id',
            'to_number',
            'sms_text',
            'provider',
            'msg',
            'date_created'
        );
        $t = AjaxDataTables::AjaxData($aColumns);
        if (isset($_GET['debug'])) {
            dump($t);
        }

        if (is_array($t) && count($t) >= 1) {
            $sWhere = $t['sWhere'];
            $sOrder = $t['sOrder'];
            $sLimit = $t['sLimit'];
        }

        $and = " AND customer_id = " . Driver::q(Driver::getUserId()) . " ";


        $stmt = "SELECT SQL_CALC_FOUND_ROWS a.*			
		FROM
		{{sms_logs}} a
		WHERE 1
		$and		
		$sWhere
		$sOrder
		$sLimit
		";
        if (isset($_GET['debug'])) {
            dump($stmt);
        }

        $DbExt = new DbExt;
        if ($res = $DbExt->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $DbExt->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {
                $date_created = Yii::app()->functions->prettyDate($val['date_created'], true);
                $date_created = Yii::app()->functions->translateDate($date_created);

                $feed_data['aaData'][] = array(
                    $val['id'],
                    $val['to_number'],
                    $val['sms_text'],
                    $val['provider'],
                    $val['msg'],
                    $date_created
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actionGetSMSBalance() {
        $id = Driver::getUserId();
        if ($res = AdminFunctions::getCustomerByID($id)) {
            if ($res['with_sms'] == 1) {
                $balance = Driver::getSMSBalance($id);
                if ($balance['code'] == 1) {
                    if ($balance['balance'] <= 0) {
                        $this->code = 1;
                        $this->msg = t("you have insufficient sms credits to send text message");
                        //$this->msg.="<br/>";
                        //$this->msg.='<a href="'.Yii::app()->createUrl('app/smspurchase').'">'.t("click here to purchase sms credits").'</a>';
                        $this->details = $balance['balance'];
                    } else
                        $this->msg = "HAS BALANCE";
                } else
                    $this->msg = "NO SMS BALANCE";
            } else
                $this->msg = "NO SMS";
        } else
            $this->msg = "ID NOT FOUND";
        $this->jsonResponse();
    }

    public function actionEmailLogs() {
        $aColumns = array(
            'id',
            'email_address',
            'subject',
            'content',
            'status',
            'date_created'
        );
        $t = AjaxDataTables::AjaxData($aColumns);
        if (isset($_GET['debug'])) {
            dump($t);
        }

        if (is_array($t) && count($t) >= 1) {
            $sWhere = $t['sWhere'];
            $sOrder = $t['sOrder'];
            $sLimit = $t['sLimit'];
        }

        $and = " AND customer_id = " . Driver::q(Driver::getUserId()) . " ";


        $stmt = "SELECT SQL_CALC_FOUND_ROWS a.*			
		FROM
		{{email_logs}} a
		WHERE 1
		$and		
		$sWhere
		$sOrder
		$sLimit
		";
        if (isset($_GET['debug'])) {
            dump($stmt);
        }

        $DbExt = new DbExt;
        if ($res = $DbExt->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $DbExt->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {
                $date_created = Yii::app()->functions->prettyDate($val['date_created'], true);
                $date_created = Yii::app()->functions->translateDate($date_created);

                $status = "<span class=\"tag " . $val['status'] . " \">" . Driver::t($val['status']) . "</span>";

                $feed_data['aaData'][] = array(
                    $val['id'],
                    $val['email_address'],
                    $val['subject'],
                    $val['content'],
                    $status,
                    $date_created
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actionSendBulkPush() {
        if ($this->data['team_id'] <= 0) {
            $this->msg = t("Team is required");
            $this->jsonResponse();
        }
        $params = array(
            'customer_id' => Driver::getUserId(),
            'team_id' => $this->data['team_id'],
            'push_title' => $this->data['push_title'],
            'push_message' => $this->data['push_message'],
            'date_created' => AdminFunctions::dateNow(),
            'ip_address' => $_SERVER['REMOTE_ADDR']
        );
        $db = new DbExt;
        if ($db->insertData("{{push_broadcast}}", $params)) {
            $this->code = 1;
            $this->msg = Driver::t("Your request has been receive please wait while the cron process your request");
        } else
            $this->msg = Driver::t("failed cannot insert record");
        $this->jsonResponse();
    }

    public function actionBroadcastLogs() {
        $aColumns = array(
            'broadcast_id',
            'team_id',
            'push_title',
            'push_message',
            'status',
            'date_created'
        );
        $t = AjaxDataTables::AjaxData($aColumns);
        if (isset($_GET['debug'])) {
            dump($t);
        }

        if (is_array($t) && count($t) >= 1) {
            $sWhere = $t['sWhere'];
            $sOrder = $t['sOrder'];
            $sLimit = $t['sLimit'];
        }

        $and = " AND customer_id = " . Driver::q(Driver::getUserId()) . " ";


        $stmt = "SELECT SQL_CALC_FOUND_ROWS a.*,
		(
			select team_name
			from {{driver_team}}
			where
			team_id=a.team_id
			limit 0,1
		) as team_name
		FROM
		{{push_broadcast}} a
		WHERE 1
		$and		
		$sWhere
		$sOrder
		$sLimit
		";
        if (isset($_GET['debug'])) {
            dump($stmt);
        }

        $DbExt = new DbExt;
        if ($res = $DbExt->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $DbExt->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {
                $date_created = Yii::app()->functions->prettyDate($val['date_created'], true);
                $date_created = Yii::app()->functions->translateDate($date_created);

                $status = "<span class=\"tag " . $val['status'] . " \">" . Driver::t($val['status']) . "</span>";

                $view = '<a href="' . Yii::app()->createUrl('/app/pushlogs', array(
                            'broadcast_id' => $val['broadcast_id']
                        )) . '" class="btn btn-info">' . t("view") . '</a>';

                if ($val['status'] == "pending") {
                    $view = '';
                }

                $feed_data['aaData'][] = array(
                    $val['broadcast_id'],
                    $val['team_name'],
                    $val['push_title'],
                    $val['push_message'],
                    $status,
                    $date_created,
                    $view
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actionloadAgentTrackBack() {
        if (Driver::islogin()) {
            if ($res = Driver::getBackTrackRecords(
                            Driver::getUserId(), $this->data['track_driver_id'], $this->data['track_date']
                    )) {

                $this->code = 1;
                $this->msg = t("Successful");
                $this->details = $res;
            } else
                $this->msg = t("Records not found");
        } else
            $this->msg = t("Session has expired");

        $this->jsonResponse();
    }

    public function actioncheckActivity() {
        if (Driver::islogin()) {
            $user_id = Driver::getUserId();

            $date_now = date("Y-m-d");
            $res = '';
            $resp = '';
            $found = false;

            if ($res = Driver::checkNewTask($user_id, $date_now)) {
                $res[] = 'checkNewTask';
                $found = true;
            }

            if ($resp = Driver::checkNewUpdatedDriver($user_id)) {
                $resp[] = 'checkNewUpdatedDriver';
                $found = true;
            }
            if ($resp = Driver::checkNewOfflineDriver($user_id)) {
                $resp[] = 'checkNewOfflineDriver';
                $found = true;
            }

            if ($resp = Driver::getOfflineDriver($user_id)) {
                $resp[] = 'getOfflineDriver';
                $found = true;
            }

            $enabled_critical_task = getOption($user_id, 'enabled_critical_task');
            $critical_minutes = getOption($user_id, 'critical_minutes');
            if ($critical_minutes <= 0) {
                $critical_minutes = 5;
            }

            if ($enabled_critical_task == 1) {
                if (Driver::checkCriticalTask($user_id, $critical_minutes)) {
                    $found = true;
                }
            }

            if ($resp = Driver::checkNewUpdatedtask($user_id)) {
                $found = true;
                $resp[] = 'checkNewUpdatedtask';
            }

            if ($found) {
                $this->code = 1;
                $this->msg = "there is activity";
                $this->details = array(
                    'res' => $res,
                    'resp' => $resp
                );
            } else
                $this->msg = "no changes";
        } else
            $this->msg = t("Session has expired");
        $this->jsonResponse();
    }

    public function actionloadTrackDate() {
        $html = '<option value="-1">' . t("Please select") . '</option>';
        $driver_id = isset($this->data['driver_id']) ? $this->data['driver_id'] : '';

        $user_id = Driver::getUserId();
        if ($user_id > 0) {
            if ($res = Driver::backTrackList2($driver_id, $user_id)) {
                foreach ($res as $val) {
                    $html .= '<option value="' . $val['date_log'] . '">' . $val['date_log'] . '</option>';
                }
                $this->code = 1;
                $this->msg = "OK";
                $this->details = $html;
            } else
                $this->msg = "no results";
        } else
            $this->msg = t("Sorry but your session has expired");
        $this->jsonResponse();
    }

    public function actionloadFilterForm() {
        $html = Yii::app()->controller->renderPartial('/app/filter_map_form', array(
                ), true);
        $this->code = 1;
        $this->msg = "OK";
        $this->details = $html;
        $this->jsonResponse();
    }

    public function actionmapFilterSettings() {
        Yii::app()->functions->updateOption('map_hide_pickup', isset($this->data['map_hide_pickup']) ? $this->data['map_hide_pickup'] : '', Driver::getUserId()
        );

        Yii::app()->functions->updateOption('map_hide_delivery', isset($this->data['map_hide_delivery']) ? $this->data['map_hide_delivery'] : '', Driver::getUserId()
        );

        Yii::app()->functions->updateOption('map_hide_success_task', isset($this->data['map_hide_success_task']) ? $this->data['map_hide_success_task'] : '', Driver::getUserId()
        );

        Yii::app()->functions->updateOption('driver_include_offline_driver_map', isset($this->data['driver_include_offline_driver_map']) ? $this->data['driver_include_offline_driver_map'] : '', Driver::getUserId()
        );

        $this->code = 1;
        $this->msg = Yii::t("default", "Setting saved");
        $this->jsonResponse();
    }

}

/* end class*/