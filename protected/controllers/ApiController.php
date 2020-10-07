<?php

class ApiController extends CController {

    public $data;
    public $code = 2;
    public $msg = '';
    public $details = '';
    public $estado = false;

    public function __construct() {
        $this->data = $_POST;

        $website_timezone = Yii::app()->functions->getOptionAdmin("website_timezone");
        if (!empty($website_timezone)) {
            Yii::app()->timeZone = $website_timezone;
        }

        if (isset($_POST['lang_id'])) {
            Yii::app()->language = $_POST['lang_id'];
        }
    }

    public function beforeAction($action) {
        /* check if there is api has key */
        $action = Yii::app()->controller->action->id;
        $continue = true;
        //if($action=="getLanguageSettings" || $action=="GetAppSettings"){		
        $action = strtolower($action);
        if ($action == "getlanguagesettings" || $action == "getappsettings" || $action == "uploadprofile" || $action == "uploadtaskphoto" || $action == "updatedriverlocation") {
            $continue = false;
        }
        if ($continue) {
            $key = getOptionA('mobile_api_key');
            if (!empty($key)) {
                if (!isset($this->data['api_key'])) {
                    $this->data['api_key'] = '';
                }
                if (trim($key) != trim($this->data['api_key'])) {
                    $this->msg = $this->t("api hash key is not valid");
                    $this->output();
                    Yii::app()->end();
                }
            }
        }
        return true;
    }

    public function actionIndex() {
        echo 'Api is working';
    }

    private function q($data = '') {
        return Yii::app()->db->quoteValue($data);
    }

    private function t($message = '') {
        return Yii::t("default", $message);
    }

    private function output() {

        if (!isset($this->data['debug'])) {
            header('Access-Control-Allow-Origin: *');
            header('Content-type: application/javascript;charset=utf-8');
        }

        $resp = array(
            'code' => $this->code,
            'msg' => $this->msg,
            'details' => json_encode($this->details, JSON_UNESCAPED_SLASHES),
            'estado' => $this->estado,
            'request' => json_encode($this->data, JSON_UNESCAPED_SLASHES)
        );
        if (isset($this->data['debug'])) {
            dump($resp);
        }

        if (!isset($_GET['callback'])) {
            $_GET['callback'] = '';
        }

        //if (isset($_GET['json']) && $_GET['json'] == TRUE) {
        echo CJSON::encode($resp);
        //} else
        // echo $_GET['callback'] . '(' . CJSON::encode($resp) . ')';
        Yii::app()->end();
    }

    public function actionLogin() {
        if (!empty($this->data['email']) && !empty($this->data['password'])) {
            if ($res = Driver::driverAppLogin($this->data['email'], $this->data['password'])) {
                $token = md5(Driver::generateRandomNumber(5) . $this->data['email']);

                $params = array(
                    'last_login' => AdminFunctions::dateNow(),
                    'last_online' => strtotime("now"),
                    'ip_address' => $_SERVER['REMOTE_ADDR'],
                    'token' => $token,
                    'device_id' => isset($this->data['device_id']) ? $this->data['device_id'] : '',
                    'device_platform' => isset($this->data['device_platform']) ? $this->data['device_platform'] : 'Android'
                );

                if (!empty($res['token'])) {
                    unset($params['token']);
                    $token = $res['token'];
                }
                $db = new DbExt;
                if ($db->updateData("{{users}}", $params, 'id', $res['id'])) {
                    $this->code = 1;
                    $this->msg = self::t("Login Successful");
                    $this->estado = true;

                    $this->details = array(
                        'id' => $res['id'],
                        'email' => $this->data['email'],
                        'nombres' => $res['nombres'],
                        'descripcion' => $res['descripcion'],
                        'busca_trabajo' => $res['busca_trabajo'],
                        'password' => $res['password'],
                        'telefono' => $res['telefono'],
                        'remember' => isset($this->data['remember']) ? $this->data['remember'] : '',
                        'todays_date' => Yii::app()->functions->translateDate(date("M, d")),
                        'todays_date_raw' => date("Y-m-d"),
                        'token' => $token
                    );
                } else
                    $this->msg = self::t("Login failed. please try again later");
            } else
                $this->msg = self::t("Login failed. either username or password is incorrect");
        } else
            $this->msg = self::t("Please fill in your username and password");
        $this->output();
    }

