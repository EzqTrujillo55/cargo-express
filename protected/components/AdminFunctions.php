<?php

class AdminFunctions {

    public static function jsLang() {
        return array(
            'delete_confirm' => self::t("This will delete the records permanently are you sure you want to continue"),
            'uploading' => self::t("Uploading"),
            'upload_logo' => self::t("Upload Logo"),
            'unabled_to_upload_file' => self::t("Unable to upload file"),
            'success_uploaded' => self::t("successfully uploaded"),
            'an_error_occured' => self::t("An error occurred and the upload failed"),
            'invalid_file_extension' => self::t("Invalid file extension")
        );
    }

    public static function t($message = '') {
        return Yii::t("default", $message);
    }

    public static function assetsUrl() {
        return Yii::app()->baseUrl . '/assets';
    }

    public static function q($data) {
        return Yii::app()->db->quoteValue($data);
    }

    public static function customerStatus() {
        return array(
            'active' => Yii::t("default", 'active'),
            'pending' => Yii::t("default", 'pending for approval'),
            'suspended' => Yii::t("default", 'suspended'),
            'blocked' => Yii::t("default", 'blocked'),
            'expired' => Yii::t("default", 'expired')
        );
    }

    public static function statusList() {
        return array(
            'published' => Yii::t("default", 'published'),
            'pending' => Yii::t("default", 'pending for approval'),
            'draft' => Yii::t("default", 'draft'),
        );
    }

    public static function parseValidatorError($error = '') {
        $error_string = '';
        if (is_array($error) && count($error) >= 1) {
            foreach ($error as $val) {
                $error_string .= "$val\n";
            }
        }
        return $error_string;
    }

    public static function Login($username = '', $password = '') {
        $encryption_type = Yii::app()->params->encryption_type;
        if (empty($encryption_type)) {
            $encryption_type = 'yii';
        }

        $db = new DbExt;
        $stmt = "
		SELECT * FROM
		{{admin}}
		WHERE
		username=" . self::q($username) . "		
		LIMIT 0,1
		";
        if ($res = $db->rst($stmt)) {
            $data = $res[0];
            $hash = $data['password'];
            if ($encryption_type == "yii") {
                if (CPasswordHelper::verifyPassword($password, $hash)) {
                    return $data;
                }
            } else {
                if (md5($password) == $hash) {
                    return $data;
                }
            }
        }
        return false;
    }

    public static function getAdminDetailsByID($admin_id = '') {
        $db = new DbExt;
        $stmt = "
		SELECT * FROM
		{{admin}}
		WHERE
		admin_id=" . self::q($admin_id) . "		
		LIMIT 0,1
		";
        if ($res = $db->rst($stmt)) {
            return $res[0];
        }
        return false;
    }

    public static function islogin() {
        if (isset($_SESSION['kartero_admin'])) {
            if (is_numeric($_SESSION['kartero_admin']['admin_id'])) {
                return true;
            }
        }
        return false;
    }

    public static function getAdminID() {
        if (isset($_SESSION['kartero_admin'])) {
            if (is_numeric($_SESSION['kartero_admin']['admin_id'])) {
                return $_SESSION['kartero_admin']['admin_id'];
            }
        }
        return false;
    }

    public static function getAdminUsername() {
        if (isset($_SESSION['kartero_admin'])) {
            if (is_numeric($_SESSION['kartero_admin']['admin_id'])) {
                return $_SESSION['kartero_admin']['first_name'];
            }
        }
        return false;
    }

    public static function getRecordsFromTable($tablename = '', $where_field = '', $where_value = '') {
        $db = new DbExt;
        $stmt = "
		SELECT * FROM
		{{{$tablename}}}
		WHERE
		$where_field=" . self::q($where_value) . "		
		LIMIT 0,1
		";
        if ($res = $db->rst($stmt)) {
            return $res[0];
        }
        return false;
    }

    public static function prettyPrice($price = '', $currency_code = '') {
        if (!is_numeric($price)) {
            return false;
        }
        $curr = '';
        $curr_id = getOptionA('website_currency');
        if ($res_cur = AdminFunctions::getCurrencyByID($curr_id)) {
            $curr = $res_cur['currency_symbol'];
        }

        if (!empty($currency_code)) {
            $curr = $currency_code;
        }

        $spacer = '';
        $currency_position = getOptionA('currency_position');
        $currency_decimal_places = getOptionA('currency_decimal_places');
        $currency_thousand_sep = getOptionA('currency_thousand_sep');
        $currency_decimal_sep = getOptionA('currency_decimal_sep');
        $currency_space = getOptionA('currency_space');
        if ($currency_space == 1) {
            $spacer = " ";
        }

        if (empty($currency_decimal_places)) {
            $currency_decimal_places = 0;
        }
        if (empty($currency_decimal_sep)) {
            $currency_decimal_sep = '.';
        }
        if (empty($currency_position)) {
            $currency_position = 'right';
        }

        $output = '';

        $final_price = number_format($price, $currency_decimal_places, $currency_decimal_sep, $currency_thousand_sep);
        if ($currency_position == "right") {
            $output = $final_price . $spacer . $curr;
        } else
            $output = $curr . $spacer . $final_price;
        return $output;
    }

    public static function normalPrettyPrice($price = '') {
        if (is_numeric($price)) {
            return number_format($price, 2, '.', '');
        }
        return false;
    }

