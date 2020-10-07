<?php

if (!isset($_SESSION)) {
    session_start();
}

class AjaxuserController extends CController {

    public $code = 2;
    public $msg;
    public $details;
    public $data;
    public $db;

    public function __construct() {
        $this->data = $_POST;
        if (isset($_GET['sEcho'])) {
            $this->data = $_GET;
        }
        $this->db = new DbExt();
    }

    public function init() {
        // set website timezone
        $website_timezone = Yii::app()->functions->getOptionAdmin("website_timezone");
        if (!empty($website_timezone)) {
            Yii::app()->timeZone = $website_timezone;
        }

        if (isset($this->data['language'])) {
            Yii::app()->language = $this->data['language'];
        }
        unset($this->data['language']);
    }

    private function jsonResponse() {
        $resp = array('code' => $this->code, 'msg' => $this->msg, 'details' => $this->details);
        echo CJSON::encode($resp);
        Yii::app()->end();
    }

    public function actionLogin() {
        $req = array(
            'email' => Driver::t("Email es requerido"),
            'password' => Driver::t("password es requerido"),
        );
        $Validator = new Validator;
        $Validator->required($req, $this->data);
        if ($Validator->validate()) {

            if ($res = Driver::LoginUser(trim($this->data['email']), trim($this->data['password']))) {

                if ($res['status'] == "active") {

                    $_SESSION['xpress'] = $res;
                    $this->code = 1;
                    $this->msg = t("Login Successful");
                    $this->details = Yii::app()->createUrl('/user/dashboard');

                    if (isset($this->data['remember'])) {
                        Yii::app()->request->cookies['kt_usernameUser'] = new CHttpCookie('kt_usernameUser', $this->data['email']);
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
                                Yii::app()->request->cookies['kt_passwordUser'] = new CHttpCookie('kt_passwordUser', $password);
                            } catch (Exception $e) {
                                $this->msg = t("Path is not writable by the server") . " $runtime_path";
                                $this->code = 2;
                            }
                        }
                    } else {
                        unset(Yii::app()->request->cookies['kt_usernameUser']);
                        unset(Yii::app()->request->cookies['kt_passwordUser']);
                    }
                } else
                    $this->msg = t("Login fallido. su cuenta está") . " " . $res['status'];
            } else
                $this->msg = t("Login fallido, email o password incorrectos");
        } else
            $this->msg = $Validator->getErrorAsHTML();
        $this->jsonResponse();
    }

    /* start Pedidos region */

    public function actionmisPedidosList() {
        $aColumns = array(
            'codigo_orden',
            'tipo_servicio',
            'o.destino',
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
        $and = " AND id_cliente =" . Driver::q(Driver::getClienteId()) . "  ";
        //$and = " AND estado !='entregado' ";

        $stmt = "SELECT SQL_CALC_FOUND_ROWS o.*,z_destino.zona AS 'zona_destino_nombre',z_origen.zona AS 'zona_origen_nombre',l_destino.nombre AS 'ciudad_destino',l_origen.nombre AS 'ciudad_origen'
		FROM
		{{orden}} o
                INNER JOIN {{zonas}} z_destino on o.zona_destino=z_destino.id
                INNER JOIN {{locacion}} l_destino on z_destino.id_locacion=l_destino.id
                INNER JOIN {{zonas}} z_origen on o.zona_origen=z_origen.id
                INNER JOIN {{locacion}} l_origen on z_origen.id_locacion=l_origen.id
		WHERE 1		
		$and
		$sWhere
		ORDER BY o.date_created DESC 
		limit 20;
		";
        if (isset($_GET['debug'])) {
            dump($stmt);
        }

        $stmt = $_SESSION['xpress_stmt_misPedidosList'];

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


                $action .= $action1;
                $action .= "&nbsp;|&nbsp;";

                $action .= "</div>";


                $feed_data['aaData'][] = array(
                    $selecciona,
                    $val['codigo_orden'] . $action,
                    $val['tipo_servicio'],
                    $val['destino'],
                    $val['ciudad_destino'],
                    $val['direccion_destino'],
                    $val['zona_destino_nombre'],
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

    public function actionmisPedidosFilteredList() {
        $aColumns = array(
            'codigo_orden',
            'tipo_servicio',
            'o.destino',
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
        $and = " AND o.id_cliente =" . Driver::q(Driver::getClienteId()) . "  ";
        //$and .= " AND o.estado !='Completado' ";

        if (isset($_GET["search"]) && $_GET["search"] != "") {
            $and .= " AND (UPPER(o.origen) like  '%" . $_GET["search"] . "%' ";
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
        if (isset($_GET["codigo_orden"]) && $_GET["codigo_orden"] != "") {
            $and .= "  AND UPPER(o.codigo_orden) like  '%" . $_GET["codigo_orden"] . "%' ";
        }
        if (isset($_GET["estado"]) && $_GET["estado"] != "") {
            $and .= "  AND o.estado =  '" . $_GET["estado"] . "' ";
        }
        if (isset($_GET["tipo_servicio"]) && $_GET["tipo_servicio"] != "") {
            $and .= "  AND o.tipo_servicio =  '" . $_GET["tipo_servicio"] . "' ";
        }

        $stmt = "SELECT SQL_CALC_FOUND_ROWS o.*,z_destino.zona AS 'zona_destino_nombre',z_origen.zona AS 'zona_origen_nombre',l_destino.nombre AS 'ciudad_destino',l_origen.nombre AS 'ciudad_origen'
		FROM
		{{orden}} o
                INNER JOIN {{zonas}} z_destino on o.zona_destino=z_destino.id
                INNER JOIN {{locacion}} l_destino on z_destino.id_locacion=l_destino.id
                INNER JOIN {{zonas}} z_origen on o.zona_origen=z_origen.id
                INNER JOIN {{locacion}} l_origen on z_origen.id_locacion=l_origen.id
		WHERE 1		
		$and
		ORDER BY o.date_created DESC 
                $sLimit;
		";
        if (isset($_GET['debug'])) {
            dump($stmt);
        }

        $_SESSION['xpress_stmt_misPedidosList'] = $stmt;

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


                $action .= $action1;
                $action .= "&nbsp;|&nbsp;";

                $action .= "</div>";


                $feed_data['aaData'][] = array(
                    $selecciona,
                    $val['codigo_orden'] . $action,
                    $val['tipo_servicio'],
                    $val['destino'],
                    $val['ciudad_destino'],
                    $val['direccion_destino'],
                    $val['zona_destino_nombre'],
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
                $res['telefono_remitente'] = !empty($res['telefono_remitente']) ? $res['telefono_remitente'] : '';
                $res['destino'] = !empty($res['destino']) ? $res['destino'] : '';
                $res['direccion_destino'] = !empty($res['direccion_destino']) ? $res['direccion_destino'] : '';
                $res['ciudad_destino'] = !empty($res['ciudad_destino']) ? $res['ciudad_destino'] : '';
                $res['zona_destino_nombre'] = !empty($res['zona_destino_nombre']) ? $res['zona_destino_nombre'] : '';
                $res['recibe'] = !empty($res['recibe']) ? $res['recibe'] : '';
                $res['date_created'] = !empty($res['date_created']) ? date("Y-m-d g:i a", strtotime($res['date_created'])) : '-';
                $res['fecha_envio'] = !empty($res['fecha_envio']) ? date("Y-m-d g:i a", strtotime($res['fecha_envio'])) : '-';
                $res['fecha_entrega'] = !empty($res['fecha_entrega']) ? date("Y-m-d g:i a", strtotime($res['fecha_entrega'])) : '-';
                $res['telefono_recibe'] = !empty($res['telefono_recibe']) ? $res['telefono_recibe'] : '';


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

    public function actionAddOrden() {

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
            'ciudad_destino_id' => Driver::t("Por favor ingrese ciudad destino")
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
                'recibe' => isset($this->data['recibe']) ? $this->data['recibe'] : '',
                'telefono_recibe' => isset($this->data['telefono_recibe']) ? $this->data['telefono_recibe'] : '',
                'contacto_origen' => (is_numeric($this->data['contacto_origen']) && $this->data['contacto_origen'] != 0) ? $this->data['contacto_origen'] : null,
                'contacto_destino' => (is_numeric($this->data['contacto_destino']) && $this->data['contacto_destino'] != 0) ? $this->data['contacto_destino'] : null,
                'date_created' => AdminFunctions::dateNow(),
                'origen_orden' => 'CLIENTE',
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'id_cliente' => Driver::getClienteId()
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
                    if ($resCli = AdminFunctions::getClienteById(Driver::getClienteId())) {
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
                        $this->msg = Driver::t("Cliente no encontrado");
                }
            } catch (Exception $e) {
                $this->msg = $e->getMessage();
            }
        } else
            $this->msg = $Validator->getErrorAsHTML();
        $this->jsonResponse();
    }

    public function actionGetContactoInfo() {
        if ($res = Driver::getContactoByID($this->data['contacto_id'], Driver::getClienteId())) {
            $this->msg = "OK";
            $this->code = 1;
            $this->details = $res;
        } else
            $this->msg = t("Contacto no encontrado");
        $this->jsonResponse();
    }

    public function actionLoadContactInfoOrigen() {
        $this->actionGetContactoInfo();
    }

    public function actionLoadContactInfoDestino() {
        $this->actionGetContactoInfo();
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

    #end Pedidos region
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
        $and = " AND id_cliente =" . Driver::q(Driver::getClienteId()) . "  ";
        //$and = " AND estado !='entregado' ";

        $stmt = "SELECT SQL_CALC_FOUND_ROWS o.*,z.zona AS 'sector',l.nombre AS 'ciudad'
		FROM
		{{contactos}} o
                INNER JOIN {{zonas}} z on o.zona=z.id
                INNER JOIN {{locacion}} l on z.id_locacion=l.id
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
        $and = " AND o.id_cliente =" . Driver::q(Driver::getClienteId()) . "  ";
        //$and .= " AND o.estado !='Completado' ";

        if (isset($_GET["search"]) && $_GET["search"] != "") {
            $and .= " AND (UPPER(o.empresa) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(o.direccion) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(l.nombre) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(z.zona) like  '%" . $_GET["search"] . "%' ";
            $and .= " OR UPPER(o.contacto) like  '%" . $_GET["search"] . "%' )";
        }


        $stmt = "SELECT SQL_CALC_FOUND_ROWS o.*,z.zona AS 'sector',l.nombre AS 'ciudad'
		FROM
		{{contactos}} o
                INNER JOIN {{zonas}} z on o.zona=z.id
                INNER JOIN {{locacion}} l on z.id_locacion=l.id 
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
                'id_cliente' => Driver::getClienteId()
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

    public function actionDeleteRecords() {
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
            $this->msg = Driver::t("Operación exitosa");
        } else
            $this->msg = Driver::t("Parámetros faltantes");
        $this->jsonResponse();
    }

    public function actiongetDashboard() {
        $stmt = "SELECT count(o.orden_id) as 'TOTAL_ORDENES' FROM {{orden}} o where o.id_cliente=" . Driver::q(Driver::getClienteId());
        $stmt2 = "SELECT count(o.orden_id) as 'TOTAL_ORDENES_CREADAS' FROM {{orden}} o where o.id_cliente=" . Driver::q(Driver::getClienteId()) . " AND o.estado='Creado'";
        $stmt3 = "SELECT count(o.orden_id) as 'TOTAL_ORDENES_COMPLETADAS' FROM {{orden}} o where o.id_cliente=" . Driver::q(Driver::getClienteId()) . " AND o.estado='Completado'";
        $stmt4 = "SELECT count(o.orden_id) as 'TOTAL_ORDENES_EN_RUTA' FROM {{orden}} o where o.id_cliente=" . Driver::q(Driver::getClienteId()) . " AND o.estado='En-Transito'";

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

    public function actionForgotPassword() {
        if ($res = AdminFunctions::getClienteByEmail($this->data['email'])) {
            if (AdminFunctions::sendResetPasswordCliente($res)) {
                $this->code = 1;
                $this->msg = "OK";
                $this->details = Yii::app()->createUrl('/user/resetpassword', array(
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
            if ($res = FrontFunctions::getClienteByToken($this->data['hash'])) {

                if ($this->data['verification_code'] != $res['verification_code']) {
                    $this->msg = t("Código de verificación está incorrecto");
                    $this->jsonResponse();
                    Yii::app()->end();
                }

                $encryption_type = Yii::app()->params->encryption_type;
                if (empty($encryption_type)) {
                    $encryption_type = 'yii';
                }

                $id_cliente = $res['id_cliente'];
                if ($encryption_type == "yii") {
                    $params['password'] = CPasswordHelper::hashPassword($this->data['password']);
                } else
                    $params['password'] = md5($this->data['password']);

                $params['date_modified'] = AdminFunctions::dateNow();

                $db = new DbExt;
                if ($db->updateData("{{clientes}}", $params, 'id_cliente', $id_cliente)) {
                    $this->code = 1;
                    $this->msg = t("Su password ha sido reseteado");
                    $this->details = Yii::app()->createUrl('/user/login');
                } else
                    $this->msg = t("no se puede actualizar el registro");
            } else
                $this->msg = t("Hash no existe en nuestros registros");
        } else
            $this->msg = t("Hash faltante");
        $this->jsonResponse();
    }

}

/* end class*/