    public function actionRegister() {
        if (!empty($this->data['email'])) {
            if (!AdminFunctions::getUserByEmail($this->data['email'])) {
                $token = md5(Driver::generateRandomNumber(5) . $this->data['email']);



                $params = array(
                    'last_login' => AdminFunctions::dateNow(),
                    'last_online' => strtotime("now"),
                    'ip_address' => $_SERVER['REMOTE_ADDR'],
                    'token' => $token,
                    'device_id' => isset($this->data['device_id']) ? $this->data['device_id'] : '',
                    'device_platform' => isset($this->data['device_platform']) ? $this->data['device_platform'] : 'Android'
                );

                $password = AdminFunctions::generateCode(10);
                $params['password'] = $password;
                $encryption_type = Yii::app()->params->encryption_type;
                if (empty($encryption_type)) {
                    $encryption_type = 'yii';
                }

                if ($encryption_type == "yii") {
                    $params['password'] = CPasswordHelper::hashPassword($params['password']);
                } else
                    $params['password'] = md5($params['password']);

                if (!empty($res['token'])) {
                    unset($params['token']);
                    $token = $res['token'];
                }
                $db = new DbExt;
                if ($db->insertData("{{users}}", $params)) {
                    $this->code = 1;
                    $this->msg = self::t("Registro exitoso, se le ha enviado un mail con su contraseña, por favor cámbiela");
                    $this->estado = true;
                    $res2 = Driver::driverBuscaUsuario($this->data['email']);
                    FrontFunctions::sendEmailWelcomeUsers($params);
                    $this->details = array(
                        'id' => $res2['id'],
                        'email' => $this->data['email'],
                        'password' => $res['password'],
                        'remember' => isset($this->data['remember']) ? $this->data['remember'] : '',
                        'todays_date' => Yii::app()->functions->translateDate(date("M, d")),
                        'todays_date_raw' => date("Y-m-d"),
                        'token' => $token
                    );
                } else
                    $this->msg = self::t("Problema al registrar datos");
            } else
                $this->msg = self::t("Email ya se encuentra registrado en el Sistema");
        } else
            $this->msg = self::t("Por favor ingrese un Email");
        $this->output();
    }

    public function actionForgotPassword() {
        if (empty($this->data['email'])) {
            $this->msg = self::t("Email address is required");
            $this->output();
            Yii::app()->end();
        }
        $db = new DbExt;
        if ($res = Driver::driverForgotPassword($this->data['email'])) {
            $driver_id = $res['driver_id'];
            $code = Driver::generateRandomNumber(5);
            $params = array('forgot_pass_code' => $code);
            if ($db->updateData('{{driver}}', $params, 'driver_id', $driver_id)) {
                $this->code = 1;
                $this->msg = self::t("We have send the a password change code to your email");

                $tpl = EmailTemplate::forgotPasswordRequest();
                $tpl = smarty('first_name', $res['first_name'], $tpl);
                $tpl = smarty('code', $code, $tpl);
                $subject = 'Forgot Password';
                if (sendEmail($res['email'], '', $subject, $tpl)) {
                    $this->details = "send email ok";
                } else
                    $this->msg = "send email failed";
            } else
                $this->msg = self::t("Algo ha fallado, por favor intente más tarde");
        } else
            $this->msg = self::t("Email address not found");
        $this->output();
    }

    public function actionChangePassword() {
        $Validator = new Validator;
        $req = array(
            'email' => self::t("Email es requerido"),
            'password' => self::t("Nuevo password es requerido")
        );
        $Validator->required($req, $this->data);
        if ($Validator->validate()) {
            if ($res = Driver::userForgotPassword($this->data['email'])) {
                if ($res['password'] == md5($this->data['passwordanterior'])) {
                    $params = array(
                        'password' => md5($this->data['password'])
                    );
                    $db = new DbExt;
                    if ($db->updateData("{{users}}", $params, 'id', $res['id'])) {
                        $this->code = 1;
                        $this->msg = self::t("Password cambiado correctamente");
                        $this->estado = true;
                    } else
                        $this->msg = self::t("Ha ocurrido un error, por favor intente de nuevo");
                } else
                    $this->msg = self::t("Password anterior no coincide");
            } else
                $this->msg = self::t("Email no encontrado");
        } else
            $this->msg = Driver::parseValidatorError($Validator->getError());
        $this->output();
    }

    public function actionGetOficiosUser() {
        if ($res = Driver::getOficiosByTokenIdUser($this->data['token'], $this->data['id'])) {
            $data = array();
            foreach ($res as $val) {
                $data[] = $val;
            }
            $this->code = 1;
            $this->msg = "OK";
            $this->details = $data;
            $this->estado = true;
        } else
            $this->msg = self::t("No hay data o el token no es válido");
        $this->output();
    }

    public function actionGetHorasAtencionUserAtencion() {
        if ($res = Driver::getHorasAtencionByTokenIdUserAtencion($this->data['token'], $this->data['id_user'])) {
            $data = array();
            foreach ($res as $val) {
                $data[] = $val;
            }
            $this->code = 1;
            $this->msg = "OK";
            $this->details = $data;
            $this->estado = true;
        } else
            $this->msg = self::t("No hay data o el token no es válido");
        $this->output();
    }