    public static function prettyDate($date = '', $time = false) {
        if (!empty($date)) {
            $format = "F d, Y";
            if ($time) {
                $format .= " G:i:s";
            }
            $date = date($format, strtotime($date));
        }
        return $date;
    }

    public static function generateActionsList($where_field = '', $where_val = '', $table = '', $slug = '') {
        $link = Yii::app()->createUrl('admin/' . $slug, array(
            'id' => $where_val,
            'lang' => Yii::app()->language
        ));
        $html = '
		 <a title="' . t("Edit") . '" href="' . $link . '" class="btn-floating btn-small blue lighten-1" style="margin-right:10px;">
          <i class="material-icons tiny">mode_edit</i>
         </a>
         
         <a title="' . t("Delete") . '" href="#no" class="rm-records btn-floating btn-small red lighten-1" 
         data-field="' . $where_field . '" data-value="' . $where_val . '" data-tbl="' . $table . '"
         >
          <i class="material-icons tiny">delete</i>
         </a>
         
         ';
        return $html;
    }

    public static function generateActionsListCustomer($where_field = '', $where_val = '', $table = '', $slug = '') {
        $link = Yii::app()->createUrl('admin/' . $slug, array(
            'id' => $where_val,
            'lang' => Yii::app()->language
        ));

        $link2 = Yii::app()->createUrl('admin/payments/', array(
            'customer_id' => $where_val,
            'lang' => Yii::app()->language
        ));

        $html = '
		 <a title="' . t("Edit") . '" href="' . $link . '" class="btn-floating btn-small blue lighten-1" style="margin-right:10px;">
          <i class="material-icons tiny">mode_edit</i>
         </a>
         
         <a title="' . t("Delete") . '" href="#no" class="rm-records btn-floating btn-small red lighten-1" 
         data-field="' . $where_field . '" data-value="' . $where_val . '" data-tbl="' . $table . '"
         >
          <i class="material-icons tiny">delete</i>
         </a>
         
         <a title="' . t("View Payments") . '" href="' . $link2 . '" class="btn-floating btn-small blue darken-3" style="margin-right:10px;">
          <i class="material-icons tiny">description</i>
         </a>
         ';
        return $html;
    }

    public static function generateActionsApproved($id = '') {
        $html = '
		 <a title="' . t("Approved") . '" href="#no" data-id="' . $id . '" class="approved-customer btn-floating btn-small teal lighten-1" style="margin-right:10px;">
          <i class="material-icons tiny">done_all</i>
         </a>                  
         ';
        return $html;
    }

    public static function dateNow() {
        return date('Y-m-d G:i:s');
    }

    public static function getCountryList() {
        $country = require_once 'CountryCode.php';
        return $country;
    }

    public static function asList($data = '', $key = '', $value = '') {
        $resp = array();
        if (is_array($data) && count($data) >= 1) {
            foreach ($data as $val) {
                $resp[$val[$key]] = $val[$value];
            }
            return $resp;
        }
        return array();
    }

    public static function getCustomerByEmail($email = '') {
        $db = new DbExt;
        $stmt = "SELECT * FROM
		{{customer}}
		WHERE
		email_address=" . self::q($email) . "
		LIMIT 0,1
		";
        if ($res = $db->rst($stmt)) {
            return $res[0];
        }
        return false;
    }

    public static function getClienteById($id_cliente = '') {
        $db = new DbExt;
        $stmt = "SELECT * FROM
		{{clientes}}
		WHERE
		id_cliente=" . self::q($id_cliente) . "
		LIMIT 0,1
		";
        if ($res = $db->rst($stmt)) {
            return $res[0];
        }
        return false;
    }

    public static function getClienteByEmail($email = '') {
        $db = new DbExt;
        $stmt = "SELECT * FROM
		{{clientes}}
		WHERE
		email=" . self::q($email) . "
		LIMIT 0,1
		";
        if ($res = $db->rst($stmt)) {
            return $res[0];
        }
        return false;
    }

    public static function getUsuarioByEmail($email = '') {
        $db = new DbExt;
        $stmt = "SELECT * FROM
		{{usuario}}
		WHERE
		email=" . self::q($email) . "
		LIMIT 0,1
		";
        if ($res = $db->rst($stmt)) {
            return $res[0];
        }
        return false;
    }

    public static function getUsuarioById($id = '') {
        $db = new DbExt;
        $stmt = "SELECT * FROM
		{{usuario}}
		WHERE
		id=" . self::q($id) . "
		LIMIT 0,1
		";
        if ($res = $db->rst($stmt)) {
            return $res[0];
        }
        return false;
    }

    public static function getCustomerByID($customer_id = '') {
        $db = new DbExt;
        $stmt = "SELECT * FROM
		{{customer}}
		WHERE
		customer_id=" . self::q($customer_id) . "
		LIMIT 0,1
		";
        if ($res = $db->rst($stmt)) {
            return $res[0];
        }
        return false;
    }

    public static function uploadPath() {
        $path = Yii::getPathOfAlias('webroot') . "/upload";
        if (!file_exists($path)) {
            mkdir($path, 0777);
        }
        return $path;
    }

    public static function uploadServiciosPath() {
        $path = Yii::getPathOfAlias('webroot') . "/upload/servicios";
        if (!file_exists($path)) {
            mkdir($path, 0777);
        }
        return $path;
    }

    public static function uploadCertificatePath() {
        self::uploadPath();

        $path = Yii::getPathOfAlias('webroot') . "/upload/certificate";
        if (!file_exists($path)) {
            mkdir($path, 0777);
        }
        return $path;
    }

