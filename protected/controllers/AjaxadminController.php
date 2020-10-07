<?php

if (!isset($_SESSION)) {
    session_start();
}

class AjaxadminController extends CController {

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

    public function beforeAction($action) {
        $action = Yii::app()->controller->action->id;
        $continue = true;

        $action = strtolower($action);
        
        if ($action == "login" || $action == "adminforgotpassword" || $action == "changepassword") {
            $continue = false;
        }

        if ($continue) {
            if (!AdminFunctions::islogin()) {
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
        
        if ($res = AdminFunctions::Login($this->data['username'], $this->data['password'])) {
            unset($res['username']);
            unset($res['password']);
            
            $_SESSION['kartero_admin'] = $res;
            $this->code = 1;
            $this->msg = t("Login successful");
        } else
            $this->msg = t("Login failed. either username or password is incorrect");
        $this->jsonResponse();
    }

   

    public function actionremRecords() {
        if (isset($this->data['tbl']) && isset($this->data['field']) && isset($this->data['value'])) {
            $stmt = "DELETE 
    		FROM  {{" . $this->data['tbl'] . "}}
    		WHERE " . $this->data['field'] . "=" . Driver::q($this->data['value']) . "
    		";
            $this->db->qry($stmt);
            $this->code = 1;
            $this->msg = t("Records deleted");
            $this->details = array(
                'table' => $this->data['tbl'],
                'redirect' => Yii::app()->createUrl('admin/' . $this->data['tbl'])
            );
        } else
            $this->msg = t("missing parameters");
        $this->jsonResponse();
    }

    public function actioncustomerList() {
        $aColumns = array(
            'customer_id', 'first_name', 'mobile_number', 'email_address', 'plan_id', 'token', 'status', 'customer_id'
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

        $stmt = "
		SELECT SQL_CALC_FOUND_ROWS a.*,
		(
		  select plan_name
		  from
		  {{plan}}
		  where plan_id=a.plan_id
		) as plan_name 
		FROM {{customer}} a
		WHERE 1
		$and		
		$sWhere
		$sOrder
		$sLimit
		";

        if (isset($_GET['debug'])) {
            dump($stmt);
        }

        if ($res = $this->db->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $this->db->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {

                $stats = $val['status'];
                $status = "<div class=\"tag $stats\">" . t($val['status']) . "</div>";
                $status .= AdminFunctions::prettyDate($val['date_created']);

                $actions = '';
                if ($val['status'] == "pending" && $val['needs_approval'] == 1) {
                    $actions .= AdminFunctions::generateActionsApproved($val['customer_id']);
                }

                $actions .= AdminFunctions::generateActionsListCustomer('customer_id', $val['customer_id'], 'customer', 'customer-new');

                $feed_data['aaData'][] = array(
                    $val['customer_id'],
                    $val['first_name'] . " " . $val['last_name'],
                    $val['mobile_number'],
                    $val['email_address'],
                    $val['plan_name'],
                    $val['token'],
                    $status,
                    $actions
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actionaddCustomer() {
        $params = $this->data;

        if (!isset($this->data['id'])) {
            if (empty($this->data['password'])) {
                $this->msg = Driver::t("Password is required");
                $this->jsonResponse();
                Yii::app()->end();
            }
            if (AdminFunctions::getCustomerByEmail($this->data['email_address'])) {
                $this->msg = Driver::t("Email address already exist");
                $this->jsonResponse();
                Yii::app()->end();
            }
        } else {
            if ($res = AdminFunctions::getCustomerByEmail($this->data['email_address'])) {
                if ($res['customer_id'] != $this->data['id']) {
                    $this->msg = Driver::t("Email address already exist");
                    $this->jsonResponse();
                    Yii::app()->end();
                }
            }
        }

        unset($params['action']);
        unset($params['id']);
        unset($params['msg']);
        unset($params['plan_expiration_submit']);

        $params['date_created'] = AdminFunctions::dateNow();
        $params['ip_address'] = $_SERVER['REMOTE_ADDR'];

        if (!isset($params['with_sms'])) {
            $params['with_sms'] = '';
        }

        if (!isset($params['with_broadcast'])) {
            $params['with_broadcast'] = 2;
        }

        if (isset($params['password'])) {
            if (!empty($params['password'])) {

                $encryption_type = Yii::app()->params->encryption_type;
                if (empty($encryption_type)) {
                    $encryption_type = 'yii';
                }

                if ($encryption_type == "yii") {
                    $params['password'] = CPasswordHelper::hashPassword($params['password']);
                } else
                    $params['password'] = md5($params['password']);
            } else
                unset($params['password']);
        }

        $params['plan_expiration'] = $this->data['plan_expiration_submit'];

        if (isset($this->data['id'])) {
            unset($params['date_created']);
            $params['date_modified'] = AdminFunctions::dateNow();
            if ($this->db->updateData("{{customer}}", $params, 'customer_id', $this->data['id'])) {
                $this->msg = t("Successfully updated");
                $this->code = 1;
            } else
                $this->msg = t("Something went wrong cannot update records");
        } else {

            $token = md5(AdminFunctions::generateCode(10));
            $verification_code = AdminFunctions::generateNumericCode(5);
            $params['token'] = $token;
            $params['verification_code'] = $verification_code;

            $params['plan_price'] = FrontFunctions::getPlansPrice($this->data['plan_id']);
            $params['plan_currency_code'] = FrontFunctions::getCurrenyCode(true);

            if ($this->db->insertData("{{customer}}", $params)) {
                $last_id = Yii::app()->db->getLastInsertID();
                $this->code = 1;
                $this->msg = t("Successful");
                $this->details = Yii::app()->createUrl('admin/customer-new', array('id' => $last_id, 'msg' => $this->msg)
                );
            } else
                $this->msg = t("Something went wrong cannot insert records");
        }
        $this->jsonResponse();
    }

    public function actiongeneralSettings() {

        updateOptionAdmin('company_name', isset($this->data['company_name']) ? $this->data['company_name'] : '' );
        updateOptionAdmin('contact_number', isset($this->data['contact_number']) ? $this->data['contact_number'] : '' );
        updateOptionAdmin('company_address', isset($this->data['company_address']) ? $this->data['company_address'] : '' );
        updateOptionAdmin('email_address', isset($this->data['email_address']) ? $this->data['email_address'] : '' );
        updateOptionAdmin('email_provider', isset($this->data['email_provider']) ? $this->data['email_provider'] : '' );
        updateOptionAdmin('global_sender', isset($this->data['global_sender']) ? $this->data['global_sender'] : '' );
        //updateOptionAdmin('google_api_key',isset($this->data['google_api_key'])?$this->data['google_api_key']:'' );
        //updateOptionAdmin('push_api_key',isset($this->data['push_api_key'])?$this->data['push_api_key']:'' );    	
        updateOptionAdmin('website_default_country', isset($this->data['website_default_country']) ? $this->data['website_default_country'] : '' );

        updateOptionAdmin('smtp_host', isset($this->data['smtp_host']) ? $this->data['smtp_host'] : '' );
        updateOptionAdmin('smtp_port', isset($this->data['smtp_port']) ? $this->data['smtp_port'] : '' );
        updateOptionAdmin('smtp_username', isset($this->data['smtp_username']) ? $this->data['smtp_username'] : '' );

        updateOptionAdmin('smtp_password', isset($this->data['smtp_password']) ? $this->data['smtp_password'] : '' );
        updateOptionAdmin('follow_fb', isset($this->data['follow_fb']) ? $this->data['follow_fb'] : '' );
        updateOptionAdmin('follow_google', isset($this->data['follow_google']) ? $this->data['follow_google'] : '' );
        updateOptionAdmin('follow_twitter', isset($this->data['follow_twitter']) ? $this->data['follow_twitter'] : '' );
        updateOptionAdmin('signup_verification_enabled', isset($this->data['signup_verification_enabled']) ? $this->data['signup_verification_enabled'] : '' );
        updateOptionAdmin('signup_verification', isset($this->data['signup_verification']) ? $this->data['signup_verification'] : '' );
        updateOptionAdmin('website_currency', isset($this->data['website_currency']) ? $this->data['website_currency'] : '' );
        updateOptionAdmin('currency_position', isset($this->data['currency_position']) ? $this->data['currency_position'] : '' );
        updateOptionAdmin('currency_decimal_places', isset($this->data['currency_decimal_places']) ? $this->data['currency_decimal_places'] : '' );
        updateOptionAdmin('currency_thousand_sep', isset($this->data['currency_thousand_sep']) ? $this->data['currency_thousand_sep'] : '' );
        updateOptionAdmin('currency_decimal_sep', isset($this->data['currency_decimal_sep']) ? $this->data['currency_decimal_sep'] : '' );

        updateOptionAdmin('website_timezone', isset($this->data['website_timezone']) ? $this->data['website_timezone'] : '' );

        updateOptionAdmin('website_custom_footer', isset($this->data['website_custom_footer']) ? $this->data['website_custom_footer'] : '' );

        updateOptionAdmin('signup_needs_approval', isset($this->data['signup_needs_approval']) ? $this->data['signup_needs_approval'] : '' );
        updateOptionAdmin('currency_space', isset($this->data['currency_space']) ? $this->data['currency_space'] : '' );

        updateOptionAdmin('enabled_promo_codes', isset($this->data['enabled_promo_codes']) ? $this->data['enabled_promo_codes'] : '' );
        updateOptionAdmin('display_promo_codes', isset($this->data['display_promo_codes']) ? $this->data['display_promo_codes'] : '' );


        updateOptionAdmin('smtp_secure', isset($this->data['smtp_secure']) ? $this->data['smtp_secure'] : '' );

        $this->code = 1;
        $this->msg = t("Settings saved");
        $this->jsonResponse();
    }

    public function actionsmsSettings() {

        updateOptionAdmin('sms_provier', isset($this->data['sms_provier']) ? $this->data['sms_provier'] : '' );
        updateOptionAdmin('twilio_sender_id', isset($this->data['twilio_sender_id']) ? $this->data['twilio_sender_id'] : '' );
        updateOptionAdmin('twilio_sid', isset($this->data['twilio_sid']) ? $this->data['twilio_sid'] : '' );
        updateOptionAdmin('twilio_token', isset($this->data['twilio_token']) ? $this->data['twilio_token'] : '' );
        updateOptionAdmin('nexmo_sender', isset($this->data['nexmo_sender']) ? $this->data['nexmo_sender'] : '' );
        updateOptionAdmin('nexmo_key', isset($this->data['nexmo_key']) ? $this->data['nexmo_key'] : '' );
        updateOptionAdmin('nexmo_secret', isset($this->data['nexmo_secret']) ? $this->data['nexmo_secret'] : '' );
        updateOptionAdmin('nexmo_curl', isset($this->data['nexmo_curl']) ? $this->data['nexmo_curl'] : '' );
        updateOptionAdmin('nexmo_unicode', isset($this->data['nexmo_unicode']) ? $this->data['nexmo_unicode'] : '' );

        updateOptionAdmin('sms_gateway_username', isset($this->data['sms_gateway_username']) ? $this->data['sms_gateway_username'] : '' );
        updateOptionAdmin('sms_gateway_password', isset($this->data['sms_gateway_password']) ? $this->data['sms_gateway_password'] : '' );
        updateOptionAdmin('sms_gateway_sender', isset($this->data['sms_gateway_sender']) ? $this->data['sms_gateway_sender'] : '' );
        updateOptionAdmin('sms_user_curl', isset($this->data['sms_user_curl']) ? $this->data['sms_user_curl'] : '' );

        $this->code = 1;
        $this->msg = t("Settings saved");
        $this->jsonResponse();
    }

    public function actionpaymentSettings() {

        updateOptionAdmin('paypal_enabled', isset($this->data['paypal_enabled']) ? $this->data['paypal_enabled'] : '' );
        updateOptionAdmin('paypal_mode', isset($this->data['paypal_mode']) ? $this->data['paypal_mode'] : '' );
        updateOptionAdmin('paypal_sandbox_user', isset($this->data['paypal_sandbox_user']) ? $this->data['paypal_sandbox_user'] : '' );

        updateOptionAdmin('paypal_sandbox_password', isset($this->data['paypal_sandbox_password']) ? $this->data['paypal_sandbox_password'] : '' );

        updateOptionAdmin('paypal_sandbox_signature', isset($this->data['paypal_sandbox_signature']) ? $this->data['paypal_sandbox_signature'] : '' );

        updateOptionAdmin('paypal_live_user', isset($this->data['paypal_live_user']) ? $this->data['paypal_live_user'] : '' );
        updateOptionAdmin('paypal_live_password', isset($this->data['paypal_live_password']) ? $this->data['paypal_live_password'] : '' );

        updateOptionAdmin('paypal_live_signature', isset($this->data['paypal_live_signature']) ? $this->data['paypal_live_signature'] : '' );

        $this->code = 1;
        $this->msg = t("Settings saved");
        $this->jsonResponse();
    }

    public function actionStripeSettings() {

        updateOptionAdmin('stripe_enabled', isset($this->data['stripe_enabled']) ? $this->data['stripe_enabled'] : '' );
        updateOptionAdmin('stripe_mode', isset($this->data['stripe_mode']) ? $this->data['stripe_mode'] : '' );

        updateOptionAdmin('stripe_sandbox_secret_key', isset($this->data['stripe_sandbox_secret_key']) ? $this->data['stripe_sandbox_secret_key'] : '' );
        updateOptionAdmin('stripe_sandbox_publish_key', isset($this->data['stripe_sandbox_publish_key']) ? $this->data['stripe_sandbox_publish_key'] : '' );

        updateOptionAdmin('stripe_live_secret_key', isset($this->data['stripe_live_secret_key']) ? $this->data['stripe_live_secret_key'] : '' );
        updateOptionAdmin('stripe_live_publish_key', isset($this->data['stripe_live_publish_key']) ? $this->data['stripe_live_publish_key'] : '' );

        $this->code = 1;
        $this->msg = t("Settings saved");
        $this->jsonResponse();
    }

    public function actionuploadFile() {
        $upload_dir = AdminFunctions::uploadPath();
        $uploader = new FileUpload('uploadfile');
        $ext = $uploader->getExtension(); // Get the extension of the uploaded file
        $prefix = isset($_GET['prefix']) ? $_GET['prefix'] : '';
        $uploader->newFileName = $prefix . "-" . AdminFunctions::generateCode(20) . "." . $ext;
        // Handle the upload
        $result = $uploader->handleUpload($upload_dir);
        if (!$result) {
            exit(json_encode(array('success' => 2, 'msg' => $uploader->getErrorMsg())));
        }

        $logo = '<img class="responsive-img" src="' . AdminFunctions::uploadURL() . "/" . $uploader->newFileName . '">';
        echo json_encode(array('success' => 1, 'logo' => $logo));
        updateOptionAdmin($prefix, $uploader->newFileName);
    }
    
       public function actionuploadFotoServicio()
    {
    	$upload_dir = AdminFunctions::uploadServiciosPath();
        $uploader = new FileUpload('uploadfile');
        $ext = $uploader->getExtension(); // Get the extension of the uploaded file
        $prefix=isset($_GET['prefix'])?$_GET['prefix']:'';
        $uploader->newFileName = $prefix."-".AdminFunctions::generateCode(20).".".$ext;
        // Handle the upload
        $result = $uploader->handleUpload($upload_dir);
	    if (!$result) {
	        exit(json_encode(array('success' => 2, 'msg' => $uploader->getErrorMsg())));  
	    }
	    $logo='<img class="responsive-img" src="'.AdminFunctions::uploadServiciosURL()."/".$uploader->newFileName.'">';
        echo json_encode(array('success' => 1 ,'logo'=>$logo ,'foto'=>AdminFunctions::uploadServiciosURL()."/".$uploader->newFileName));
    }

    public function actionuploadCertificateFile() {
        $upload_dir = AdminFunctions::uploadCertificatePath();
        $uploader = new FileUpload('uploadfile');
        $ext = $uploader->getExtension(); // Get the extension of the uploaded file
        $prefix = isset($_GET['prefix']) ? $_GET['prefix'] : '';
        $uploader->newFileName = $_GET['uploadfile'];
        // Handle the upload
        $result = $uploader->handleUpload($upload_dir);
        if (!$result) {
            exit(json_encode(array('success' => 2, 'msg' => $uploader->getErrorMsg())));
        }

        echo json_encode(array('success' => 1, 'filename' => $uploader->newFileName));
        updateOptionAdmin($prefix, $uploader->newFileName);
    }

    public function actionupdateProfile() {
        $encryption_type = Yii::app()->params->encryption_type;
        if (empty($encryption_type)) {
            $encryption_type = 'yii';
        }

        $params = $this->data;
        $params['date_modified'] = AdminFunctions::dateNow();
        unset($params['action']);
        unset($params['cpassword']);
        //dump($params);
        if (isset($params['password'])) {
            if (empty($params['password'])) {
                unset($params['password']);
            } else {

                if ($this->data['password'] != $this->data['cpassword']) {
                    $this->msg = t("Confirm does not match");
                    $this->jsonResponse();
                    Yii::app()->end();
                }

                if ($encryption_type == "yii") {
                    $params['password'] = CPasswordHelper::hashPassword($params['password']);
                } else
                    $params['password'] = md5($params['password']);
            }
        }

        $id = AdminFunctions::getAdminID();
        if ($this->db->updateData("{{admin}}", $params, 'admin_id', $id)) {
            $this->msg = t("Profile updated");
            $this->code = 1;
        } else
            $this->msg = t("Something went wrong cannot update records");
        $this->jsonResponse();
    }

    public function actionnewSignup() {
        $aColumns = array(
            'customer_id', 'first_name', 'mobile_number', 'email_address', 'plan_id',
            'company_name',
            'verification_code',
            'status', 'customer_id'
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

        $stmt = "
		SELECT SQL_CALC_FOUND_ROWS a.*,
		(
		  select plan_name
		  from
		  {{plan}}
		  where plan_id=a.plan_id
		) as plan_name 
		FROM {{customer}} a
		WHERE date_created LIKE '" . date("Y-m-d") . "%'
		$and		
		$sWhere
		$sOrder
		$sLimit
		";

        if (isset($_GET['debug'])) {
            dump($stmt);
        }

        if ($res = $this->db->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $this->db->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {

                $stats = $val['status'];
                $status = "<div class=\"tag $stats\">" . t($val['status']) . "</div>";
                $status .= AdminFunctions::prettyDate($val['date_created']);

                $actions = '';
                if ($val['status'] == "pending" && $val['needs_approval'] == 1) {
                    $actions .= AdminFunctions::generateActionsApproved($val['customer_id']);
                }

                $actions .= AdminFunctions::generateActionsList('customer_id', $val['customer_id'], 'customer', 'customer-new');

                $feed_data['aaData'][] = array(
                    $val['customer_id'],
                    $val['first_name'] . " " . $val['last_name'],
                    $val['mobile_number'],
                    $val['email_address'],
                    $val['plan_name'],
                    $val['company_name'],
                    $val['verification_code'],
                    $status,
                    $actions
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actionsignupCharts() {
        $start = date('Y-m-d', strtotime("-30 day"));
        $end = date("Y-m-d", strtotime("+1 day"));
        if ($res = AdminFunctions::getTotalSignup($start, $end)) {
            
        } else
            $res = array();

        ob_start();
        $this->renderPartial('/admin/chart-signup', array(
            'data' => $res
        ));
        $charts = ob_get_contents();
        ob_end_clean();
        $this->code = 1;
        $this->details = $charts;
        $this->msg = "OK";
        $this->jsonResponse();
    }

    public function actionsaveTemplates() {

        updateOptionAdmin('welcome_tpl', isset($this->data['welcome_tpl']) ? $this->data['welcome_tpl'] : '' );
        //updateOptionAdmin('welcome_user_tpl', isset($this->data['welcome_user_tpl']) ? $this->data['welcome_user_tpl'] : '' );
        updateOptionAdmin('forgot_password_tpl', isset($this->data['forgot_password_tpl']) ? $this->data['forgot_password_tpl'] : '' );
        updateOptionAdmin('signup_tpl_sms', isset($this->data['signup_tpl_sms']) ? $this->data['signup_tpl_sms'] : '' );
        updateOptionAdmin('signup_tpl_email', isset($this->data['signup_tpl_email']) ? $this->data['signup_tpl_email'] : '' );
        updateOptionAdmin('signup_tpl_email_subject', isset($this->data['signup_tpl_email_subject']) ? $this->data['signup_tpl_email_subject'] : '' );
        updateOptionAdmin('forgot_password_subject', isset($this->data['forgot_password_subject']) ? $this->data['forgot_password_subject'] : '' );
        updateOptionAdmin('welcome_tpl_subject', isset($this->data['welcome_tpl_subject']) ? $this->data['welcome_tpl_subject'] : '' );

        updateOptionAdmin('approved_tpl_subject', isset($this->data['approved_tpl_subject']) ? $this->data['approved_tpl_subject'] : '' );

        updateOptionAdmin('approved_tpl', isset($this->data['approved_tpl']) ? $this->data['approved_tpl'] : '' );

        $this->code = 1;
        $this->msg = t("Settings saved");
        $this->jsonResponse();
    }

    public function actioncurrencyList() {
        $aColumns = array(
            'curr_id', 'currency_code', 'currency_symbol', 'status', 'date_created'
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

        $stmt = "
		SELECT SQL_CALC_FOUND_ROWS * 
		FROM {{currency}} 
		WHERE 1
		$and		
		$sWhere
		$sOrder
		$sLimit
		";

        if (isset($_GET['debug'])) {
            dump($stmt);
        }

        if ($res = $this->db->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $this->db->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {

                $stats = $val['status'];
                $status = "<div class=\"tag $stats\">" . t($val['status']) . "</div>";
                $status .= AdminFunctions::prettyDate($val['date_created']);
                $actions = AdminFunctions::generateActionsList('curr_id', $val['curr_id'], 'currency', 'currency-new');

                $feed_data['aaData'][] = array(
                    $val['curr_id'],
                    $val['currency_code'],
                    $val['currency_symbol'],
                    $status,
                    $actions
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actionaddCurrency() {

        $params = $this->data;
        unset($params['action']);
        unset($params['id']);
        unset($params['msg']);
        $params['date_created'] = AdminFunctions::dateNow();
        $params['ip_address'] = $_SERVER['REMOTE_ADDR'];

        if (isset($this->data['id'])) {
            unset($params['date_created']);
            $params['date_modified'] = AdminFunctions::dateNow();
            if ($this->db->updateData("{{currency}}", $params, 'curr_id', $this->data['id'])) {
                $this->msg = t("Successfully updated");
                $this->code = 1;
            } else
                $this->msg = t("Something went wrong cannot update records");
        } else {
            if ($this->db->insertData("{{currency}}", $params)) {
                $last_id = Yii::app()->db->getLastInsertID();
                $this->code = 1;
                $this->msg = t("Successful");
                $this->details = Yii::app()->createUrl('admin/currency-new', array('id' => $last_id, 'msg' => $this->msg)
                );
            } else
                $this->msg = t("Something went wrong cannot insert records");
        }
        $this->jsonResponse();
    }

    public function actionapprovedCustomer() {
        if (isset($this->data['customer_id'])) {
            if ($res = AdminFunctions::getCustomerByID($this->data['customer_id'])) {
                $params = array(
                    'status' => 'active',
                    'needs_approval' => 2,
                    'date_modified' => AdminFunctions::dateNow()
                );
                if ($this->db->updateData("{{customer}}", $params, 'customer_id', $this->data['customer_id'])) {
                    $this->msg = t("Successfully updated");
                    $this->code = 1;
                    AdminFunctions::sendApprovedEmail($res);
                } else
                    $this->msg = t("Something went wrong cannot update records");
            } else
                $this->msg = t("Records not found");
        } else
            $this->msg = t("Customer id is missing");
        $this->jsonResponse();
    }

    public function actionRemoveLogo() {
        $stmt = "DELETE FROM
    	{{option}}
    	WHERE
    	option_name='website_logo'
    	";
        $this->db->qry($stmt);
        $this->code = 1;
        $this->msg = t("Successful");
        $this->details = '';
        $this->jsonResponse();
    }

    public function actionsaveMobileSettings() {
        //dump($this->data);
        updateOptionAdmin('push_api_key', isset($this->data['push_api_key']) ? $this->data['push_api_key'] : '' );
        updateOptionAdmin('ios_mode', isset($this->data['ios_mode']) ? $this->data['ios_mode'] : '' );
        updateOptionAdmin('ios_password', isset($this->data['ios_password']) ? $this->data['ios_password'] : '' );
        updateOptionAdmin('mobile_api_key', isset($this->data['mobile_api_key']) ? $this->data['mobile_api_key'] : '' );
        updateOptionAdmin('fcm_server_key', isset($this->data['fcm_server_key']) ? $this->data['fcm_server_key'] : '' );

        $this->code = 1;
        $this->msg = t("Settings saved");
        $this->jsonResponse();
    }

    public function actionrptCustomerList() {
        $aColumns = array(
            'customer_id', 'first_name', 'mobile_number', 'email_address', 'plan_id', 'status', 'customer_id'
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

        if (!empty($this->data['start_date_submit']) && !empty($this->data['end_date_submit'])) {
            $and = " AND date_created BETWEEN " . Driver::q($this->data['start_date_submit']) . " AND
			" . Driver::q($this->data['end_date_submit']) . "
			 ";
        }

        $stmt = "
		SELECT SQL_CALC_FOUND_ROWS a.*,
		(
		  select plan_name
		  from
		  {{plan}}
		  where plan_id=a.plan_id
		) as plan_name 
		FROM {{customer}} a
		WHERE 1
		$and		
		$sWhere
		$sOrder
		$sLimit
		";

        if (isset($_GET['debug'])) {
            dump($stmt);
        }

        $_SESSION['kt_export_stmt'] = $stmt;

        if ($res = $this->db->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $this->db->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {

                $stats = $val['status'];
                $status = "<div class=\"tag $stats\">" . $val['status'] . "</div>";

                $feed_data['aaData'][] = array(
                    $val['customer_id'],
                    $val['first_name'] . " " . $val['last_name'],
                    $val['mobile_number'],
                    $val['email_address'],
                    $val['plan_name'],
                    $status,
                    AdminFunctions::prettyDate($val['date_created'])
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actionrptSales() {
        $aColumns = array(
            'date_created', 'transaction_type', 'payment_provider',
            'memo', 'total_paid', 'transaction_ref'
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

        if (!empty($this->data['start_date_submit']) && !empty($this->data['end_date_submit'])) {
            $and = " AND date_created BETWEEN " . Driver::q($this->data['start_date_submit']) . " AND
			" . Driver::q($this->data['end_date_submit']) . "
			 ";
        }

        $stmt = "
		SELECT SQL_CALC_FOUND_ROWS *
		FROM {{payment_logs}} 
		WHERE 1
		$and		
		$sWhere
		$sOrder
		$sLimit
		";

        if (isset($_GET['debug'])) {
            dump($stmt);
        }

        $_SESSION['kt_export_stmt'] = $stmt;

        if ($res = $this->db->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $this->db->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {

                $feed_data['aaData'][] = array(
                    AdminFunctions::prettyDate($val['date_created']),
                    t($val['transaction_type']),
                    AdminFunctions::prettyGateway($val['payment_provider']),
                    $val['memo'],
                    prettyPrice($val['total_paid']),
                    $val['transaction_ref'],
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actionrptSMS() {
        $aColumns = array(
            'date_created', 'to_number', 'sms_text', 'provider', 'msg'
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

        if (!empty($this->data['start_date_submit']) && !empty($this->data['end_date_submit'])) {
            $and = " AND date_created BETWEEN " . Driver::q($this->data['start_date_submit']) . " AND
			" . Driver::q($this->data['end_date_submit']) . "
			 ";
        }

        $stmt = "
		SELECT SQL_CALC_FOUND_ROWS *
		FROM {{sms_logs}} 
		WHERE 1
		$and		
		$sWhere
		$sOrder
		$sLimit
		";

        if (isset($_GET['debug'])) {
            dump($stmt);
        }

        $_SESSION['kt_export_stmt'] = $stmt;

        if ($res = $this->db->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $this->db->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {

                $feed_data['aaData'][] = array(
                    AdminFunctions::prettyDate($val['date_created']),
                    $val['to_number'],
                    $val['sms_text'],
                    $val['provider'],
                    $val['msg'],
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actionrptEmail() {

        $aColumns = array(
            'date_created', 'email_address', 'subject', 'content', 'status'
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

        if (!empty($this->data['start_date_submit']) && !empty($this->data['end_date_submit'])) {
            $and = " AND date_created BETWEEN " . Driver::q($this->data['start_date_submit']) . " AND
			" . Driver::q($this->data['end_date_submit']) . "
			 ";
        }

        $stmt = "
		SELECT SQL_CALC_FOUND_ROWS *
		FROM {{email_logs}} 
		WHERE 1
		$and		
		$sWhere
		$sOrder
		$sLimit
		";

        if (isset($_GET['debug'])) {
            dump($stmt);
        }

        $_SESSION['kt_export_stmt'] = $stmt;

        if ($res = $this->db->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $this->db->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {

                $feed_data['aaData'][] = array(
                    AdminFunctions::prettyDate($val['date_created']),
                    $val['email_address'],
                    $val['subject'],
                    $val['content'],
                    '<span class="tag ' . $val['status'] . '">' . t($val['status']) . '</span>',
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actionrptPush() {

        $aColumns = array(
            'date_created', 'device_platform', 'device_id', 'push_title', 'push_message', 'push_type', 'status'
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

        if (!empty($this->data['start_date_submit']) && !empty($this->data['end_date_submit'])) {
            $and = " AND date_created BETWEEN " . Driver::q($this->data['start_date_submit']) . " AND
			" . Driver::q($this->data['end_date_submit']) . "
			 ";
        }

        $stmt = "
		SELECT SQL_CALC_FOUND_ROWS *
		FROM {{driver_pushlog}} 
		WHERE 1
		$and		
		$sWhere
		$sOrder
		$sLimit
		";

        if (isset($_GET['debug'])) {
            dump($stmt);
        }

        $_SESSION['kt_export_stmt'] = $stmt;

        if ($res = $this->db->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $this->db->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {

                $feed_data['aaData'][] = array(
                    AdminFunctions::prettyDate($val['date_created']),
                    $val['device_platform'],
                    '<span class="truncate">' . $val['device_id'] . '</span>',
                    $val['push_title'],
                    $val['push_message'],
                    $val['push_type'],
                    '<span class="tag ' . $val['status'] . '">' . t($val['status']) . '</span>',
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actioncustomPageList() {
        $aColumns = array(
            'page_id', 'title', 'status', 'date_created', 'page_id'
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

        $stmt = "
		SELECT SQL_CALC_FOUND_ROWS a.*
		FROM {{page}} a
		WHERE 1
		$and		
		$sWhere
		$sOrder
		$sLimit
		";

        if (isset($_GET['debug'])) {
            dump($stmt);
        }

        if ($res = $this->db->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $this->db->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {

                $stats = $val['status'];
                $status = "<div class=\"tag $stats\">" . t($val['status']) . "</div>";

                $actions = '';


                $actions = AdminFunctions::generateActionsList('page_id', $val['page_id'], 'page', 'custompage-new');

                $feed_data['aaData'][] = array(
                    $val['page_id'],
                    $val['slug'],
                    $val['title'],
                    $status,
                    AdminFunctions::prettyDate($val['date_created']),
                    $actions
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actionaddCustomePage() {
        $params = $this->data;
        unset($params['action']);
        unset($params['msg']);
        unset($params['id']);
        unset($params['text_editor_value']);

        //$params['content']=$this->data['text_editor_value'];
        $params['content'] = $this->data['content'];
        $params['date_created'] = AdminFunctions::dateNow();

        $slug = AdminFunctions::seoFriendlyURL($this->data['title']);
        //customePageCount  	
        $params['slug'] = AdminFunctions::getPageSlug($slug);
        /* dump("->".$params['slug']);
          die(); */
        $params['ip_address'] = $_SERVER['REMOTE_ADDR'];

        if (isset($this->data['id'])) {
            unset($params['date_created']);
            unset($params['slug']);
            $params['date_modified'] = AdminFunctions::dateNow();
            if ($this->db->updateData("{{page}}", $params, 'page_id', $this->data['id'])) {
                $this->msg = t("Successfully updated");
                $this->code = 1;
            } else
                $this->msg = t("Something went wrong cannot update records");
        } else {
            if ($this->db->insertData("{{page}}", $params)) {
                $last_id = Yii::app()->db->getLastInsertID();
                $this->code = 1;
                $this->msg = t("Successful");
                $this->details = Yii::app()->createUrl('admin/custompage-new', array('id' => $last_id, 'msg' => $this->msg)
                );
            } else
                $this->msg = t("Something went wrong cannot insert records");
        }
        $this->jsonResponse();
    }

    public function actionsaveLanguage() {
        updateOptionAdmin('language_list', isset($this->data['lang']) ? json_encode($this->data['lang']) : '' );
        updateOptionAdmin('default_lang', isset($this->data['default_lang']) ? $this->data['default_lang'] : '' );


        updateOptionAdmin('app_default_lang', isset($this->data['app_default_lang']) ? $this->data['app_default_lang'] : '' );
        updateOptionAdmin('app_force_lang', isset($this->data['app_force_lang']) ? $this->data['app_force_lang'] : '' );

        $this->code = 1;
        $this->msg = t("Settings saved");
        $this->jsonResponse();
    }

    public function actionsaveAssignPage() {
        $this->db->qry("UPDATE {{page}} SET active='2' ");
        if (is_array($this->data['page_id']) && count($this->data['page_id']) >= 1) {
            foreach ($this->data['page_id'] as $key => $val) {
                $params = array(
                    'active' => 1,
                    'assign_to' => $this->data['assign_to'][$key]
                );
                $this->db->updateData("{{page}}", $params, 'page_id', $key);
            }
        }
        $this->code = 1;
        $this->msg = t("Settings saved");
        $this->jsonResponse();
    }

    public function actionMercadopagoSettings() {
        updateOptionAdmin('mcd_enabled', isset($this->data['mcd_enabled']) ? $this->data['mcd_enabled'] : '' );
        updateOptionAdmin('mcd_mode', isset($this->data['mcd_mode']) ? $this->data['mcd_mode'] : '' );
        updateOptionAdmin('mcd_client_id', isset($this->data['mcd_client_id']) ? trim($this->data['mcd_client_id']) : '' );
        updateOptionAdmin('mcd_secret', isset($this->data['mcd_secret']) ? trim($this->data['mcd_secret']) : '' );

        $this->code = 1;
        $this->msg = t("Settings saved");
        $this->jsonResponse();
    }

    public function actionRazorSettings() {
        updateOptionAdmin('rzr_enabled', isset($this->data['rzr_enabled']) ? $this->data['rzr_enabled'] : '' );
        updateOptionAdmin('rzr_mode', isset($this->data['rzr_mode']) ? $this->data['rzr_mode'] : '' );
        updateOptionAdmin('rzr_key_id', isset($this->data['rzr_key_id']) ? trim($this->data['rzr_key_id']) : '' );
        updateOptionAdmin('rzr_secret', isset($this->data['rzr_secret']) ? trim($this->data['rzr_secret']) : '' );

        $this->code = 1;
        $this->msg = t("Settings saved");
        $this->jsonResponse();
    }

    public function actionAuthorizeSettings() {
        updateOptionAdmin('atz_enabled', isset($this->data['atz_enabled']) ? $this->data['atz_enabled'] : '' );
        updateOptionAdmin('atz_mode', isset($this->data['atz_mode']) ? $this->data['atz_mode'] : '' );
        updateOptionAdmin('atz_login_id', isset($this->data['atz_login_id']) ? trim($this->data['atz_login_id']) : '' );
        updateOptionAdmin('atz_transaction_key', isset($this->data['atz_transaction_key']) ? trim($this->data['atz_transaction_key']) : '' );

        $this->code = 1;
        $this->msg = t("Settings saved");
        $this->jsonResponse();
    }

    public function actionadminforgotpassword() {
        if ($res = AdminFunctions::getAdminByUsername($this->data['username'])) {
            if (!empty($res['email_address'])) {
                $link = websiteUrl() . "/admin/forgotpass/?token=" . urlencode($res['password']);
                $tpl = EmailTemplate::adminForgotPassword();
                $tpl = smarty('username', $res['username'], $tpl);
                $tpl = smarty('link', $link, $tpl);
                $global_sender = getOptionA('global_sender');
                if (empty($global_sender)) {
                    $global_sender = "noreply@" . $_SERVER['SERVER_NAME'];
                }
                $subject = t("Forgot password");
                sendEmail($res['email_address'], $global_sender, $subject, $tpl);
                $this->code = 1;
                $this->msg = t("We have sent you email including your password change link");
            } else
                $this->msg = t("Email address is empty");
        } else
            $this->msg = t("Username doest not exist");
        $this->jsonResponse();
    }

    public function actionChangePassword() {
        if ($this->data['password'] == $this->data['cpassword']) {
            $token = isset($this->data['token']) ? $this->data['token'] : '';
            if ($res = AdminFunctions::getAdminByPassword($token)) {
                $admin_id = $res['admin_id'];
                $encryption_type = Yii::app()->params->encryption_type;
                if (empty($encryption_type)) {
                    $encryption_type = 'yii';
                }

                if ($encryption_type == "yii") {
                    $new_password = CPasswordHelper::hashPassword($this->data['password']);
                } else
                    $new_password = md5($this->data['password']);

                $params = array(
                    'password' => $new_password,
                    'date_modified' => AdminFunctions::dateNow(),
                    'ip_address' => $_SERVER['REMOTE_ADDR']
                );

                $db = new DbExt;
                if ($db->updateData("{{admin}}", $params, 'admin_id', $admin_id)) {
                    $this->code = 1;
                    $this->msg = t("Password successsfully change");
                } else
                    $this->msg = t("Password cannot be updated this time please try again");
            } else
                $this->msg = t("Token is not valid");
        } else
            $this->msg = t("Password does not match with the confirm password");
        $this->jsonResponse();
    }

    public function actionserviceslist() {
        $aColumns = array(
            'services_id', 'sevices_name', 'status', 'services_id'
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

        $stmt = "
		SELECT SQL_CALC_FOUND_ROWS * 
		FROM {{services}} 
		WHERE services_parent_id='0'
		$and		
		$sWhere
		$sOrder
		$sLimit
		";

        if (isset($_GET['debug'])) {
            dump($stmt);
        }

        if ($res = $this->db->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $this->db->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {

                $stats = $val['status'];
                $status = "<div class=\"tag $stats\">" . t($val['status']) . "</div>";
                $status .= AdminFunctions::prettyDate($val['date_created']);
                $actions = AdminFunctions::generateActionsList('services_id', $val['services_id'], 'services', 'services-new');

                $feed_data['aaData'][] = array(
                    $val['services_id'],
                    $val['sevices_name'],
                    $status,
                    $actions
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actionaddServices() {
        $params = $this->data;
        unset($params['action']);
        unset($params['id']);
        unset($params['msg']);
        $params['date_created'] = AdminFunctions::dateNow();
        $params['ip_address'] = $_SERVER['REMOTE_ADDR'];
        

        if (isset($this->data['id'])) {
            unset($params['date_created']);
            $params['date_modified'] = AdminFunctions::dateNow();
            if ($this->db->updateData("{{services}}", $params, 'services_id', $this->data['id'])) {
                $this->msg = t("Successfully updated");
                $this->code = 1;
            } else
                $this->msg = t("Something went wrong cannot update records");
        } else {
            if ($this->db->insertData("{{services}}", $params)) {
                $last_id = Yii::app()->db->getLastInsertID();
                $this->code = 1;
                $this->msg = t("Successful");
                $this->details = Yii::app()->createUrl('admin/services-new', array('id' => $last_id, 'msg' => $this->msg)
                );
            } else
                $this->msg = t("Something went wrong cannot insert records");
        }
        $this->jsonResponse();
    }

    public function actionSortServicesParent() {
        $db = new DbExt;
        if (isset($this->data['ids'])) {
            $this->data['ids'] = substr($this->data['ids'], 0, -1);
            $id = explode(",", $this->data['ids']);
            foreach ($id as $key => $val) {
                $db->updateData("{{services}}", array(
                    'sequence' => $key,
                    'date_modified' => AdminFunctions::dateNow()
                        ), 'services_id', $val);
            }
            $this->code = 1;
            $this->msg = t("Successfully updated");
        } else
            $this->msg = t("Missing id");
        $this->jsonResponse();
    }

    public function actionSeoSettings() {
        updateOptionAdmin('home_seo_title', isset($this->data['home_seo_title']) ? $this->data['home_seo_title'] : '' );
        updateOptionAdmin('home_seo_meta', isset($this->data['home_seo_meta']) ? $this->data['home_seo_meta'] : '' );
        updateOptionAdmin('price_seo_title', isset($this->data['price_seo_title']) ? $this->data['price_seo_title'] : '' );
        updateOptionAdmin('price_seo_meta', isset($this->data['price_seo_meta']) ? $this->data['price_seo_meta'] : '' );

        $this->code = 1;
        $this->msg = t("Settings saved");
        $this->jsonResponse();
    }

    public function actionPromoCodeList() {
        $aColumns = array(
            'promo_code_id',
            'promo_code_name',
            'discoun_type',
            'discount',
            'expiration',
            'status',
            'promo_code_id'
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

        $stmt = "
		SELECT SQL_CALC_FOUND_ROWS * 
		FROM {{promo_code}} 
		WHERE 1
		$and		
		$sWhere
		$sOrder
		$sLimit
		";

        if (isset($_GET['debug'])) {
            dump($stmt);
        }

        if ($res = $this->db->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $this->db->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {

                $stats = $val['status'];
                $status = "<div class=\"tag $stats\">" . t($val['status']) . "</div>";
                $status .= AdminFunctions::prettyDate($val['date_created']);
                $actions = AdminFunctions::generateActionsList('promo_code_id', $val['promo_code_id'], 'promo_code', 'promocode-new');

                $discount = $val['discount'];
                if ($val['discount_type'] == "percentage") {
                    $discount = $val['discount'] . "%";
                }

                $feed_data['aaData'][] = array(
                    $val['promo_code_id'],
                    $val['promo_code_name'],
                    $val['discount_type'],
                    $discount,
                    prettyDate($val['expiration'], false),
                    $status,
                    $actions
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actionAddPromoCode() {

        $params = $this->data;
        $params['expiration'] = $this->data['expiration_submit'];
        unset($params['action']);
        unset($params['id']);
        unset($params['msg']);
        unset($params['expiration_submit']);
        $params['date_created'] = AdminFunctions::dateNow();
        $params['ip_address'] = $_SERVER['REMOTE_ADDR'];


        if (isset($this->data['id'])) {
            unset($params['date_created']);
            if (!AdminFunctions::isPromoCodeExist($this->data['promo_code_name'], $this->data['id'])) {
                $params['date_modified'] = AdminFunctions::dateNow();
                if ($this->db->updateData("{{promo_code}}", $params, 'promo_code_id', $this->data['id'])) {
                    $this->msg = t("Successfully updated");
                    $this->code = 1;
                } else
                    $this->msg = t("Something went wrong cannot update records");
            } else
                $this->msg = t("Promo code name already exist");
        } else {
            if (!AdminFunctions::isPromoCodeExist($this->data['promo_code_name'])) {
                if ($this->db->insertData("{{promo_code}}", $params)) {
                    $last_id = Yii::app()->db->getLastInsertID();
                    $this->code = 1;
                    $this->msg = t("Successful");
                    $this->details = Yii::app()->createUrl('admin/promocode-new', array('id' => $last_id, 'msg' => $this->msg)
                    );
                } else
                    $this->msg = t("Something went wrong cannot insert records");
            } else
                $this->msg = t("Promo code name already exist");
        }
        $this->jsonResponse();
    }

    public function actionsendTestEmail() {
        if (!empty($this->data['email_address'])) {
            if (sendEmail($this->data['email_address'], '', "Test Email", "This is a test email")) {
                $this->code = 1;
                $this->msg = t("Successful");
            } else
                $this->msg = t("Failed sending email");
        } else
            $this->msg = t("Email address is required");
        $this->jsonResponse();
    }

    public function actionsendTestSMS() {
        $message = t("This is a test message");
        if ($res = Yii::app()->functions->sendSMS($this->data['phone_number'], $message)) {
            if ($res['msg'] == "process") {
                $this->code = 1;
                $this->msg = t("Process");
            } else
                $this->msg = $res['msg'];
        } else
            $this->msg = t("failed sending sms");
        $this->jsonResponse();
    }

    public function actionsaveMapSettings() {
        updateOptionAdmin('map_provider', isset($this->data['map_provider']) ? $this->data['map_provider'] : '' );
        updateOptionAdmin('google_api_key', isset($this->data['google_api_key']) ? $this->data['google_api_key'] : '' );
        updateOptionAdmin('mapbox_access_token', isset($this->data['mapbox_access_token']) ? $this->data['mapbox_access_token'] : '' );
        updateOptionAdmin('map_use_curl', isset($this->data['map_use_curl']) ? $this->data['map_use_curl'] : '' );

        updateOptionAdmin('map_default_country', isset($this->data['map_default_country']) ? $this->data['map_default_country'] : '' );

        $country_list = require_once('CountryCode.php');
        if (array_key_exists($this->data['map_default_country'], (array) $country_list)) {
            $country_name = $country_list[$this->data['map_default_country']];
        } else
            $country_name = $this->data['map_default_country'];

        if ($res = Driver::addressToLatLong($country_name)) {
            updateOptionAdmin('map_default_lat', $res['lat']);
            updateOptionAdmin('map_default_lng', $res['long']);
        }

        $this->code = 1;
        $this->msg = t("Settings saved");
        $this->jsonResponse();
    }

    public function actionapiLogs() {
        $aColumns = array(
            'id',
            'map_provider',
            'api_functions',
            'api_response',
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

        $and = '';

        $stmt = "
		SELECT SQL_CALC_FOUND_ROWS a.*
		FROM {{api_logs}} a
		WHERE 1
		$and		
		$sWhere
		$sOrder
		$sLimit
		";

        if (isset($_GET['debug'])) {
            dump($stmt);
        }

        if ($res = $this->db->rst($stmt)) {

            $iTotalRecords = 0;
            $stmtc = "SELECT FOUND_ROWS() as total_records";
            if ($resc = $this->db->rst($stmtc)) {
                $iTotalRecords = $resc[0]['total_records'];
            }

            $feed_data['sEcho'] = intval($_GET['sEcho']);
            $feed_data['iTotalRecords'] = $iTotalRecords;
            $feed_data['iTotalDisplayRecords'] = $iTotalRecords;

            foreach ($res as $val) {


                $feed_data['aaData'][] = array(
                    $val['id'],
                    $val['map_provider'],
                    $val['api_functions'],
                    "<span class=\"truncate-text\">" . $val['api_response'] . "</span> <a href=\"#no\" class=\"read_more\">" . Driver::t("view more") . "</a> ",
                    AdminFunctions::prettyDate($val['date_created']),
                );
            }
            if (isset($_GET['debug'])) {
                dump($feed_data);
            }
            $this->otableOutput($feed_data);
        }
        $this->otableNodata();
    }

    public function actiongenerateServicesKey() {
        $code = md5(AdminFunctions::generateCode(20));
        $this->code = 1;
        $this->msg = "OK";
        $this->details = $code;
        $this->jsonResponse();
    }

    public function actionapiServicesSettings() {
        updateOptionAdmin('api_services_key', isset($this->data['api_services_key']) ? $this->data['api_services_key'] : '' );

        $this->code = 1;
        $this->msg = t("Settings saved");
        $this->jsonResponse();
    }

    public function actionPaystackSettings() {
        updateOptionAdmin('psk_enabled', isset($this->data['psk_enabled']) ? trim($this->data['psk_enabled']) : '' );
        updateOptionAdmin('psk_mode', isset($this->data['psk_mode']) ? trim($this->data['psk_mode']) : '' );
        updateOptionAdmin('psk_sandbox_secret_key', isset($this->data['psk_sandbox_secret_key']) ? trim($this->data['psk_sandbox_secret_key']) : '' );
        updateOptionAdmin('psk_live_secret_key', isset($this->data['psk_live_secret_key']) ? trim($this->data['psk_live_secret_key']) : '' );

        $this->code = 1;
        $this->msg = t("Settings saved");
        $this->jsonResponse();
    }

}

/*end class*/