    public function actionGetHorasAtencionUser() {
        if ($res = Driver::getHorasAtencionByTokenIdUserAtencion($this->data['token'], $this->data['id_user'])) {
            $data = array();
            foreach ($res as $val) {
                $data[] = $val;
            }
            $this->code = 1;
            $this->msg = "OK";
            $this->details = $data;
            $this->estado = true;
        } else
            $this->msg = self::t("No hay data o el token no es válido");
        $this->output();
    }

    public function actionGetReviewsUser() {
        if (!$token = Driver::getUserByToken($this->data['token'])) {
            $this->msg = self::t("Token no válido");
            $this->output();
            Yii::app()->end();
        }
        if ($res = Driver::getReviewsIdUser($token['id'])) {

            $this->code = 1;
            $this->msg = "OK";
            $this->details = $res;
            $this->estado = true;
        } else
            $this->msg = self::t("No hay data o el token no es válido");
        $this->output();
    }

    public function actionGetReviewsListUser() {
        if (!$token = Driver::getUserByToken($this->data['token'])) {
            $this->msg = self::t("Token no válido");
            $this->output();
            Yii::app()->end();
        }
        if ($res = Driver::userReviewsListPaginated($token['id'], $this->data['limit'], $this->data['offset'], $this->data['orderby'])) {

            $data = array();
            foreach ($res as $val) {
                $data[] = $val;
            }
            $this->code = 1;
            $this->msg = "OK";
            $this->details = $data;
            $this->estado = true;
        } else
            $this->msg = self::t("No hay data");
        $this->output();
    }

    public function actionGetOfertasUser() {
        if (!$token = Driver::getUserByToken($this->data['token'])) {
            $this->msg = self::t("Token no válido");
            $this->output();
            Yii::app()->end();
        }

        if ($res = Driver::userOfertasUserListPaginated($token['id'], $this->data['limit'], $this->data['offset'], $this->data['orderby'])) {

            $data = array();
            foreach ($res as $val) {
                $data[] = $val;
            }
            $this->code = 1;
            $this->msg = "OK";
            $this->details = $data;
            $this->estado = true;
        } else
            $this->msg = self::t("No hay data");
        $this->output();
    }

    public function actionGetOfertasPublicadasByUser() {
        if (!$token = Driver::getUserByToken($this->data['token'])) {
            $this->msg = self::t("Token no válido");
            $this->output();
            Yii::app()->end();
        }

        if ($res = Driver::userOfertasUserPublicadasListPaginated($token['id'], $this->data['limit'], $this->data['offset'], $this->data['orderby'])) {

            $data = array();
            foreach ($res as $val) {
                $data[] = $val;
            }
            $this->code = 1;
            $this->msg = "OK";
            $this->details = $data;
            $this->estado = true;
        } else
            $this->msg = self::t("No hay data");
        $this->output();
    }

    public function actionGetHorasAtencionUserTodos() {
        if ($res = Driver::getHorasAtencionByTokenIdUserTodos($this->data['token'], $this->data['id_user'])) {
            $data = array();
            foreach ($res as $val) {
                $data[] = $val;
            }
            $this->code = 1;
            $this->msg = "OK";
            $this->details = $data;
            $this->estado = true;
        } else
            $this->msg = self::t("No hay data o el token no es válido");
        $this->output();
    }

    public function actionUpdateOficio() {
        $id = $this->data['id'];
        $params = array(
            'id_service' => $this->data['id_service'],
            'descripcion' => $this->data['descripcion'],
            'id_user' => $this->data['id_user'],
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'status' => 'published'
        );
        $db = new DbExt;
        if ($id !== 'null') {
            $params['date_modified'] = AdminFunctions::dateNow();

            if ($db->updateData("{{oficios}}", $params, 'id', $id)) {
                $this->code = 1;
                $this->msg = self::t("Registro guardado");
                $this->estado = true;
            } else
                $this->msg = self::t("Ha ocurrido un error, por favor intente de nuevo");
        } else {
            $params['date_created'] = AdminFunctions::dateNow();
            if ($db->insertData("{{oficios}}", $params)) {
                $this->code = 1;
                $this->msg = self::t("Registro guardado");
                $this->estado = true;
            } else
                $this->msg = self::t("Ha ocurrido un error, por favor intente de nuevo");
        }


        $this->output();
    }

    public function actionDeleteOficio() {

        Driver::deleteOficio($this->data['id']);
        $this->code = 1;
        $this->msg = self::t("Registro eliminado");
        $this->estado = true;


        $this->output();
    }