    public static function uploadURL() {
        return Yii::app()->request->baseUrl . "/upload";
    }

    public static function uploadServiciosURL() {
        return Yii::app()->request->baseUrl . "/upload/servicios";
    }

    public static function generateCode($length = 8) {
        //$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz1234567890';
        $ret = '';
        for ($i = 0; $i < $length; ++$i) {
            $random = str_shuffle($chars);
            $ret .= $random[0];
        }
        return $ret;
    }

    public static function generateNumericCode($length = 8) {
        $chars = '1234567890';
        $ret = '';
        for ($i = 0; $i < $length; ++$i) {
            $random = str_shuffle($chars);
            $ret .= $random[0];
        }
        return $ret;
    }

    public static function getImageLink($key = '') {
        $logo = getOptionA($key);
        if (!empty($logo)) {
            if (file_exists(self::uploadPath() . "/$logo")) {
                return self::uploadURL() . "/$logo";
            }
        }
        return false;
    }

    public static function getTotalSignup($start = '', $end = '') {
        $db = new DbExt;
        $data = array();
        $stmt = "
		SELECT count(*)as total,		
		DATE_FORMAT(date_created,'%Y-%m-%d') as date_createdx
	     FROM
		{{customer}}
		WHERE
		date_created BETWEEN " . self::q($start) . " AND " . self::q($end) . "
		GROUP BY date_createdx
		ORDER BY date_createdx ASC		
		";
        if ($res = $db->rst($stmt)) {
            $first_date = date("Y-m-d", strtotime($res[0]['date_createdx'] . " -1 day"));
            $data[] = array(
                'date' => $first_date,
                'total' => 0
            );

            foreach ($res as $val) {
                $data[] = array(
                    'date' => $val['date_createdx'],
                    'total' => $val['total']
                );
            }
            return $data;
        }
        return false;
    }

    public static function currencyList() {
        $db = new DbExt;
        $stmt = "SELECT * FROM
    	{{currency}}
    	WHERE
    	status='published'
    	ORDER BY currency_code ASC
    	";
        if ($res = $db->rst($stmt)) {
            return $res;
        }
        return false;
    }

    public static function getCurrencyByID($curr_id = '') {
        $db = new DbExt;
        $stmt = "SELECT * FROM
    	{{currency}}
    	WHERE
    	curr_id=" . self::q($curr_id) . "
    	LIMIT 0,1
    	";
        if ($res = $db->rst($stmt)) {
            return $res[0];
        }
        return false;
    }

    public static function timeZone() {
        $version = phpversion();
        if ($version <= 5.2) {
            return self::timezoneList();
        }
        $list[''] = Yii::t("default", 'Please Select');
        $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        if (is_array($tzlist) && count($tzlist) >= 1) {
            foreach ($tzlist as $val) {
                $list[$val] = $val;
            }
        }
        return $list;
    }

