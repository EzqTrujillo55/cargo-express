<?php
$DbExt = new DbExt();

$date_default = "datetime NOT NULL DEFAULT CURRENT_TIMESTAMP";
if($res=$DbExt->rst("SELECT VERSION() as mysql_version")){
$res=$res[0];			
$mysql_version = (float)$res['mysql_version'];	
if($mysql_version<=5.5){				
$date_default="datetime NOT NULL DEFAULT '0000-00-00 00:00:00'";
}
}

$prefix=Yii::app()->db->tablePrefix;		

dump("mysql_version=>$mysql_version");
dump($date_default);
dump($prefix);

$stmt='';

/*admin TABLE*/
$stmt.= "
ALTER TABLE ".$prefix."admin
CHANGE `date_created` `date_created` $date_default;

ALTER TABLE ".$prefix."admin
CHANGE `date_modified` `date_modified` $date_default;

ALTER TABLE ".$prefix."admin
CHANGE `last_login` `last_login` $date_default;
";


/*api_logs TABLE*/
$stmt.= "
ALTER TABLE ".$prefix."api_logs CHANGE `api_functions` `api_functions` VARCHAR(100)
CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';

ALTER TABLE ".$prefix."api_logs
CHANGE `date_created` `date_created` $date_default;

ALTER TABLE ".$prefix."api_logs
CHANGE `date_call` `date_call` date DEFAULT NULL;
";

/*contacts TABLE*/
$stmt.= "
ALTER TABLE ".$prefix."contacts 
CHANGE `customer_id` `customer_id` INT(14) NOT NULL DEFAULT '0';

ALTER TABLE ".$prefix."contacts CHANGE `fullname` `fullname` VARCHAR(255)
CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';

ALTER TABLE ".$prefix."contacts CHANGE `email` `email` VARCHAR(255)
CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';

ALTER TABLE ".$prefix."contacts CHANGE `phone` `phone` VARCHAR(50)
CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';

ALTER TABLE ".$prefix."contacts CHANGE `address` `address` VARCHAR(255)
CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';

ALTER TABLE ".$prefix."contacts CHANGE `addresss_lat` `addresss_lat` VARCHAR(50)
CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';

ALTER TABLE ".$prefix."contacts CHANGE `addresss_lng` `addresss_lng` VARCHAR(50)
CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';

ALTER TABLE ".$prefix."contacts
CHANGE `date_created` `date_created` $date_default;

ALTER TABLE ".$prefix."contacts
CHANGE `date_modified` `date_modified` $date_default;
";


/*currency TABLE*/
$stmt.= "
ALTER TABLE ".$prefix."currency
CHANGE `date_created` `date_created` $date_default;

ALTER TABLE ".$prefix."currency
CHANGE `date_modified` `date_modified` $date_default;
";


/*customer TABLE*/
$stmt.= "
ALTER TABLE ".$prefix."customer
CHANGE `date_created` `date_created` $date_default;

ALTER TABLE ".$prefix."customer
CHANGE `date_modified` `date_modified` $date_default;

ALTER TABLE ".$prefix."customer
CHANGE `last_login` `last_login` $date_default;

ALTER TABLE ".$prefix."customer
CHANGE `plan_expiration` `plan_expiration` date DEFAULT NULL;

ALTER TABLE ".$prefix."customer
CHANGE `verification_confirm_date` `verification_confirm_date` $date_default;
";


/*driver TABLE*/
$stmt.= "
ALTER TABLE ".$prefix."driver
CHANGE `date_created` `date_created` $date_default;

ALTER TABLE ".$prefix."driver
CHANGE `date_modified` `date_modified` $date_default;

ALTER TABLE ".$prefix."driver
CHANGE `last_login` `last_login` $date_default;

ALTER TABLE ".$prefix."driver
CHANGE `location_address` `location_address` text;

ALTER TABLE ".$prefix."driver
CHANGE `device_id` `device_id` text;
";

/*driver_assignment TABLE*/
$stmt.= "
ALTER TABLE ".$prefix."driver_assignment
CHANGE `date_created` `date_created` $date_default;

ALTER TABLE ".$prefix."driver_assignment
CHANGE `date_process` `date_process` $date_default;
";


/*driver_pushlog TABLE*/
$stmt.= "
ALTER TABLE ".$prefix."driver_pushlog
CHANGE `device_id` `device_id` text;