    public function actionRegistraHorasAtencionUser() {
        $id_user = $this->data['id'];
        $json = json_decode($this->data['horasatencion']);
        foreach ($json as $item) {
            $id = $item->id;
            $params = array(
                'hora_inicio' => $item->hora_inicio,
                'hora_fin' => $item->hora_fin,
                'atencion' => $item->atencion,
            );

            $db = new DbExt;

            if ($db->updateData("{{horas_atencion}}", $params, 'id', $id)) {
                $this->code = 1;
                $this->msg = self::t("Registro guardado");
                $this->estado = true;
            } else
                $this->msg = self::t("Ha ocurrido un error, por favor intente de nuevo");
        }
        $this->output();
    }

    public function actionRegistraOferta() {
        $id_user = $this->data['id_user'];
        $params = array(
            'solicito' => $this->data['solicito'],
            'descripcion' => $this->data['descripcion'],
            'sector' => $this->data['sector'],
            'ciudad' => $this->data['ciudad'],
            'direccion' => $this->data['direccion'],
            'estado' => $this->data['estado'],
            'date_created' => AdminFunctions::dateNow(),
            'date_modified' => AdminFunctions::dateNow(),
            'fecha_vigencia' => $this->data['fecha_vigencia'],
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'id_user' => $id_user
        );

        $db = new DbExt;

        if ($res2 = $db->insertDataDevuelveId("{{oferta_ocasional}}", $params)) {
            $this->code = 1;
            $this->msg = self::t("Registro guardado");
            $this->estado = true;
            $this->details = array(
                'id' => $res2,
                'solicito' => $this->data['solicito'],
                'id_user' => $id_user
            );
        } else
            $this->msg = self::t("Ha ocurrido un error, por favor intente de nuevo");
        $this->output();
    }

    public function actionAplicaOferta() {
        if (!$token = Driver::getUserByToken($this->data['token'])) {
            $this->msg = self::t("Token no válido");
            $this->output();
            Yii::app()->end();
        }
        if (!Driver::getOfertaAplicada($this->data['id_oferta'], $token['id'])) {
            $params = array(
                'id_oferta' => $this->data['id_oferta'],
                'id_user' => $token['id'],
                'lat' => $this->data['lat'],
                'lng' => $this->data['lng'],
                'date_created' => AdminFunctions::dateNow(),
                'ip_address' => $_SERVER['REMOTE_ADDR'],
            );

            $db = new DbExt;

            if ($db->insertData("{{aplica_oferta}}", $params)) {
                $this->code = 1;
                $this->msg = self::t("Oferta aplicada exitosamente");
                $this->estado = true;
            } else
                $this->msg = self::t("Ha ocurrido un error, por favor intente de nuevo");
        } else
            $this->msg = self::t("Ya has aplicado a la oferta");
        $this->output();
    }

    public function actionGetUserInfo() {
        if (!$token = Driver::getUserByToken($this->data['token'])) {
            $this->msg = self::t("Token no válido");
            $this->output();
            Yii::app()->end();
        }
        //$id = $token['id'];
//        $profile_photo = '';
//        if (!empty($info['profile_photo'])) {
//            $profile_photo_path = Driver::driverUploadPath() . "/" . $info['profile_photo'];
//            if (file_exists($profile_photo_path)) {
//                $profile_photo = websiteUrl() . "/upload/photo/" . $info['profile_photo'];
//            }
//        }

        $this->code = 1;
        $this->msg = "OK";
        $this->details = array(
            'id' => $token['id'],
            'nombres' => $token['nombres'],
            'email' => $token['email'],
            'descripcion' => $token['descripcion'],
            'busca_trabajo' => $token['busca_trabajo'],
            'telefono' => $token['telefono'],
        );
        $this->estado = true;
        $this->output();
    }

    public function actionGetOfertabyId() {
        if (!$token = Driver::getOfertabyId($this->data['id'])) {
            $this->msg = self::t("No existe la oferta");
            $this->output();
            Yii::app()->end();
        }
        //$id = $token['id'];
//        $profile_photo = '';
//        if (!empty($info['profile_photo'])) {
//            $profile_photo_path = Driver::driverUploadPath() . "/" . $info['profile_photo'];
//            if (file_exists($profile_photo_path)) {
//                $profile_photo = websiteUrl() . "/upload/photo/" . $info['profile_photo'];
//            }
//        }

        $this->code = 1;
        $this->msg = "OK";
        $this->details = array(
            'id' => $token['id'],
            'solicito' => $token['solicito'],
            'descripcion' => $token['descripcion'],
            'fecha_vigencia' => $token['fecha_vigencia'],
            'direccion' => $token['direccion'],
            'sector' => $token['sector'],
            'ciudad' => $token['ciudad']
        );
        $this->output();
    }