    public static function timezoneList() {
        $t = array(
            '(UTC-11:00) Midway Island' => 'Pacific/Midway',
            '(UTC-11:00) Samoa' => 'Pacific/Samoa',
            '(UTC-10:00) Hawaii' => 'Pacific/Honolulu',
            '(UTC-09:00) Alaska' => 'US/Alaska',
            '(UTC-08:00) Pacific Time (US &amp; Canada)' => 'America/Los_Angeles',
            '(UTC-08:00) Tijuana' => 'America/Tijuana',
            '(UTC-07:00) Arizona' => 'US/Arizona',
            '(UTC-07:00) Chihuahua' => 'America/Chihuahua',
            '(UTC-07:00) La Paz' => 'America/Chihuahua',
            '(UTC-07:00) Mazatlan' => 'America/Mazatlan',
            '(UTC-07:00) Mountain Time (US &amp; Canada)' => 'US/Mountain',
            '(UTC-06:00) Central America' => 'America/Managua',
            '(UTC-06:00) Central Time (US &amp; Canada)' => 'US/Central',
            '(UTC-06:00) Guadalajara' => 'America/Mexico_City',
            '(UTC-06:00) Mexico City' => 'America/Mexico_City',
            '(UTC-06:00) Monterrey' => 'America/Monterrey',
            '(UTC-06:00) Saskatchewan' => 'Canada/Saskatchewan',
            '(UTC-05:00) Bogota' => 'America/Bogota',
            '(UTC-05:00) Eastern Time (US &amp; Canada)' => 'US/Eastern',
            '(UTC-05:00) Indiana (East)' => 'US/East-Indiana',
            '(UTC-05:00) Lima' => 'America/Lima',
            '(UTC-05:00) Quito' => 'America/Bogota',
            '(UTC-04:00) Atlantic Time (Canada)' => 'Canada/Atlantic',
            '(UTC-04:30) Caracas' => 'America/Caracas',
            '(UTC-04:00) La Paz' => 'America/La_Paz',
            '(UTC-04:00) Santiago' => 'America/Santiago',
            '(UTC-03:30) Newfoundland' => 'Canada/Newfoundland',
            '(UTC-03:00) Brasilia' => 'America/Sao_Paulo',
            '(UTC-03:00) Buenos Aires' => 'America/Argentina/Buenos_Aires',
            '(UTC-03:00) Georgetown' => 'America/Argentina/Buenos_Aires',
            '(UTC-03:00) Greenland' => 'America/Godthab',
            '(UTC-02:00) Mid-Atlantic' => 'America/Noronha',
            '(UTC-01:00) Azores' => 'Atlantic/Azores',
            '(UTC-01:00) Cape Verde Is.' => 'Atlantic/Cape_Verde',
            '(UTC+00:00) Casablanca' => 'Africa/Casablanca',
            '(UTC+00:00) Edinburgh' => 'Europe/London',
            '(UTC+00:00) Greenwich Mean Time : Dublin' => 'Etc/Greenwich',
            '(UTC+00:00) Lisbon' => 'Europe/Lisbon',
            '(UTC+00:00) London' => 'Europe/London',
            '(UTC+00:00) Monrovia' => 'Africa/Monrovia',
            '(UTC+00:00) UTC' => 'UTC',
            '(UTC+01:00) Amsterdam' => 'Europe/Amsterdam',
            '(UTC+01:00) Belgrade' => 'Europe/Belgrade',
            '(UTC+01:00) Berlin' => 'Europe/Berlin',
            '(UTC+01:00) Bern' => 'Europe/Berlin',
            '(UTC+01:00) Bratislava' => 'Europe/Bratislava',
            '(UTC+01:00) Brussels' => 'Europe/Brussels',
            '(UTC+01:00) Budapest' => 'Europe/Budapest',
            '(UTC+01:00) Copenhagen' => 'Europe/Copenhagen',
            '(UTC+01:00) Ljubljana' => 'Europe/Ljubljana',
            '(UTC+01:00) Madrid' => 'Europe/Madrid',
            '(UTC+01:00) Paris' => 'Europe/Paris',
            '(UTC+01:00) Prague' => 'Europe/Prague',
            '(UTC+01:00) Rome' => 'Europe/Rome',
            '(UTC+01:00) Sarajevo' => 'Europe/Sarajevo',
            '(UTC+01:00) Skopje' => 'Europe/Skopje',
            '(UTC+01:00) Stockholm' => 'Europe/Stockholm',
            '(UTC+01:00) Vienna' => 'Europe/Vienna',
            '(UTC+01:00) Warsaw' => 'Europe/Warsaw',
            '(UTC+01:00) West Central Africa' => 'Africa/Lagos',
            '(UTC+01:00) Zagreb' => 'Europe/Zagreb',
            '(UTC+02:00) Athens' => 'Europe/Athens',
            '(UTC+02:00) Bucharest' => 'Europe/Bucharest',
            '(UTC+02:00) Cairo' => 'Africa/Cairo',
            '(UTC+02:00) Harare' => 'Africa/Harare',
            '(UTC+02:00) Helsinki' => 'Europe/Helsinki',
            '(UTC+02:00) Istanbul' => 'Europe/Istanbul',
            '(UTC+02:00) Jerusalem' => 'Asia/Jerusalem',
            '(UTC+02:00) Kyiv' => 'Europe/Helsinki',
            '(UTC+02:00) Pretoria' => 'Africa/Johannesburg',
            '(UTC+02:00) Riga' => 'Europe/Riga',
            '(UTC+02:00) Sofia' => 'Europe/Sofia',
            '(UTC+02:00) Tallinn' => 'Europe/Tallinn',
            '(UTC+02:00) Vilnius' => 'Europe/Vilnius',
            '(UTC+03:00) Baghdad' => 'Asia/Baghdad',
            '(UTC+03:00) Kuwait' => 'Asia/Kuwait',
            '(UTC+03:00) Minsk' => 'Europe/Minsk',
            '(UTC+03:00) Nairobi' => 'Africa/Nairobi',
            '(UTC+03:00) Riyadh' => 'Asia/Riyadh',
            '(UTC+03:00) Volgograd' => 'Europe/Volgograd',
            '(UTC+03:30) Tehran' => 'Asia/Tehran',
            '(UTC+04:00) Abu Dhabi' => 'Asia/Muscat',
            '(UTC+04:00) Baku' => 'Asia/Baku',
            '(UTC+04:00) Moscow' => 'Europe/Moscow',
            '(UTC+04:00) Muscat' => 'Asia/Muscat',
            '(UTC+04:00) St. Petersburg' => 'Europe/Moscow',
            '(UTC+04:00) Tbilisi' => 'Asia/Tbilisi',
            '(UTC+04:00) Yerevan' => 'Asia/Yerevan',
            '(UTC+04:30) Kabul' => 'Asia/Kabul',
            '(UTC+05:00) Islamabad' => 'Asia/Karachi',
            '(UTC+05:00) Karachi' => 'Asia/Karachi',
            '(UTC+05:00) Tashkent' => 'Asia/Tashkent',
            '(UTC+05:30) Chennai' => 'Asia/Calcutta',
            '(UTC+05:30) Kolkata' => 'Asia/Kolkata',
            '(UTC+05:30) Mumbai' => 'Asia/Calcutta',
            '(UTC+05:30) New Delhi' => 'Asia/Calcutta',
            '(UTC+05:30) Sri Jayawardenepura' => 'Asia/Calcutta',
            '(UTC+05:45) Kathmandu' => 'Asia/Katmandu',
            '(UTC+06:00) Almaty' => 'Asia/Almaty',
            '(UTC+06:00) Astana' => 'Asia/Dhaka',
            '(UTC+06:00) Dhaka' => 'Asia/Dhaka',
            '(UTC+06:00) Ekaterinburg' => 'Asia/Yekaterinburg',
            '(UTC+06:30) Rangoon' => 'Asia/Rangoon',
            '(UTC+07:00) Bangkok' => 'Asia/Bangkok',
            '(UTC+07:00) Hanoi' => 'Asia/Bangkok',
            '(UTC+07:00) Jakarta' => 'Asia/Jakarta',
            '(UTC+07:00) Novosibirsk' => 'Asia/Novosibirsk',
            '(UTC+08:00) Beijing' => 'Asia/Hong_Kong',
            '(UTC+08:00) Chongqing' => 'Asia/Chongqing',
            '(UTC+08:00) Hong Kong' => 'Asia/Hong_Kong',
            '(UTC+08:00) Krasnoyarsk' => 'Asia/Krasnoyarsk',
            '(UTC+08:00) Kuala Lumpur' => 'Asia/Kuala_Lumpur',
            '(UTC+08:00) Perth' => 'Australia/Perth',
            '(UTC+08:00) Singapore' => 'Asia/Singapore',
            '(UTC+08:00) Taipei' => 'Asia/Taipei',
            '(UTC+08:00) Ulaan Bataar' => 'Asia/Ulan_Bator',
            '(UTC+08:00) Urumqi' => 'Asia/Urumqi',
            '(UTC+09:00) Irkutsk' => 'Asia/Irkutsk',
            '(UTC+09:00) Osaka' => 'Asia/Tokyo',
            '(UTC+09:00) Sapporo' => 'Asia/Tokyo',
            '(UTC+09:00) Seoul' => 'Asia/Seoul',
            '(UTC+09:00) Tokyo' => 'Asia/Tokyo',
            '(UTC+09:30) Adelaide' => 'Australia/Adelaide',
            '(UTC+09:30) Darwin' => 'Australia/Darwin',
            '(UTC+10:00) Brisbane' => 'Australia/Brisbane',
            '(UTC+10:00) Canberra' => 'Australia/Canberra',
            '(UTC+10:00) Guam' => 'Pacific/Guam',
            '(UTC+10:00) Hobart' => 'Australia/Hobart',
            '(UTC+10:00) Melbourne' => 'Australia/Melbourne',
            '(UTC+10:00) Port Moresby' => 'Pacific/Port_Moresby',
            '(UTC+10:00) Sydney' => 'Australia/Sydney',
            '(UTC+10:00) Yakutsk' => 'Asia/Yakutsk',
            '(UTC+11:00) Vladivostok' => 'Asia/Vladivostok',
            '(UTC+12:00) Auckland' => 'Pacific/Auckland',
            '(UTC+12:00) Fiji' => 'Pacific/Fiji',
            '(UTC+12:00) International Date Line West' => 'Pacific/Kwajalein',
            '(UTC+12:00) Kamchatka' => 'Asia/Kamchatka',
            '(UTC+12:00) Magadan' => 'Asia/Magadan',
            '(UTC+12:00) Marshall Is.' => 'Pacific/Fiji',
            '(UTC+12:00) New Caledonia' => 'Asia/Magadan',
            '(UTC+12:00) Solomon Is.' => 'Asia/Magadan',
            '(UTC+12:00) Wellington' => 'Pacific/Auckland',
            '(UTC+13:00) Nuku\'alofa' => 'Pacific/Tongatapu'
        );

        $t = array_flip($t);
        return $t;
    }

