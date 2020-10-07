<?php

class FrontFunctions {

    public static function getCompanyName() {
        $name = getOptionA('company_name');
        if (!empty($name)) {
            return $name;
        }
        return "Kartero";
    }

    public static function q($data) {
        return Yii::app()->db->quoteValue($data);
    }

    public static function getLogoURL() {
        //$logo=getOptionA('website_logo');
        $logo = Yii::app()->functions->getOptionAdmin('website_logo');
        if (!empty($logo)) {
            $logo_path = Driver::uploadPath() . "/$logo";
            if (file_exists($logo_path)) {
                return Driver::uploadURL() . "/$logo";
            } else
                return Yii::app()->getBaseUrl(true) . "/assets/images-front/logo.png";
        } else
            return Yii::app()->getBaseUrl(true) . "/assets/images-front/logo.png";
    }

    public static function getMenu($menu_id = '') {

        /* $home_link=Yii::app()->getBaseUrl(true);
          if(isset($_GET['language'])){
          $home_link.="/?language=".$_GET['language'];
          } */
        $home_link = Yii::app()->createUrl('/');
        $cmenu[] = array('visible' => true, 'label' => t('Home'),
            'url' => websiteUrl(), 'linkOptions' => array());


        if ($new_menu = AdminFunctions::getCustomPageAssign('top')) {
            foreach ($new_menu as $val_page) {
                $page_slug = "page-" . $val_page['slug'];
                $cmenu[] = array('visible' => true, 'label' => t($val_page['title']),
                    'url' => array('/front/' . $page_slug), 'linkOptions' => array());
            }
        }

        if ($menu_id == "mobile-nav") {
            $cmenu[] = array('visible' => true, 'label' => t('login in'),
                'itemOptions' => array('class' => "login"),
                'url' => array('/user/login'), 'linkOptions' => array(
                    'class' => "login"
            ));
        } else {
            $cmenu[] = array('visible' => true, 'label' => "<i class=\"ion-android-lock\"></i>" . t('LOGIN IN'),
                'itemOptions' => array('class' => "login"),
                'url' => array('/user/login'), 'linkOptions' => array(
                    'class' => "login"
            ));
        }

        $menu = array(
            'id' => $menu_id,
            'htmlOptions' => array(
                'class' => ''
            ),
            'activeCssClass' => 'active',
            'encodeLabel' => false,
            'items' => $cmenu
        );

        /* dump($menu);
          die(); */
        return $menu;
    }

  