    public function actionGetTransport() {
        $this->code = 1;
        $this->code = 1;
        $this->details = Driver::transportType();
        $this->output();
    }

    public function actionUpdateUserInfo() {
        if (!$token = Driver::getUserByTokenandId($this->data['token'], $this->data['id'])) {
            $this->msg = self::t("Token no válido");
            $this->output();
            Yii::app()->end();
        }
        $own = false;
        if ($res = Driver::getUserByTokenandEmail($this->data['token'], $this->data['email'])) {
            $own = true;
        }
        if ($own == false) {
            if ($res = Driver::userForgotPassword($this->data['email'])) {
                $this->msg = self::t("El email ya existe en otro usuario en la aplicación");
                $this->output();
                Yii::app()->end();
            }
        }
        $id = $token['id'];

        $Validator = new Validator;
        $req = array(
            'telefono' => self::t("Teléfono es requerido")
        );
        $Validator->required($req, $this->data);
        if ($Validator->validate()) {
            $token = md5(Driver::generateRandomNumber(5) . $this->data['email']);

            $params = array(
                'telefono' => $this->data['telefono'],
                'nombres' => $this->data['nombres'],
                'descripcion' => $this->data['descripcion'],
                'busca_trabajo' => $this->data['busca_trabajo'],
                'email' => $this->data['email'],
                'token' => $token,
                //'date_modified' => AdminFunctions::dateNow(),
                'ip_address' => $_SERVER['REMOTE_ADDR']
            );
            $db = new DbExt;
            if ($db->updateData("{{users}}", $params, 'id', $id)) {
                $this->code = 1;
                $this->msg = self::t("Usuario actualizado exitosamente");
                $this->estado = true;

                $this->details = array(
                    'id' => $id,
                    'password' => $this->data['password'],
                    'telefono' => $this->data['telefono'],
                    'nombres' => $this->data['nombres'],
                    'descripcion' => $this->data['descripcion'],
                    'busca_trabajo' => $this->data['busca_trabajo'],
                    'email' => $this->data['email'],
                    'token' => $token,
                    //'date_modified' => AdminFunctions::dateNow(),
                    'ip_address' => $_SERVER['REMOTE_ADDR']
                );
                ;
            } else
                $this->msg = self::t("Algo ha fallado, por favor intente más tarde");
        } else
            $this->msg = Driver::parseValidatorError($Validator->getError());
        $this->output();
    }

    public function actionProfileChangePassword() {
        if (!$token = Driver::getDriverByToken($this->data['token'])) {
            $this->msg = self::t("Token no válido");
            $this->output();
            Yii::app()->end();
        }
        $driver_id = $token['driver_id'];

        Driver::setCustomerTimezone($token['customer_id']);

        $Validator = new Validator;
        $req = array(
            'current_pass' => self::t("Current password is required"),
            'new_pass' => self::t("New password is required"),
            'confirm_pass' => self::t("Confirm password is required")
        );
        if ($this->data['new_pass'] != $this->data['confirm_pass']) {
            $Validator->msg[] = self::t("Confirm password does not macth with your new password");
        }

        $Validator->required($req, $this->data);
        if ($Validator->validate()) {

            if (!Driver::driverAppLogin($token['username'], $this->data['current_pass'])) {
                $this->msg = self::t("Current password is invalid");
                $this->output();
                Yii::app()->end();
            }
            $params = array(
                'password' => md5($this->data['new_pass']),
                'date_modified' => AdminFunctions::dateNow(),
                'ip_address' => $_SERVER['REMOTE_ADDR']
            );
            $db = new DbExt;
            if ($db->updateData("{{driver}}", $params, 'driver_id', $driver_id)) {
                $this->code = 1;
                $this->msg = self::t("Password Successfully Changed");
                $this->details = $this->data['new_pass'];
            } else
                $this->msg = self::t("Algo ha fallado, por favor intente más tarde");
        } else
            $this->msg = Driver::parseValidatorError($Validator->getError());
        $this->output();
    }

    public function actionSettingPush() {
        if (!$token = Driver::getDriverByToken($this->data['token'])) {
            $this->msg = self::t("Token no válido");
            $this->output();
            Yii::app()->end();
        }
        $driver_id = $token['driver_id'];

        Driver::setCustomerTimezone($token['customer_id']);

        $params = array(
            'enabled_push' => $this->data['enabled_push'],
            'date_modified' => AdminFunctions::dateNow(),
            'ip_address' => $_SERVER['REMOTE_ADDR']
        );
        $db = new DbExt;
        if ($db->updateData("{{driver}}", $params, 'driver_id', $driver_id)) {
            $this->code = 1;
            $this->msg = self::t("Setting Saved");

            $this->details = array(
                'enabled_push' => $params['enabled_push']
            );
        } else
            $this->msg = self::t("Algo ha fallado, por favor intente más tarde");
        $this->output();
    }