    public static function paymentGatewayList() {
        return array(
            'pyp' => t("Paypal"),
            'stp' => t("Stripe"),
            'mcd' => t("Mercadopago"),
            'rzr' => t("RazorPay"),
            'atz' => t("Authorize.net"),
        );
    }

    public static function prettyGateway($code = '') {
        $list = self::paymentGatewayList();
        if (array_key_exists($code, (array) $list)) {
            return $list[$code];
        }
        return $code;
    }

    public static function getEnabledPaymentList() {
        $enabled_list = array();
        $list = self::paymentGatewayList();
        foreach ($list as $key => $val) {

            switch ($key) {
                case "pyp":
                    $enabled = getOptionA('paypal_enabled');
                    if ($enabled == 1) {
                        $enabled_list[$key] = $val;
                    }
                    break;

                case "stp":
                    $enabled = getOptionA('stripe_enabled');
                    if ($enabled == 1) {
                        $enabled_list[$key] = $val;
                    }
                    break;

                case "mcd":
                    $enabled = getOptionA('mcd_enabled');
                    if ($enabled == 1) {
                        $enabled_list[$key] = $val;
                    }
                    break;

                case "rzr":
                    $enabled = getOptionA('rzr_enabled');
                    if ($enabled == 1) {
                        $enabled_list[$key] = $val;
                    }
                    break;

                case "atz":
                    $enabled = getOptionA('atz_enabled');
                    if ($enabled == 1) {
                        $enabled_list[$key] = $val;
                    }
                    break;

                case "psk":
                    $enabled = getOptionA('psk_enabled');
                    if ($enabled == 1) {
                        $enabled_list[$key] = $val;
                    }
                    break;

                default:
                    break;
            }
        }
        return $enabled_list;
    }