ALTER TABLE ".$prefix."driver_pushlog
CHANGE `json_response` `json_response` text;

ALTER TABLE ".$prefix."driver_pushlog
CHANGE `date_created` `date_created` $date_default;

ALTER TABLE ".$prefix."driver_pushlog
CHANGE `date_process` `date_process` $date_default;
";


/*driver_task TABLE*/
$stmt.= "
ALTER TABLE ".$prefix."driver_task
CHANGE `delivery_date` `delivery_date` $date_default;

ALTER TABLE ".$prefix."driver_task
CHANGE `date_created` `date_created` $date_default;

ALTER TABLE ".$prefix."driver_task
CHANGE `date_modified` `date_modified` $date_default;

ALTER TABLE ".$prefix."driver_task
CHANGE `assign_started` `assign_started` $date_default;
";


/*driver_team TABLE*/
$stmt.= "   
ALTER TABLE ".$prefix."driver_team
CHANGE `date_created` `date_created` $date_default;

ALTER TABLE ".$prefix."driver_team
CHANGE `date_modified` `date_modified` $date_default;
";

/*driver_track_location TABLE*/
$stmt.= "
ALTER TABLE ".$prefix."driver_track_location
CHANGE `date_created` `date_created` $date_default;

ALTER TABLE ".$prefix."driver_track_location
CHANGE `date_log` `date_log` date DEFAULT NULL;
";


/*email_logs TABLE*/
$stmt.= "
ALTER TABLE ".$prefix."email_logs
CHANGE `date_created` `date_created` $date_default;

ALTER TABLE ".$prefix."email_logs
CHANGE `content` `content` text;
";

/*option TABLE*/
$stmt.= "
ALTER TABLE ".$prefix."option
CHANGE `option_value` `option_value` text;
";

/*page TABLE*/
$stmt.= "
ALTER TABLE ".$prefix."page
CHANGE `content` `content` text;

ALTER TABLE ".$prefix."page
CHANGE `date_created` `date_created` $date_default;

ALTER TABLE ".$prefix."page
CHANGE `date_modified` `date_modified` $date_default;
";

/*payment_logs TABLE*/
$stmt.= "
ALTER TABLE ".$prefix."payment_logs
CHANGE `memo` `memo` text;

ALTER TABLE ".$prefix."payment_logs
CHANGE `date_created` `date_created` $date_default;
";

/*plan TABLE*/
$stmt.= "
ALTER TABLE ".$prefix."plan
CHANGE `plan_name_description` `plan_name_description` text;

ALTER TABLE ".$prefix."plan
CHANGE `date_created` `date_created` $date_default;

ALTER TABLE ".$prefix."plan
CHANGE `date_modified` `date_modified` $date_default;
";

/*promo_code TABLE*/
$stmt.= "
ALTER TABLE ".$prefix."promo_code
CHANGE `expiration` `expiration` date DEFAULT NULL;

ALTER TABLE ".$prefix."promo_code
CHANGE `date_created` `date_created` $date_default;

ALTER TABLE ".$prefix."promo_code
CHANGE `date_modified` `date_modified` $date_default;
";

/*push_broadcast TABLE*/
$stmt.= "
ALTER TABLE ".$prefix."push_broadcast
CHANGE `date_created` `date_created` $date_default;

ALTER TABLE ".$prefix."push_broadcast
CHANGE `date_process` `date_process` $date_default;
";

/*services TABLE*/
$stmt.= "
ALTER TABLE ".$prefix."services
CHANGE `description` `description` text;

ALTER TABLE ".$prefix."services
CHANGE `date_created` `date_created` $date_default;

ALTER TABLE ".$prefix."services
CHANGE `date_modified` `date_modified` $date_default;
";


/*sms_logs TABLE*/
$stmt.= "
ALTER TABLE ".$prefix."sms_logs
CHANGE `raw` `raw` text;

ALTER TABLE ".$prefix."sms_logs
CHANGE `date_created` `date_created` $date_default;
";


/*task_history TABLE*/
$stmt.= "
ALTER TABLE ".$prefix."task_history
CHANGE `remarks` `remarks` text;

ALTER TABLE ".$prefix."task_history
CHANGE `reason` `reason` text;

ALTER TABLE ".$prefix."task_history
CHANGE `date_created` `date_created` $date_default;

ALTER TABLE ".$prefix."task_history CHANGE `ip_address` `ip_address` VARCHAR(50)
CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';     

";