    public function actionGetDashboardUser() {
        if (!$token = Driver::getUserByToken($this->data['token'])) {
            $this->msg = self::t("Token no válido");
            $this->output();
            Yii::app()->end();
        }


        if ($res = Driver::getGetDashboardUser($token['id'])) {

            $this->code = 1;
            $this->estado = true;
            $this->msg = "OK";
            $this->details = array(
                'id' => $res['id'],
                'id_user' => $res['id_user'],
                'cantidad_ofertas_perfil' => $res['cantidad_ofertas_perfil'],
                'cantidad_ofertas_aplicadas' => $res['cantidad_ofertas_aplicadas'],
                'cantidad_ofertas_publicadas' => $res['cantidad_ofertas_publicadas'],
                'cantidad_ofertas_trabajadas' => $res['cantidad_ofertas_trabajadas'],
            );
        } else
            $this->msg = self::t("No hay data");
        $this->output();
    }

    public function actionGetSettings() {
        if (!$token = Driver::getDriverByToken($this->data['token'])) {
            $this->msg = self::t("Token no válido");
            $this->output();
            Yii::app()->end();
        }
        $driver_id = $token['driver_id'];

        Driver::setCustomerTimezone($token['customer_id']);

        $lang = Driver::availableLanguages();
        $lang = '';

        $resp = array(
            'enabled_push' => $token['enabled_push'],
            'language' => $lang
        );
        $this->code = 1;
        $this->msg = "OK";
        $this->details = $resp;
        $this->output();
    }

    public function actionLanguageList() {
        $final_list = '';
        $lang = getOptionA('language_list');
        if (!empty($lang)) {
            $lang = json_decode($lang, true);
        }
        if (is_array($lang) && count($lang) >= 1) {
            foreach ($lang as $lng) {
                $final_list[$lng] = $lng;
            }
            $this->code = 1;
            $this->msg = "OK";
        } else
            $this->msg = t("No language");
        $this->details = $final_list;
        $this->output();
    }

    public function actionGetAppSettings() {

        $translation = Driver::getMobileTranslation();
        $this->code = 1;
        $this->msg = "OK";
        $this->details = array(
            'notification_sound_url' => Driver::moduleUrl() . "/sound/food_song.mp3",
            'app_default_lang' => getOptionA('app_default_lang'),
            'app_force_lang' => getOptionA('app_force_lang'),
            'map_provider' => getOptionA('map_provider'),
            'mapbox_access_token' => getOptionA('mapbox_access_token'),
            'translation' => $translation
        );
        $this->output();
    }

    public function actionGetNotifications() {
        if (!$token = Driver::getDriverByToken($this->data['token'])) {
            $this->msg = self::t("Token no válido");
            $this->output();
            Yii::app()->end();
        }
        $driver_id = $token['driver_id'];
        Driver::setCustomerTimezone($token['customer_id']);

        if ($res = Driver::getDriverNotifications($driver_id)) {
            $data = array();
            foreach ($res as $val) {
                $val['date_created'] = Driver::prettyDate($val['date_created']);
                //$val['date_created']=date("h:i:s",strtotime($val['date_created']));
                $val['push_title'] = Driver::t($val['push_title']);
                $data[] = $val;
            }
            $this->code = 1;
            $this->msg = "OK";
            $this->details = $data;
        } else
            $this->msg = self::t("No notifications");
        $this->output();
    }

    public function actionGetServiciosActivos() {


        if ($res = Driver::servicesList(0, 'published')) {
            $data = array();
            foreach ($res as $val) {
                $data[] = $val;
            }
            $this->code = 1;
            $this->msg = "OK";
            $this->details = $data;
            $this->estado = true;
        } else
            $this->msg = self::t("No hay data");
        $this->output();
    }

    public function actionGetUltimas5OfertasActivas() {


        if ($res = Driver::ofertasList()) {
            $data = array();
            foreach ($res as $val) {
                $data[] = $val;
            }
            $this->code = 1;
            $this->msg = "OK";
            $this->details = $data;
            $this->estado = true;
        } else
            $this->msg = self::t("No hay data");
        $this->output();
    }

    public function actionGetOfertasActivasPaginated() {

        if (!$token = Driver::getUserByToken($this->data['token'])) {
            $this->msg = self::t("Token no válido");
            $this->output();
            Yii::app()->end();
        }
        if ($res = Driver::userOfertasListPaginated($this->data['solicito'], $this->data['limit'], $this->data['offset'], $this->data['orderby'])) {

            $data = array();
            foreach ($res as $val) {
                $data[] = $val;
            }
            $this->code = 1;
            $this->msg = "OK";
            $this->details = $data;
            $this->estado = true;
        } else
            $this->msg = self::t("No hay data");
        $this->output();
    }