    public static function sendApprovedEmail($data = '') {
        if (isset($data['email_address'])) {
            $tpl = getOptionA('approved_tpl');
            $company_name = getOptionA('company_name');

            $login_link = Yii::app()->getBaseUrl(true) . "/app/login";

            $tpl = smarty('first_name', $data['first_name'], $tpl);
            $tpl = smarty('last_name', $data['last_name'], $tpl);
            $tpl = smarty('username', $data['email_address'], $tpl);
            $tpl = smarty('login_link', $login_link, $tpl);
            $tpl = smarty('company_name', $company_name, $tpl);

            $global_sender = getOptionA('global_sender');
            $subject = getOptionA('approved_tpl_subject');
            if (!empty($subject)) {
                $subject = smarty('first_name', $data['first_name'], $subject);
                $subject = smarty('last_name', $data['last_name'], $subject);
                $subject = smarty('company_name', $company_name, $subject);
            }

            if (!empty($global_sender)) {
                sendEmail($data['email_address'], $global_sender, $subject, $tpl);
            }
        }
    }

    public static function sendResetPassword($data = '') {
        if (isset($data['email'])) {
            $tpl = getOptionA('forgot_password_tpl');

            $login_link = Yii::app()->getBaseUrl(true) . "/app/login";

            $tpl = smarty('nombre', $data['nombre'], $tpl);
            $tpl = smarty('apellido', $data['apellido'], $tpl);
            $tpl = smarty('email', $data['email'], $tpl);
            $tpl = smarty('verification_code', $data['verification_code'], $tpl);

            $global_sender = getOptionA('global_sender');
            $subject = getOptionA('forgot_password_subject');
            if (!empty($subject)) {
                $subject = smarty('nombre', $data['nombre'], $subject);
                $subject = smarty('apellido', $data['apellido'], $subject);
            }

            if (!empty($global_sender)) {
                if (sendEmail($data['email'], $global_sender, $subject, $tpl)) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function sendRegistroCliente($data = '') {
        if (isset($data['email'])) {
            $tpl = getOptionA('signup_tpl_email');

            $login_link = Yii::app()->getBaseUrl(true) . "/user/login";

            $tpl = smarty('nombre', $data['nombre'], $tpl);
            $tpl = smarty('apellido', $data['apellido'], $tpl);
            $tpl = smarty('email', $data['email'], $tpl);
            $tpl = smarty('verification_code', $data['verification_code'], $tpl);

            $global_sender = getOptionA('global_sender');
            $subject = getOptionA('signup_tpl_email_subject');
            if (!empty($subject)) {
                $subject = smarty('nombre', $data['nombre'], $subject);
                $subject = smarty('apellido', $data['apellido'], $subject);
            }

            if (!empty($global_sender)) {
                if (sendEmail($data['email'], $global_sender, $subject, $tpl)) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function sendNoEfectivoCliente($data = '') {
        if (isset($data['email'])) {
            $tpl = getOptionA('no_efectivo_email');

            $login_link = Yii::app()->getBaseUrl(true) . "/user/login";

            $tpl = smarty('nombre', $data['nombre'], $tpl);
            $tpl = smarty('apellido', $data['apellido'], $tpl);
            $tpl = smarty('email', $data['email'], $tpl);

            $global_sender = getOptionA('global_sender');
            $subject = getOptionA('no_efectivo_subject');
            if (!empty($subject)) {
                $subject = smarty('nombre', $data['nombre'], $subject);
                $subject = smarty('apellido', $data['apellido'], $subject);
            }

            if (!empty($global_sender)) {
                if (sendEmail($data['email'], $global_sender, $subject, $tpl)) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function sendRegistroUsuario($data = '') {
        if (isset($data['email'])) {
            $tpl = getOptionA('signup_tpl_email');

            $login_link = Yii::app()->getBaseUrl(true) . "/user/login";

            $tpl = smarty('nombre', $data['nombre'], $tpl);
            $tpl = smarty('apellido', $data['apellido'], $tpl);
            $tpl = smarty('email', $data['email'], $tpl);
            $tpl = smarty('verification_code', $data['verification_code'], $tpl);

            $global_sender = getOptionA('global_sender');
            $subject = getOptionA('signup_tpl_email_subject');
            if (!empty($subject)) {
                $subject = smarty('nombre', $data['nombre'], $subject);
                $subject = smarty('apellido', $data['apellido'], $subject);
            }

            if (!empty($global_sender)) {
                if (sendEmail($data['email'], $global_sender, $subject, $tpl)) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function sendResetPasswordCliente($data = '') {
        if (isset($data['email'])) {
            $tpl = getOptionA('reset_password_tpl');

            $login_link = Yii::app()->getBaseUrl(true) . "/user/login";

            $tpl = smarty('nombre', $data['nombre'], $tpl);
            $tpl = smarty('apellido', $data['apellido'], $tpl);
            $tpl = smarty('email', $data['email'], $tpl);
            $tpl = smarty('verification_code', $data['verification_code'], $tpl);
            $tpl = smarty('password', $data['password'], $tpl);

            $global_sender = getOptionA('global_sender');
            $subject = getOptionA('forgot_password_subject');
            if (!empty($subject)) {
                $subject = smarty('nombre', $data['nombre'], $subject);
                $subject = smarty('apellido', $data['apellido'], $subject);
            }

            if (!empty($global_sender)) {
                if (sendEmail($data['email'], $global_sender, $subject, $tpl)) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function sendResetPasswordUsuario($data = '') {
        if (isset($data['email'])) {
            $tpl = getOptionA('reset_password_tpl');

            $login_link = Yii::app()->getBaseUrl(true) . "/user/login";

            $tpl = smarty('nombre', $data['nombre'], $tpl);
            $tpl = smarty('apellido', $data['apellido'], $tpl);
            $tpl = smarty('email', $data['email'], $tpl);
            $tpl = smarty('verification_code', $data['verification_code'], $tpl);
            $tpl = smarty('password', $data['password'], $tpl);

            $global_sender = getOptionA('global_sender');
            $subject = getOptionA('forgot_password_subject');
            if (!empty($subject)) {
                $subject = smarty('nombre', $data['nombre'], $subject);
                $subject = smarty('apellido', $data['apellido'], $subject);
            }

            if (!empty($global_sender)) {
                if (sendEmail($data['email'], $global_sender, $subject, $tpl)) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function getCustomerPaymentLogs($customer_id = '', $limit = 50, $transaction_type = "signup") {
        $db = new DbExt;
        $stmt = "SELECT * FROM
    	{{payment_logs}}
    	WHERE
    	customer_id=" . self::q($customer_id) . "
    	AND
    	transaction_type=" . self::q($transaction_type) . "
    	ORDER BY id DESC
    	LIMIT 0,$limit
    	";
        if ($res = $db->rst($stmt)) {
            return $res;
        }
        return false;
    }

    public static function seoFriendlyURL($string) {
        $string = str_replace(array('[\', \']'), '', $string);
        $string = preg_replace('/\[.*\]/U', '', $string);
        $string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $string);
        $string = htmlentities($string, ENT_COMPAT, 'utf-8');
        $string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string);
        $string = preg_replace(array('/[^a-z0-9]/i', '/[-]+/'), '-', $string);
        return strtolower(trim($string, '-'));
    }

    public static function getCustomList($status = '') {
        $and = '';
        if (!empty($status)) {
            $and = " AND status=" . Driver::q($status) . "";
        }

        $db = new DbExt;
        $stmt = "SELECT * FROM
    	{{page}}
    	WHERE
    	1
    	$and
    	LIMIT 0,100
    	";
        if ($res = $db->rst($stmt)) {
            return $res;
        }
        return false;
    }

    public static function getCustomPage($page_id = '') {
        $db = new DbExt;
        $stmt = "SELECT * FROM
    	{{page}}
    	WHERE
    	page_id=" . self::q($page_id) . "    	
    	LIMIT 0,1
    	";
        if ($res = $db->rst($stmt)) {
            return $res[0];
        }
        return false;
    }

    public static function getCustomPageByPageSlug($slug = '', $status = '') {
        $and = '';
        if (!empty($status)) {
            $and = " AND status=" . Driver::q($status) . "";
        }
        $db = new DbExt;
        $stmt = "SELECT * FROM
    	{{page}}
    	WHERE
    	slug=" . self::q($slug) . "    	
    	$and
    	LIMIT 0,1
    	";
        if ($res = $db->rst($stmt)) {
            return $res[0];
        }
        return false;
    }

    public static function getCustomPageAssign($assign_to = '') {
        $db = new DbExt;
        $stmt = "SELECT * FROM
    	{{page}}
    	WHERE
    	status ='published'
    	AND
    	active='1'
    	AND
    	assign_to=" . self::q($assign_to) . "
    	ORDER BY sequence,page_id ASC
    	LIMIT 0,100
    	";
        if ($res = $db->rst($stmt)) {
            return $res;
        }
        return false;
    }

    public static function getPageSlug($slug = '') {
        $db = new DbExt;

        $i = 1;
        $baseSlug = $slug;
        while (self::SlugExist($slug)) {
            $slug = $baseSlug . "-" . $i++;
        }
        return $slug;
    }

    public static function SlugExist($slug = '') {
        $db = new DbExt;
        $stmt = "SELECT * FROM
		{{page}}
		WHERE
		slug=" . Driver::q($slug) . "
		LIMIT 0,1
		";
        if ($res = $db->rst($stmt)) {
            return true;
        }
        return false;
    }

    public static function CheckCustomerExpiry() {
        $db = new DbExt;
        $date_now = date("Y-m-d");
        $stmt = "UPDATE 
    	{{customer}}
    	SET status='expired'
    	WHERE 
    	plan_expiration<" . Driver::q($date_now) . "
    	";
        $db->qry($stmt);
    }

    public static function getLanguage() {
        $list = array();
        $path = Yii::getPathOfAlias('webroot') . "/protected/messages";
        $res = scandir($path);
        if (is_array($res) && count($res) >= 1) {
            foreach ($res as $val) {
                if ($val == ".") {
                    
                } elseif ($val == "..") {
                    
                } else {
                    $list[] = $val;
                }
            }
            return $list;
        }
        return false;
    }

    public static function getAdminByUsername($username = '') {
        $db = new DbExt;
        $stmt = "
		SELECT * FROM
		{{admin}}
		WHERE
		username=" . self::q($username) . "		
		LIMIT 0,1
		";
        if ($res = $db->rst($stmt)) {
            return $res[0];
        }
        return false;
    }

    public static function getAdminByPassword($password = '') {
        $db = new DbExt;
        $stmt = "
		SELECT * FROM
		{{admin}}
		WHERE
		password=" . self::q($password) . "		
		LIMIT 0,1
		";
        if ($res = $db->rst($stmt)) {
            return $res[0];
        }
        return false;
    }

    public static function servicesListParentAsList() {
        $data = array();
        $data[0] = t("Select Parent Services");
        if ($res = self::servicesList()) {
            foreach ($res as $val) {
                $data[$val['services_id']] = $val['sevices_name'];
            }
            return $data;
        }
        return false;
    }

    public static function servicesList($services_parent_id = 0, $status = '') {
        $and = '';
        if (!empty($status)) {
            $and = " AND status = " . self::q($status) . " ";
        }
        $db = new DbExt;
        $stmt = "
		SELECT * FROM
		{{services}}
		WHERE
		services_parent_id=" . self::q($services_parent_id) . "
		$and
		ORDER BY sequence ASC
		";
        //dump($stmt);
        if ($res = $db->rst($stmt)) {
            return $res;
        }
        return false;
    }

    public static function servicesFullList($services_parent_id = 0, $status = '') {
        $data = array();
        if ($res = self::servicesList($services_parent_id, $status)) {
            foreach ($res as $val) {
                $val['sub'] = self::servicesList($val['services_id'], $status);
                $data[] = $val;
            }
            return $data;
        }
        return false;
    }

    public static function checkTableFields($table = '', $new_field = '') {
        $DbExt = new DbExt;
        $prefix = Yii::app()->db->tablePrefix;
        $existing_field = array();
        if ($res = self::checkTableStructure($table)) {
            foreach ($res as $val) {
                $existing_field[$val['Field']] = $val['Field'];
            }
            foreach ($new_field as $key_new => $val_new) {
                if (in_array($key_new, (array) $existing_field)) {
                    return true;
                }
            }
        }
        return false;
    }

    public static function checkTableStructure($table_name = '') {
        $db_ext = new DbExt;
        $stmt = " SHOW COLUMNS FROM {{{$table_name}}}";
        if ($res = $db_ext->rst($stmt)) {
            return $res;
        }
        return false;
    }

    public static function checkNewVersion() {
        $new = 0;
        // version 1.1		
        $new_fields = array('no_allowed_driver' => "no_allowed_driver");
        if (!self::checkTableFields('customer', $new_fields)) {
            $new++;
        }
        $new_fields = array('services' => "services");
        if (!self::checkTableFields('customer', $new_fields)) {
            $new++;
        }

        /* version 1.2 */
        $new_fields = array('auto_retry_assigment' => "auto_retry_assigment");
        if (!self::checkTableFields('customer', $new_fields)) {
            $new++;
        }
        $new_fields = array('critical' => "critical");
        if (!self::checkTableFields('driver_task', $new_fields)) {
            $new++;
        }
        $new_fields = array('with_broadcast' => "with_broadcast");
        if (!self::checkTableFields('customer', $new_fields)) {
            $new++;
        }

        /* version 1.4 */
        $new_fields = array('last_onduty' => "last_onduty");
        if (!self::checkTableFields('driver', $new_fields)) {
            $new++;
        }

        /* $new_fields=array('id'=>"id");
          if ( !self::checkTableFields('paystack_webhook',$new_fields)){
          $new++;
          } */

        if ($new > 0) {
            return true;
        } else
            return false;
    }

    public static function setSEO($title = '', $meta = '') {
        if (!empty($title)) {
            Yii::app()->clientScript->registerMetaTag($title, 'title');
        }
        if ($meta) {
            Yii::app()->clientScript->registerMetaTag($meta, 'description');
            Yii::app()->clientScript->registerMetaTag($meta, 'og:description');
        }
    }

    public static function isPromoCodeExist($promo_code_name = '', $promo_code_id = '') {
        $db_ext = new DbExt;

        $and = "";
        if (is_numeric($promo_code_id)) {
            $and .= " AND promo_code_id !=" . self::q($promo_code_id) . " ";
        }

        $stmt = "
		SELECT * FROM
		{{promo_code}}
		WHERE
		promo_code_name=" . self::q(trim($promo_code_name)) . "
		$and
		LIMIT 0,1
		";
        if ($res = $db_ext->rst($stmt)) {
            return $res[0];
        }
        return false;
    }

    public static function getPromoCodeByID($promo_code_id = '') {
        $db_ext = new DbExt;

        $stmt = "
		SELECT * FROM
		{{promo_code}}
		WHERE
		promo_code_id=" . self::q(trim($promo_code_id)) . "		
		LIMIT 0,1
		";
        if ($res = $db_ext->rst($stmt)) {
            return $res[0];
        }
        return false;
    }

    public static function getPromoCode($promo_code_name = '') {
        $db_ext = new DbExt;
        $stmt = "
		SELECT * FROM
		{{promo_code}}
		WHERE
		promo_code_name=" . self::q(trim($promo_code_name)) . "		
		AND
		status='published'
		LIMIT 0,1
		";
        if ($res = $db_ext->rst($stmt)) {
            return $res[0];
        }
        return false;
    }

    public static function mapProviderList() {
        return array(
            'google' => t("Google maps (default)"),
            'mapbox' => t("Mapbox")
        );
    }

    public static function checkIfTableExist($table_name = '') {
        $DbExt = new DbExt;
        $prefix = Yii::app()->db->tablePrefix;
        $table = $prefix . $table_name;
        $stmt = "SHOW TABLES LIKE " . self::q($table) . " ";
        if ($res = $DbExt->rst($stmt)) {
            return $res;
        }
        return false;
    }

}

/* END CLASS */

function prettyPrice($price = '', $currency_code = '') {
    return AdminFunctions::prettyPrice($price, $currency_code);
}

function normalPrettyPrice($price = '') {
    return AdminFunctions::normalPrettyPrice($price);
}

function prettyDate($date = '', $time = false) {
    return AdminFunctions::prettyDate($date, $time);
}

function dateNow() {
    return AdminFunctions::dateNow();
}