    public static function getPlansPrice($plan_id = '') {
        if ($res = self::getPlansByID($plan_id)) {
            $price = $res['price'];
            if ($res['promo_price'] > 0.0001) {
                $price = $res['promo_price'];
            }
            return $price;
        }
        return 0;
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

    public static function getCustomerByToken($token = '') {
        if (empty($token)) {
            return false;
        }
        $db = new DbExt;
        $stmt = "SELECT * FROM
		{{customer}}
		WHERE
		token=" . self::q($token) . "
		LIMIT 0,1
		";
        if ($res = $db->rst($stmt)) {
            return $res[0];
        }
        return false;
    }

    public static function getUsuarioByToken($token = '') {
        if (empty($token)) {
            return false;
        }
        $db = new DbExt;
        $stmt = "SELECT * FROM
		{{usuario}}
		WHERE
		token=" . self::q($token) . "
		LIMIT 0,1
		";
        if ($res = $db->rst($stmt)) {
            return $res[0];
        }
        return false;
    }

    public static function getClienteByToken($token = '') {
        if (empty($token)) {
            return false;
        }
        $db = new DbExt;
        $stmt = "SELECT * FROM
		{{clientes}}
		WHERE
		token=" . self::q($token) . "
		LIMIT 0,1
		";
        if ($res = $db->rst($stmt)) {
            return $res[0];
        }
        return false;
    }

    public static function checkByEmailExist($email = '', $customer_id = '') {
        $db = new DbExt;
        $stmt = "SELECT * FROM
		{{customer}}
		WHERE
		email_address=" . self::q($email) . "
		AND
		customer_id<>" . self::q($customer_id) . "
		LIMIT 0,1
		";
        if ($res = $db->rst($stmt)) {
            return $res[0];
        }
        return false;
    }

    public static function sendEmailSignVerification($data = '', $verification_code = '') {
        if (isset($data['email_address'])) {
            $tpl = getOptionA('signup_tpl_email');
            $company_name = getOptionA('company_name');

            $tpl = smarty('first_name', $data['first_name'], $tpl);
            $tpl = smarty('first_name', $data['first_name'], $tpl);
            $tpl = smarty('verification_code', $verification_code, $tpl);
            $tpl = smarty('company_name', $company_name, $tpl);

            $global_sender = getOptionA('global_sender');
            $subject = getOptionA('signup_tpl_email_subject');
            if (!empty($subject)) {
                $subject = smarty('first_name', $data['first_name'], $subject);
                $subject = smarty('first_name', $data['first_name'], $subject);
                $subject = smarty('company_name', $company_name, $subject);
            }

            if (!empty($global_sender)) {
                sendEmail($data['email_address'], $global_sender, $subject, $tpl);
            }
        }
    }

    public static function sendEmailWelcome($data = '') {
        if (isset($data['email_address'])) {
            $tpl = getOptionA('welcome_tpl');
            $company_name = getOptionA('company_name');

            $tpl = smarty('first_name', $data['first_name'], $tpl);
            $tpl = smarty('last_name', $data['last_name'], $tpl);
            $tpl = smarty('company_name', $company_name, $tpl);

            $global_sender = getOptionA('global_sender');
            $subject = getOptionA('welcome_tpl_subject');
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

    public static function sendEmailWelcomeUsers($data = '') {
        if (isset($data['email'])) {
            $tpl = getOptionA('welcome_user_tpl');

            $tpl = smarty('email', $data['email'], $tpl);
            $tpl = smarty('password', $data['password'], $tpl);

            $global_sender = getOptionA('global_sender');
            $subject = getOptionA('welcome_tpl_subject');
            $subject = smarty('email', $data['email'], $subject);

            if (!empty($global_sender)) {
                sendEmail($data['email'], $global_sender, $subject, $tpl);
            }
        }
    }

    public static function formatPricing($price = '') {
        if (!is_numeric($price)) {
            return false;
        }
        $curr = '';
        $curr_id = getOptionA('website_currency');
        if ($res_cur = AdminFunctions::getCurrencyByID($curr_id)) {
            $curr = $res_cur['currency_symbol'];
        }
        $currency_position = getOptionA('currency_position');
        $currency_decimal_places = getOptionA('currency_decimal_places');
        $currency_thousand_sep = getOptionA('currency_thousand_sep');
        $currency_decimal_sep = getOptionA('currency_decimal_sep');

        if (empty($currency_decimal_places)) {
            $currency_decimal_places = 0;
        }
        if (empty($currency_decimal_sep)) {
            $currency_decimal_sep = '.';
        }
        if (empty($currency_position)) {
            $currency_position = 'right';
        }

        $final_price = number_format($price, $currency_decimal_places, $currency_decimal_sep, $currency_thousand_sep);
        if ($currency_position == "right") {
            $hml = "<price>$final_price <span>$curr</span></price>";
        } else
            $hml = "<price><span>$curr</span> $final_price</price>";
        return $hml;
    }

    public static function getPaypalConnection() {
        $paypal_con = array();
        $paypal_mode = getOptionA('paypal_mode');

        switch ($paypal_mode) {
            case "sandbox":
                $paypal_con['mode'] = "sandbox";
                $paypal_con['sandbox']['paypal_nvp'] = 'https://api-3t.sandbox.paypal.com/nvp';
                $paypal_con['sandbox']['paypal_web'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
                $paypal_con['sandbox']['user'] = trim(getOptionA('paypal_sandbox_user'));
                $paypal_con['sandbox']['psw'] = trim(getOptionA('paypal_sandbox_password'));
                $paypal_con['sandbox']['signature'] = trim(getOptionA('paypal_sandbox_signature'));
                $paypal_con['sandbox']['version'] = '61.0';
                $paypal_con['sandbox']['action'] = 'Sale';
                break;

            case "live":
                $paypal_con['mode'] = "live";
                $paypal_con['live']['paypal_nvp'] = 'https://api-3t.paypal.com/nvp';
                $paypal_con['live']['paypal_web'] = 'https://www.paypal.com/cgi-bin/webscr';
                $paypal_con['live']['user'] = trim(getOptionA('paypal_live_user'));
                $paypal_con['live']['psw'] = trim(getOptionA('paypal_live_password'));
                $paypal_con['live']['signature'] = trim(getOptionA('paypal_live_signature'));
                $paypal_con['live']['version'] = '61.0';
                $paypal_con['live']['action'] = 'Sale';
                break;

            default:
                $paypal_con = false;
                break;
        }
        return $paypal_con;
    }

    public static function getCurrenyCode($return_code = false) {
        $curr_id = getOptionA('website_currency');
        if (!empty($curr_id)) {
            if ($res = AdminFunctions::getCurrencyByID($curr_id)) {
                if ($return_code) {
                    return $res['currency_code'];
                } else {
                    return array(
                        'currency_code' => $res['currency_code'],
                        'currency_symbol' => $res['currency_symbol'],
                    );
                }
            }
        }
        return false;
    }

    public static function savePaymentLogs($customer_id, $transaction_type = '', $payment_provider = '', $memo = '', $total_paid = 0, $currency_code = '', $transaction_ref = ''
    ) {
        $db = new DbExt;

        $params = array(
            'customer_id' => $customer_id,
            'transaction_type' => $transaction_type,
            'payment_provider' => $payment_provider,
            'memo' => $memo,
            'total_paid' => $total_paid,
            'currency_code' => $currency_code,
            'transaction_ref' => $transaction_ref,
            'date_created' => AdminFunctions::dateNow(),
            'ip_address' => $_SERVER['REMOTE_ADDR']
        );

        if ($promo = self::getPromoCodeDetails()) {
            $total_paid = $total_paid - $promo['discount_amount'];
            $params['total_paid'] = $total_paid;
            $params['promo_code_id'] = $promo['promo_code_id'];
            $params['promo_code_discount'] = $promo['discount_amount'];
        }

        $db->insertData("{{payment_logs}}", $params);
    }

    public static function YearList() {
        $length = 20;
        $year = date("Y") - 1;
        for ($i = 0; $i < $length; $i++) {
            $_year = $year + $i;
            $_year = substr($_year, 2, 2);
            $year_list[$_year] = $year + $i;
        }
        return $year_list;
    }

    public static function MonthList() {
        $month["01"] = t("January");
        $month["02"] = t("February");
        $month["03"] = t("March");
        $month["04"] = t("April");
        $month["05"] = t("May");
        $month["06"] = t("June");
        $month["07"] = t("July");
        $month["08"] = t("August");
        $month["09"] = t("September");
        $month["10"] = t("October");
        $month["11"] = t("November");
        $month["12"] = t("December");
        return $month;
    }

    public static function urlFixed($url) {
        if (strpos($url, '://') === false)
            if (self::isSSL()) {
                $url = 'https://' . $url;
            } else
                $url = 'http://' . $url;
        return $url;
    }

    public static function isSSL() {
        if (isset($_SERVER['HTTPS'])) {
            if ($_SERVER['HTTPS'] == 1) {
                return true;
            } elseif ($_SERVER['HTTPS'] == 'on') {
                return true;
            }
        } elseif ($_SERVER['SERVER_PORT'] == 443) {
            return true;
        }
        return false;
    }

    public static function getMenus($assign_top = '') {

        $cmenu = array();
        if ($new_menu = AdminFunctions::getCustomPageAssign($assign_top)) {
            foreach ($new_menu as $val_page) {
                $page_slug = "page-" . $val_page['slug'];
                $cmenu[] = array('visible' => true, 'label' => $val_page['title'],
                    'url' => array('/front/' . $page_slug), 'linkOptions' => array());
            }
        }

        $menu = array(
            'id' => '',
            'htmlOptions' => array(
                'class' => ''
            ),
            'activeCssClass' => 'active',
            'encodeLabel' => false,
            'items' => $cmenu
        );

        return $menu;
    }

    public static function websiteUrl() {
        return Yii::app()->getBaseUrl(true);
    }

    public static function getPromoCodeDetails() {
        $enabled_promo_codes = getOptionA("enabled_promo_codes");
        if ($enabled_promo_codes == 1) {
            if (isset($_SESSION['promo_code'])) {
                if (is_array($_SESSION['promo_code']) && count($_SESSION['promo_code']) >= 1) {
                    return $_SESSION['promo_code'];
                }
            }
        }
        return false;
    }

    public static function ClearPromoCode() {
        unset($_SESSION['promo_code']);
    }

}

/*end class*/