    public function actionGetPerfilesFiltroId() {

        if (!$token = Driver::getUserByToken($this->data['token'])) {
            $this->msg = self::t("Token no válido");
            $this->output();
            Yii::app()->end();
        }
        if ($res = Driver::userListPaginated($this->data['service_id'], $this->data['limit'], $this->data['offset'], $this->data['orderby'])) {

            $data = array();
            foreach ($res as $val) {
                $data[] = $val;
            }
            $this->code = 1;
            $this->msg = "OK";
            $this->details = $data;
            $this->estado = true;
        } else
            $this->msg = self::t("No hay data");
        $this->output();
    }

    public function actionGetUsersAplicantes() {

        if (!$token = Driver::getUserByToken($this->data['token'])) {
            $this->msg = self::t("Token no válido");
            $this->output();
            Yii::app()->end();
        }
        if ($res = Driver::userAplicantesListPaginated($this->data['id'], $this->data['limit'], $this->data['offset'], $this->data['orderby'])) {

            $data = array();
            foreach ($res as $val) {
                $data[] = $val;
            }
            $this->code = 1;
            $this->msg = "OK";
            $this->details = $data;
            $this->estado = true;
        } else
            $this->msg = self::t("No hay data");
        $this->output();
    }

    public function actionLogout() {
        if (!$token = Driver::getDriverByToken($this->data['token'])) {
            $this->msg = self::t("Token no válido");
            $this->output();
            Yii::app()->end();
        }

        Driver::setCustomerTimezone($token['customer_id']);

        $driver_id = $token['driver_id'];

        $tracking_type = Driver::getTrackingOptions($token['customer_id']);
        if ($tracking_type == 2) {
            $last_online = strtotime("-35 minutes");
        } else
            $last_online = strtotime("-20 minutes");

        $params = array(
            'last_online' => $last_online,
            'on_duty' => 2,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'is_online' => 2
        );

        $db = new DbExt;
        $db->updateData('{{driver}}', $params, 'driver_id', $driver_id);
        $this->code = 1;
        $this->msg = "OK";
        unset($db);
        $this->output();
    }

    public function actionUploadTaskPhoto() {

        $this->data = $_REQUEST;
        $request = json_encode($_REQUEST);

        if (!$token = Driver::getDriverByToken($this->data['token'])) {
            $this->msg = self::t("Token no válido");
            echo "$this->code|$this->msg|$this->details|" . $request;
            Yii::app()->end();
        }

        $driver_id = $token['driver_id'];
        $driver_name = $token['first_name'] . " " . $token['last_name'];

        Driver::setCustomerTimezone($token['customer_id']);

        if ($res = Driver::getTaskId($this->data['task_id'])) {

            $task_id = $res['task_id'];

            $path_to_upload = Driver::driverUploadPath();

            if (isset($_FILES['file'])) {

                header('Access-Control-Allow-Origin: *');

                $new_image_name = urldecode($_FILES["file"]["name"]) . ".jpg";
                $new_image_name = str_replace(array('?', ':'), '', $new_image_name);

                if (@move_uploaded_file($_FILES["file"]["tmp_name"], "$path_to_upload/" . $new_image_name)) {
                    $params = array(
                        'status' => "photo",
                        'remarks' => Driver::driverStatusPretty($driver_name, "photo"),
                        'task_id' => $task_id,
                        'driver_id' => $driver_id,
                        'driver_location_lat' => isset($token['location_lat']) ? $token['location_lat'] : '',
                        'driver_location_lng' => isset($token['location_lng']) ? $token['location_lng'] : '',
                        'reason' => '',
                        'date_created' => AdminFunctions::dateNow(),
                        'ip_address' => $_SERVER['REMOTE_ADDR'],
                        'photo_name' => $new_image_name
                    );

                    $db = new DbExt;
                    if ($db->insertData("{{task_history}}", $params)) {
                        $this->code = 1;
                        $this->msg = self::t("Upload successful");
                        $this->details = $task_id;

                        $photo_link = websiteUrl() . "/upload/photo/" . $new_image_name;

                        $task_info = $res;
                        $task_info['photo_link'] = $photo_link;

                        if ($task_info['trans_type'] == "delivery") {
                            Driver::sendCustomerNotification('DELIVERY_PHOTO', $task_info);
                        } else
                            Driver::sendCustomerNotification('PICKUP_PHOTO', $task_info);
                    } else
                        $this->msg = self::t("failed cannot insert record");
                } else
                    $this->msg = self::t("Cannot upload photo");
            } else
                $this->msg = self::t("Image is missing");
        } else
            $this->msg = self::t("Task not found");

        echo "$this->code|$this->msg|$this->details|" . $request;
    }

