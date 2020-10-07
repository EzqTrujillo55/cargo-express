<?php

if (!isset($_SESSION)) {
    session_start();
}

class AjaxfrontController extends CController {

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

    public function actionSignup() {
        if ($this->data['cpassword'] != $this->data['password']) {
            $this->msg = t("Password de confirmación no concuerda");
            $this->jsonResponse();
            Yii::app()->end();
        }
        $params = $this->data;

        if ($res = AdminFunctions::getClienteByEmail($this->data['email'])) {
            $this->msg = Driver::t("Email ya existe");
            $this->jsonResponse();
            Yii::app()->end();
        }

        $password = $this->data['cpassword'];
        unset($params['action']);
        unset($params['cpassword']);


        $encryption_type = Yii::app()->params->encryption_type;
        if (empty($encryption_type)) {
            $encryption_type = 'yii';
        }

        if ($encryption_type == "yii") {
            $params['password'] = CPasswordHelper::hashPassword($params['password']);
        } else
            $params['password'] = md5($params['password']);

        $params['date_created'] = AdminFunctions::dateNow();
        $params['ip_address'] = $_SERVER['REMOTE_ADDR'];
        $token = md5(AdminFunctions::generateCode(10));
        $verification_code = AdminFunctions::generateNumericCode(5);
        $params['token'] = $token;
        $params['verification_code'] = $verification_code;

        if ($this->db->insertData("{{clientes}}", $params)) {
            $params['password'] = $password;
            if (AdminFunctions::sendRegistroCliente($params)) {
                $this->code = 1;
                $this->msg = Driver::t("Operación exitosa, espere la activación por parte del Admin");
            } else
                $this->msg = t("Cliente creado, No se pudo enviar el mail de registro");
            //$this->details = Yii::app()->createUrl('front/users-new', array('id' => $last_id, 'msg' => $this->msg)
            // );
        } else
            $this->msg = "Ha ocurrido un error, no se pueden insertar registros";

        $this->jsonResponse();
    }

    public function actionenviarFormularioContacto() {
        $code = 2;
        $msg;
        $details = $_POST;
        $data;
        $resp = '';
        $data = $_POST;

        if (isset($data['name']) && !empty($data['name']) &&
                isset($data['email']) && !empty($data['email']) &&
                isset($data['telefono']) && !empty($data['telefono']) &&
                isset($data['mensaje']) && !empty($data['mensaje'])
        ) {
            $nombre = $data['name'];
            $cedula = $data['email'];
            $destino = "supervisor@web-cargoxpress.com";
            $desde = "From:" . $nombre;
            $asunto = "Requiero Información de sus servicios";
            $mensaje = "Hola, mi nombre es " . $data['name'] . ", me podrían ayudar por favor con información sobre " . $data['mensaje'] . "? Mi número de teléfono es " . $data['telefono'];
            mail($destino, $asunto, $mensaje, $desde);
            $code = 1;
            $msg = 'OK';
            //header("location:graciaspwd.php");
        } else {
            $msg = "Problemas al enviar";
        }

        $resp = array('code' => $code, 'msg' => $msg, 'details' => $details);
        echo json_encode($resp);
    }

    public function actionbuscarOrden() {
        if (isset($this->data['codigo_orden_busqueda'])) {



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

}

/* end class*/