<?php
$date_default = "datetime NOT NULL DEFAULT CURRENT_TIMESTAMP";
if($res=$DbExt->rst("SELECT VERSION() as mysql_version")){
	$res=$res[0];			
	$mysql_version = (float)$res['mysql_version'];	
	if($mysql_version<=5.5){				
		$date_default="datetime NOT NULL DEFAULT '0000-00-00 00:00:00'";
	}
}

$tbl['admin']="
CREATE TABLE IF NOT EXISTS ".$table_prefix."admin (
  `admin_id` int(14) NOT NULL,
  `first_name` varchar(255) NOT NULL DEFAULT '',
  `last_name` varchar(255) NOT NULL DEFAULT '',
  `username` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(100) NOT NULL DEFAULT '',
  `date_created` $date_default,
  `date_modified` $date_default,
  `last_login` $date_default,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `email_address` varchar(100) NOT NULL DEFAULT ''  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE ".$table_prefix."admin
  ADD PRIMARY KEY (`admin_id`),
  ADD KEY `username` (`username`);
 
ALTER TABLE ".$table_prefix."admin
  MODIFY `admin_id` int(14) NOT NULL AUTO_INCREMENT;
";


$tbl['currency']="
CREATE TABLE IF NOT EXISTS ".$table_prefix."currency (
  `curr_id` int(14) NOT NULL,
  `currency_code` varchar(3) NOT NULL DEFAULT '',
  `currency_symbol` varchar(20) NOT NULL DEFAULT '',
  `status` varchar(100) NOT NULL DEFAULT 'pending',
  `date_created` $date_default ,
  `date_modified` $date_default ,
  `ip_address` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE ".$table_prefix."currency
  ADD PRIMARY KEY (`curr_id`);
  
ALTER TABLE ".$table_prefix."currency
  MODIFY `curr_id` int(14) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;  
";

$tbl['customer']="
CREATE TABLE IF NOT EXISTS ".$table_prefix."customer (
  `customer_id` int(14) NOT NULL,
  `first_name` varchar(255) NOT NULL DEFAULT '',
  `last_name` varchar(255) NOT NULL DEFAULT '',
  `mobile_number` varchar(20) NOT NULL DEFAULT '',
  `email_address` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `company_name` varchar(255) NOT NULL DEFAULT '',
  `company_address` varchar(255) NOT NULL DEFAULT '',
  `country_code` varchar(3) NOT NULL DEFAULT '',
  `status` varchar(100) NOT NULL DEFAULT 'pending',
  `date_created` $date_default,
  `date_modified` $date_default,
  `last_login` $date_default,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `enabled_auto_assign` int(1) NOT NULL DEFAULT '0',
  `include_offline_driver` int(1) NOT NULL DEFAULT '0',
  `autoassign_notify_email` varchar(255) NOT NULL DEFAULT '' ,
  `request_expire` int(14) NOT NULL DEFAULT '0',
  `auto_assign_type` varchar(50) NOT NULL DEFAULT '',
  `assign_request_expire` int(14) NOT NULL DEFAULT '0',
  `plan_id` int(14) NOT NULL DEFAULT '0',
  `plan_price` float(14,4) NOT NULL DEFAULT '0.0000',
  `plan_expiration` date DEFAULT NULL,
  `plan_currency_code` varchar(3) NOT NULL DEFAULT '',
  `with_sms` int(1) NOT NULL DEFAULT '2',
  `token` varchar(255) NOT NULL DEFAULT '',
  `verification_code` varchar(50) NOT NULL DEFAULT '',
  `verification_confirm_date` $date_default,
  `needs_approval` int(1) NOT NULL DEFAULT '2',
  `renew_plan_id` int(14) NOT NULL DEFAULT '0',
  `driver_assign_radius` int(14) NOT NULL DEFAULT '0',
  `no_allowed_driver` varchar(50) NOT NULL DEFAULT '',
  `no_allowed_task` varchar(50) NOT NULL DEFAULT '',
  `services` varchar(255) NOT NULL DEFAULT '',
  `sms_limit` int(14) NOT NULL DEFAULT '0',
  `auto_retry_assigment` varchar(1) NOT NULL DEFAULT '',
  `with_broadcast` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE ".$table_prefix."customer
  ADD PRIMARY KEY (`customer_id`),
  ADD KEY `email_address` (`email_address`),
  ADD KEY `status` (`status`),
  ADD KEY `token` (`token`);

ALTER TABLE ".$table_prefix."customer
  MODIFY `customer_id` int(14) NOT NULL AUTO_INCREMENT;
";

$tbl['driver']="

CREATE TABLE IF NOT EXISTS ".$table_prefix."driver (
  `driver_id` int(14) NOT NULL,
  `customer_id` int(14) NOT NULL DEFAULT '0',
  `on_duty` int(1) NOT NULL DEFAULT '1',
  `first_name` varchar(255) NOT NULL DEFAULT '',
  `last_name` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `phone` varchar(20) NOT NULL DEFAULT '',
  `username` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(100) NOT NULL DEFAULT '',
  `team_id` int(14) NOT NULL DEFAULT '0',
  `transport_type_id` varchar(50) NOT NULL DEFAULT '',
  `transport_description` varchar(255) NOT NULL DEFAULT '',
  `licence_plate` varchar(255) NOT NULL DEFAULT '',
  `color` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `date_created` $date_default,
  `date_modified` $date_default,
  `last_login` $date_default,
  `last_online` int(14) NOT NULL DEFAULT '0',
  `location_address` text,
  `location_lat` varchar(50) NOT NULL DEFAULT '',
  `location_lng` varchar(50) NOT NULL DEFAULT '',
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `forgot_pass_code` varchar(10) NOT NULL DEFAULT '',
  `token` varchar(255) NOT NULL DEFAULT '',
  `device_id` text,
  `device_platform` varchar(50) NOT NULL DEFAULT 'Android',
  `enabled_push` int(1) NOT NULL DEFAULT '1',
  `profile_photo` varchar(255) NOT NULL DEFAULT '',
  `app_version` varchar(14) NOT NULL DEFAULT '',
  `last_onduty` varchar(50) NOT NULL DEFAULT '',
  `is_online` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE ".$table_prefix."driver
  ADD PRIMARY KEY (`driver_id`),
  ADD KEY `team_id` (`team_id`),
  ADD KEY `user_id` (`customer_id`),
  ADD KEY `status` (`status`);


ALTER TABLE ".$table_prefix."driver
  MODIFY `driver_id` int(14) NOT NULL AUTO_INCREMENT;
";


$tbl['driver_assignment']="
CREATE TABLE IF NOT EXISTS ".$table_prefix."driver_assignment (
  `assignment_id` int(14) NOT NULL,
  `auto_assign_type` varchar(50) NOT NULL DEFAULT '',
  `task_id` int(14) NOT NULL DEFAULT '0',
  `driver_id` int(14) NOT NULL DEFAULT '0',
  `first_name` varchar(255) NOT NULL DEFAULT '',
  `last_name` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(100) NOT NULL DEFAULT 'pending',
  `task_status` varchar(255) NOT NULL DEFAULT 'unassigned',
  `date_created` $date_default,
  `date_process` $date_default,
  `ip_address` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE ".$table_prefix."driver_assignment
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `task_id` (`task_id`),
  ADD KEY `driver_id` (`driver_id`);

ALTER TABLE ".$table_prefix."driver_assignment
  MODIFY `assignment_id` int(14) NOT NULL AUTO_INCREMENT;
";


$tbl['driver_pushlog']="
CREATE TABLE IF NOT EXISTS ".$table_prefix."driver_pushlog (
  `push_id` int(14) NOT NULL,
  `customer_id` int(14) NOT NULL DEFAULT '0',
  `device_platform` varchar(50) NOT NULL DEFAULT '',
  `device_id` text,
  `push_title` varchar(255) NOT NULL DEFAULT '',
  `push_message` varchar(255) NOT NULL DEFAULT '',
  `push_type` varchar(50) NOT NULL DEFAULT 'task',
  `actions` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `json_response` text,
  `driver_id` int(14) NOT NULL DEFAULT '0',
  `task_id` int(14) NOT NULL DEFAULT '0',
  `date_created` $date_default,
  `date_process` $date_default,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `is_read` int(1) DEFAULT '2',
  `broadcast_id` int(14) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE ".$table_prefix."driver_pushlog
  ADD PRIMARY KEY (`push_id`),
  ADD KEY `device_platform` (`device_platform`),
  ADD KEY `status` (`status`),
  ADD KEY `task_id` (`task_id`),
 ADD KEY `broadcast_id` (`broadcast_id`);
  
ALTER TABLE ".$table_prefix."driver_pushlog
  MODIFY `push_id` int(14) NOT NULL AUTO_INCREMENT;
";


$tbl['driver_task']="
CREATE TABLE IF NOT EXISTS ".$table_prefix."driver_task (
  `task_id` int(14) NOT NULL,
  `customer_id` int(14) NOT NULL DEFAULT '0',
  `task_description` varchar(255) NOT NULL DEFAULT '',
  `trans_type` varchar(255) NOT NULL DEFAULT '',
  `contact_number` varchar(50) NOT NULL DEFAULT '',
  `email_address` varchar(200) NOT NULL DEFAULT '',
  `customer_name` varchar(255) NOT NULL DEFAULT '',
  `delivery_date` $date_default,
  `delivery_address` varchar(255) NOT NULL DEFAULT '',
  `team_id` int(14) NOT NULL DEFAULT '0',
  `driver_id` int(14) NOT NULL DEFAULT '0',
  `task_lat` varchar(50) NOT NULL DEFAULT '',
  `task_lng` varchar(50) NOT NULL DEFAULT '',
  `customer_signature` varchar(255) NOT NULL DEFAULT '',
  `status` varchar(255) NOT NULL DEFAULT 'unassigned',
  `date_created` $date_default,
  `date_modified` $date_default,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `auto_assign_type` varchar(50) NOT NULL DEFAULT '',
  `assign_started` $date_default,
  `assignment_status` varchar(255) NOT NULL DEFAULT '',
  `dropoff_contact_name` varchar(255) NOT NULL DEFAULT '',
  `dropoff_contact_number` varchar(50) NOT NULL DEFAULT '',
  `drop_address` varchar(255) NOT NULL DEFAULT '',
  `dropoff_task_lat` varchar(50) NOT NULL DEFAULT '',
  `dropoff_task_lng` varchar(50) NOT NULL DEFAULT '',
  `task_token` varchar(255) NOT NULL DEFAULT '',
  `ratings` varchar(14) NOT NULL DEFAULT '',
  `rating_comment` varchar(255) NOT NULL DEFAULT '',
  `critical` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE ".$table_prefix."driver_task
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `user_id` (`customer_id`),
  ADD KEY `team_id` (`team_id`),
  ADD KEY `driver_id` (`driver_id`);

ALTER TABLE ".$table_prefix."driver_task
  MODIFY `task_id` int(14) NOT NULL AUTO_INCREMENT;  
  
ALTER TABLE ".$table_prefix."driver_task AUTO_INCREMENT = 100000;  
";


$tbl['driver_team']="
CREATE TABLE IF NOT EXISTS ".$table_prefix."driver_team (
  `team_id` int(14) NOT NULL,
  `customer_id` int(14) NOT NULL DEFAULT '0',
  `team_name` varchar(255) NOT NULL DEFAULT '',
  `location_accuracy` varchar(50) NOT NULL DEFAULT '',
  `status` varchar(255) NOT NULL DEFAULT '',
  `date_created` $date_default,
  `date_modified` $date_default,
  `ip_address` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE ".$table_prefix."driver_team
  ADD PRIMARY KEY (`team_id`),
  ADD KEY `user_id` (`customer_id`),
  ADD KEY `status` (`status`);
  
ALTER TABLE ".$table_prefix."driver_team
  MODIFY `team_id` int(14) NOT NULL AUTO_INCREMENT;  
";


$tbl['email_logs']="
CREATE TABLE IF NOT EXISTS ".$table_prefix."email_logs (
  `id` int(14) NOT NULL,
  `email_address` varchar(255) NOT NULL DEFAULT '',
  `sender` varchar(255) NOT NULL DEFAULT '',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `content` text,
  `status` varchar(255) NOT NULL DEFAULT '',
  `date_created` $date_default,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `customer_id` int(14) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE ".$table_prefix."email_logs
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

ALTER TABLE ".$table_prefix."email_logs
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;  
";



$tbl['option']="
CREATE TABLE IF NOT EXISTS ".$table_prefix."option (
  `id` int(14) NOT NULL,
  `customer_id` int(14) NOT NULL DEFAULT '0',
  `option_name` varchar(255) NOT NULL DEFAULT '',
  `option_value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `".$table_prefix."option`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `option_name` (`option_name`);
  
ALTER TABLE `".$table_prefix."option`
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;  
";


$tbl['page']="
CREATE TABLE IF NOT EXISTS ".$table_prefix."page (
  `page_id` int(14) NOT NULL,
  `slug` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` text,
  `status` varchar(255) NOT NULL DEFAULT 'published',
  `active` int(1) NOT NULL DEFAULT '2',
  `assign_to` varchar(100) NOT NULL DEFAULT 'bottom-1',
  `date_created` $date_default,
  `date_modified` $date_default,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `sequence` int(14) NOT NULL NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE ".$table_prefix."page
  ADD PRIMARY KEY (`page_id`),
  ADD KEY `slug` (`slug`);
  
ALTER TABLE ".$table_prefix."page
  MODIFY `page_id` int(14) NOT NULL AUTO_INCREMENT;
";



$tbl['payment_logs']="
CREATE TABLE IF NOT EXISTS ".$table_prefix."payment_logs (
  `id` int(14) NOT NULL,
  `customer_id` int(14) NOT NULL DEFAULT '0',
  `transaction_type` varchar(255) NOT NULL DEFAULT 'signup',
  `payment_provider` varchar(100) NOT NULL DEFAULT '',
  `memo` text,
  `total_paid` float(14,4) NOT NULL DEFAULT '0.0000',
  `currency_code` varchar(3) NOT NULL DEFAULT '',
  `transaction_ref` varchar(255) NOT NULL DEFAULT '',
  `date_created` $date_default,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `promo_code_id` int(14) NOT NULL DEFAULT '0',
  `promo_code_discount` varchar(14) NOT NULL DEFAULT ''  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE ".$table_prefix."payment_logs
  ADD PRIMARY KEY (`id`),
  ADD KEY `promo_code_id` (`promo_code_id`);
  
ALTER TABLE ".$table_prefix."payment_logs
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;
";


$tbl['plan']="
CREATE TABLE IF NOT EXISTS ".$table_prefix."plan (
  `plan_id` int(14) NOT NULL,
  `plan_name` varchar(255) NOT NULL DEFAULT '',
  `plan_name_description` text,
  `price` float(14,4) NOT NULL DEFAULT '0.0000',
  `promo_price` float(14,4) NOT NULL DEFAULT '0.0000',
  `plan_type` varchar(255) NOT NULL DEFAULT '',
  `expiration` varchar(50) NOT NULL DEFAULT '',
  `allowed_driver` varchar(50) NOT NULL DEFAULT '',
  `allowed_task` varchar(50) NOT NULL DEFAULT '',
  `with_sms` int(1) NOT NULL DEFAULT '0',
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `date_created` $date_default,
  `date_modified` $date_default,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `sequence` int(14) NOT NULL NULL DEFAULT '0',
  `sms_limit` int(14) NOT NULL DEFAULT '0',
  `with_broadcast` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE ".$table_prefix."plan
  ADD PRIMARY KEY (`plan_id`),
  ADD KEY `with_broadcast` (`with_broadcast`);

ALTER TABLE ".$table_prefix."plan
  MODIFY `plan_id` int(14) NOT NULL AUTO_INCREMENT;  
";

$tbl['sms_logs']="
CREATE TABLE IF NOT EXISTS ".$table_prefix."sms_logs (
  `id` int(14) NOT NULL,
  `to_number` varchar(100) NOT NULL DEFAULT '',
  `sms_text` text,
  `provider` varchar(100) NOT NULL DEFAULT '',
  `msg` varchar(255) NOT NULL DEFAULT '',
  `raw` text,
  `date_created` $date_default,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `customer_id` int(14) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE ".$table_prefix."sms_logs
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);
  
ALTER TABLE ".$table_prefix."sms_logs
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;  
";

$tbl['task_history']="
CREATE TABLE IF NOT EXISTS ".$table_prefix."task_history (
  `id` int(14) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT '',
  `remarks` text,
  `task_id` int(14) NOT NULL DEFAULT '0',
  `reason` text,
  `customer_signature` varchar(255) NOT NULL DEFAULT '',
  `notification_viewed` int(1) NOT NULL DEFAULT '2',
  `driver_id` int(14) NOT NULL DEFAULT '0',
  `driver_location_lat` varchar(50) NOT NULL DEFAULT '',
  `driver_location_lng` varchar(50) NOT NULL DEFAULT '',
  `date_created` $date_default,
  `ip_address` varchar(50) NOT NULL DEFAULT '',
  `notes` varchar(255) NOT NULL DEFAULT '',
  `photo_name` varchar(255) NOT NULL DEFAULT '',
  `receive_by` varchar(255) NOT NULL DEFAULT '',
  `signature_base30` text  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE ".$table_prefix."task_history
  ADD PRIMARY KEY (`id`),
  ADD KEY `status` (`status`),
  ADD KEY `driver_id` (`driver_id`),
  ADD KEY `notification_viewed` (`notification_viewed`);

ALTER TABLE ".$table_prefix."task_history
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;
";

$tbl['contacts']="
CREATE TABLE IF NOT EXISTS ".$table_prefix."contacts (
  `contact_id` int(14) NOT NULL,
  `customer_id` int(14) NOT NULL DEFAULT '0',
  `fullname` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `phone` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `address` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `addresss_lat` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `addresss_lng` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `status` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pending',
  `date_created` $date_default,
  `date_modified` $date_default,
  `ip_address` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE ".$table_prefix."contacts
  ADD PRIMARY KEY (`contact_id`),
  ADD KEY `customer_id` (`customer_id`);

ALTER TABLE ".$table_prefix."contacts
  MODIFY `contact_id` int(14) NOT NULL AUTO_INCREMENT;  
";

$tbl['services']="
CREATE TABLE IF NOT EXISTS ".$table_prefix."services (
  `services_id` int(14) NOT NULL,
  `services_parent_id` int(14) NOT NULL DEFAULT '0',
  `sevices_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` text COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `status` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'published',
  `sequence` int(14) NOT NULL DEFAULT '0',
  `date_created` $date_default,
  `date_modified` $date_default,
  `ip_address` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE ".$table_prefix."services
  ADD PRIMARY KEY (`services_id`),
  ADD KEY `services_parent_id` (`services_parent_id`);
   
ALTER TABLE ".$table_prefix."services
  MODIFY `services_id` int(14) NOT NULL AUTO_INCREMENT;
";

/*1.2 new table*/

$tbl['promo_code']="
CREATE TABLE IF NOT EXISTS ".$table_prefix."promo_code (
  `promo_code_id` int(14) NOT NULL,
  `promo_code_name` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `discount` varchar(14) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '',
  `discount_type` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'fixed',
  `expiration` date DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'published',
  `date_created` $date_default,
  `date_modified` $date_default,
  `ip_address` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE ".$table_prefix."promo_code
  ADD PRIMARY KEY (`promo_code_id`),
  ADD KEY `promo_code_id` (`promo_code_id`),
  ADD KEY `promo_code_name` (`promo_code_name`),
  ADD KEY `discoun_type` (`discount_type`),
  ADD KEY `discount` (`discount`),
  ADD KEY `status` (`status`),
  ADD KEY `expiration` (`expiration`);
  
ALTER TABLE ".$table_prefix."promo_code
  MODIFY `promo_code_id` int(14) NOT NULL AUTO_INCREMENT;
";

$tbl['driver_track_location']="
CREATE TABLE IF NOT EXISTS ".$table_prefix."driver_track_location (
  `id` int(14) NOT NULL,
  `customer_id` int(14) NOT NULL DEFAULT '0',
  `driver_id` int(14) NOT NULL DEFAULT '0',
  `latitude` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `longitude` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `altitude` varchar(50) COLLATE utf8_unicode_ci DEFAULT '',
  `accuracy` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `altitudeAccuracy` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `heading` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `speed` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `date_created` $date_default,
  `ip_address` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `track_type` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `date_log` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE ".$table_prefix."driver_track_location
  ADD PRIMARY KEY (`id`);
  
ALTER TABLE ".$table_prefix."driver_track_location
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;  
";

$tbl['push_broadcast']="
CREATE TABLE IF NOT EXISTS ".$table_prefix."push_broadcast (
  `broadcast_id` int(14) NOT NULL,
  `team_id` int(14) NOT NULL DEFAULT '0',
  `customer_id` int(14) NOT NULL DEFAULT '0',
  `push_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `push_message` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `status` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'pending',
  `date_created` $date_default,
  `date_process` $date_default,
  `ip_address` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE ".$table_prefix."push_broadcast
  ADD PRIMARY KEY (`broadcast_id`),
  ADD KEY `team_id` (`team_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `status` (`status`);

ALTER TABLE ".$table_prefix."push_broadcast
  MODIFY `broadcast_id` int(14) NOT NULL AUTO_INCREMENT;  
";

/*1.4*/
$tbl['api_logs']="
CREATE TABLE IF NOT EXISTS ".$table_prefix."api_logs (
  `id` int(14) NOT NULL,
  `map_provider` varchar(100) NOT NULL DEFAULT '',
  `api_functions` varchar(100) NOT NULL DEFAULT '',
  `api_response` text,
  `date_created` $date_default,
  `date_call` date DEFAULT NULL,
  `ip_address` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE ".$table_prefix."api_logs
  ADD PRIMARY KEY (`id`);

ALTER TABLE ".$table_prefix."api_logs
  MODIFY `id` int(14) NOT NULL AUTO_INCREMENT;
";


/*VIEW*/
$tbl['driver_task_view']="
Create OR replace view ".$table_prefix."driver_task_view as
SELECT a.*,
concat(b.first_name,' ',b.last_name) as driver_name,
b.device_id,
b.phone as driver_phone,
b.email as driver_email,
b.device_platform,
b.enabled_push,
b.location_lat as driver_location_lat,
b.location_lng as driver_location_lng,
b.last_login as driver_last_login ,
b.transport_type_id as transport_type ,
e.team_name
	
FROM
".$table_prefix."driver_task a
		
LEFT JOIN ".$table_prefix."driver b
ON
b.driver_id=a.driver_id

left join ".$table_prefix."driver_team e
ON 
e.team_id=a.team_id
";

$tbl['view_task_history']="
Create OR replace view ".$table_prefix."view_task_history as
select 
	a.id as history_id,
	a.task_id,
	DATE_FORMAT(a.date_created,'%Y%m%d%H%i%S') as date_created ,
	b.customer_id		
	
	FROM 
	".$table_prefix."task_history a
	left join ".$table_prefix."driver_task  b
	On
	a.task_id = b.task_id
";