    public function actionGetTaskPhoto() {

        if (!$token = Driver::getDriverByToken($this->data['token'])) {
            $this->msg = self::t("Token no válido");
            $this->output();
            Yii::app()->end();
        }

        $driver_id = $token['driver_id'];
        Driver::setCustomerTimezone($token['customer_id']);

        if ($res = Driver::getTaskId($this->data['task_id'])) {
            if ($photos = Driver::getTaskPhoto($this->data['task_id'])) {
                $this->code = 1;
                $this->msg = $res['status'];
                $this->details = $photos;
            } else
                $this->msg = self::t("No photo to show");
        } else
            $this->msg = self::t("Task not found");

        $this->output();
    }

    public function actionUploadProfile() {
        $this->data = $_REQUEST;

        $request = json_encode($_REQUEST);

        if (!isset($this->data['token'])) {
            $this->data['token'] = '';
        }
        if (!$token = Driver::getDriverByToken($this->data['token'])) {
            $this->msg = self::t("token not found");
            echo "$this->code|$this->msg||" . $request;
            Yii::app()->end();
        }

        $driver_id = $token['driver_id'];
        Driver::setCustomerTimezone($token['customer_id']);

        $path_to_upload = Driver::driverUploadPath();
        if (!file_exists($path_to_upload)) {
            if (!@mkdir($path_to_upload, 0777)) {
                $this->msg = Driver::t("Error has occured cannot create upload directory");
                $this->jsonResponse();
            }
        }

        $profile_photo = '';

        if (isset($_FILES['file'])) {

            header('Access-Control-Allow-Origin: *');

            $new_image_name = urldecode($_FILES["file"]["name"]) . ".jpg";
            $new_image_name = str_replace(array('?', ':'), '', $new_image_name);

            @move_uploaded_file($_FILES["file"]["tmp_name"], "$path_to_upload/" . $new_image_name);

            $db = new DbExt;
            $params = array(
                'profile_photo' => $new_image_name,
                'date_modified' => AdminFunctions::dateNow()
            );
            if ($db->updateData("{{driver}}", $params, 'driver_id', $driver_id)) {
                $this->code = 1;
                $this->msg = t("Upload successful");
                $this->details = $new_image_name;
                $profile_photo = websiteUrl() . "/upload/photo/" . $new_image_name;
            } else
                $this->msg = self::t("Error cannot update");
        } else
            $this->msg = self::t("Image is missing");

        echo "$this->code|$this->msg|$profile_photo|" . $request;
    }

    public function actionDeletePhoto() {

        if (!$token = Driver::getDriverByToken($this->data['token'])) {
            $this->msg = self::t("Token no válido");
            $this->output();
            Yii::app()->end();
        }

        $driver_id = $token['driver_id'];

        if (isset($this->data['id'])) {
            if ($res = Driver::getTaskId($this->data['task_id'])) {
                if ($data = Driver::getTasHistoryByID($this->data['id'])) {
                    $file = Driver::driverUploadPath() . "/" . $data['photo_name'];
                    if (file_exists($file)) {
                        @unlink($file);
                    }
                    Driver::deleteSignature($this->data['id']);
                    $this->code = 1;
                    $this->msg = "OK";
                    $this->details = $this->data['task_id'];
                } else
                    $this->msg = self::t("Task not found");
            } else
                $this->msg = self::t("Task not found");
        } else
            $this->msg = self::t("missing parameters");

        $this->output();
    }

    public function actionreRegisterDevice() {
        $new_device_id = isset($this->data['new_device_id']) ? $this->data['new_device_id'] : '';
        if (empty($new_device_id)) {
            $this->msg = $this->t("New device id is empty");
            $this->output();
        }

        if (!$token = Driver::getDriverByToken($this->data['token'])) {
            $this->msg = self::t("Token no válido");
            $this->output();
            Yii::app()->end();
        }

        $driver_id = $token['driver_id'];

        $db = new DbExt();

        $params = array(
            'device_id' => $new_device_id,
            'device_platform' => isset($this->data['device_platform']) ? $this->data['device_platform'] : '',
            'app_version' => isset($this->data['app_version']) ? $this->data['app_version'] : '',
        );
        if ($db->updateData("{{driver}}", $params, 'driver_id', $driver_id)) {
            $this->code = 1;
            $this->msg = "OK";
            $this->details = $new_device_id;
        } else
            $this->msg = "Failed cannot update";
        $this->output();
    }

}

/*end class*/