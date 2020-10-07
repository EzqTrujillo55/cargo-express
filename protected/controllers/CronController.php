<?php

class CronController extends CController {

    public function init() {
        // set website timezone
        $website_timezone = Yii::app()->functions->getOptionAdmin("website_timezone");
        if (!empty($website_timezone)) {
            Yii::app()->timeZone = $website_timezone;
        }
    }

    public function actionIndex() {
        
    }

    public function actionProcessPush() {
        $db = new DbExt;
        $status = '';

        $ring_tone_filename = 'beep';
        $api_key = Yii::app()->functions->getOptionAdmin('push_api_key');

        $driver_ios_push_mode = getOptionA('ios_mode');
        $driver_ios_pass_phrase = getOptionA('ios_password');
        $driver_ios_push_dev_cer = getOptionA('ios_dev_certificate');
        $driver_ios_push_prod_cer = getOptionA('ios_prod_certificate');

        $DriverIOSPush = new DriverIOSPush;
        $DriverIOSPush->pass_prase = $driver_ios_pass_phrase;
        $DriverIOSPush->dev_certificate = $driver_ios_push_dev_cer;
        $DriverIOSPush->prod_certificate = $driver_ios_push_prod_cer;

        $production = $driver_ios_push_mode == "production" ? true : false;

        $push_server_key = getOptionA('fcm_server_key');
        $channel = 'kartero';

        $stmt = "
		SELECT a.*,
		b.app_version
		FROM
		{{driver_pushlog}} a
		
		left join {{driver}} b
		On
		a.driver_id = b.driver_id
		
		WHERE
		a.status='pending'
		ORDER BY a.date_created ASC
		LIMIT 0,10
		";
        if ($res = $db->rst($stmt)) {
            foreach ($res as $val) {

                if (isset($_GET['debug'])) {
                    dump($val);
                }

                $push_id = $val['push_id'];
                $device_platform = strtolower($val['device_platform']);
                $device_id = trim($val['device_id']);
                $app_version = isset($val['app_version']) ? trim($val['app_version']) : '';
                $device_id = trim($val['device_id']);

                dump("device platform $device_platform");
                dump("app version=>" . $app_version);
                dump($device_id);

                if (!empty($device_id)) {
                    if ($app_version >= 1.4) {
                        /* FIREBASE PUSH */

                        switch ($device_platform) {
                            case "android":

                                $data = array(
                                    'title' => $val['push_title'],
                                    'body' => $val['push_message'],
                                    'vibrate' => 1,
                                    'soundname' => $ring_tone_filename,
                                    'android_channel_id' => $channel,
                                    'content-available' => 1,
                                    'count' => 1,
                                    'badge' => 1,
                                    'push_type' => $val['push_type'],
                                    'actions' => $val['actions'],
                                );

                                if (isset($_GET['debug'])) {
                                    dump($data);
                                }

                                if (!empty($push_server_key)) {
                                    try {
                                        $resp = DriverFCMPush::pushAndroid($data, $device_id, $push_server_key);
                                        $status = 'process';
                                    } catch (Exception $e) {
                                        $status = 'Caught exception:' . $e->getMessage();
                                    }
                                } else
                                    $status = 'server key is empty';

                                break;

                            case "ios":

                                try {
                                    $data = array(
                                        'title' => $val['push_title'],
                                        'body' => $val['push_message'],
                                        'sound' => 'beep.wav',
                                        'android_channel_id' => $channel,
                                        'badge' => 1,
                                        'content-available' => 1,
                                        'push_type' => $val['push_type'],
                                        'actions' => $val['actions'],
                                    );
                                    if (isset($_GET['debug'])) {
                                        dump($data);
                                    }
                                    $resp = DriverFCMPush::pushIOS($data, $device_id, $push_server_key);
                                    $status = 'process';
                                } catch (Exception $e) {
                                    $status = $e->getMessage();
                                }

                                break;
                        }
                    } else {
                        /* LEGACY PUSH */
                        if (!empty($api_key)) {

                            $message = array(
                                'title' => $val['push_title'],
                                'message' => $val['push_message'],
                                'soundname' => $ring_tone_filename,
                                'count' => 1,
                                'content-available' => 1,
                                'push_type' => $val['push_type'],
                                'actions' => $val['actions'],
                            );

                            switch ($device_platform) {
                                case "android":
                                    $resp = AndroidPush::sendPush($api_key, $$device_id, $message);
                                    if (is_array($resp) && count($resp) >= 1) {
                                        if ($resp['success'] > 0) {
                                            $status = "process";
                                        } else
                                            $status = $resp['results'][0]['error'];
                                    } else
                                        $status = "uknown push response";
                                    break;

                                case"ios":

                                    $aps_body['aps'] = array(
                                        'alert' => $val['push_message'],
                                        'sound' => "www/beep.wav",
                                        'badge' => (integer) 1,
                                        'push_type' => $val['push_type'],
                                        'actions' => $val['actions'],
                                    );

                                    if ($DriverIOSPush->push($val['push_message'], $val['device_id'], $production, $aps_body)) {
                                        $status = "process";
                                    } else
                                        $status = $DriverIOSPush->get_msg();

                                    break;

                                default:
                                    $status = "Uknown device";
                                    break;
                            }
                        } else
                            $status = "API key is empty";
                    }
                } else
                    $status = "Device id is empty";

                $params = array(
                    'status' => $status,
                    'date_process' => AdminFunctions::dateNow(),
                    'json_response' => isset($resp) ? json_encode($resp) : '',
                    'ip_address' => $_SERVER['REMOTE_ADDR']
                );

                if (isset($_GET['debug'])) {
                    dump($params);
                }

                $db->updateData("{{driver_pushlog}}", $params, 'push_id', $push_id);
            }
        } else {
            if (isset($_GET['debug'])) {
                echo 'no record to process';
            }
        }
    }

    public function actionGeneraDashboardUser() {
        //$date = date("Y-m-d 23:59:00", strtotime("-7 days"));

        echo("Inicio proceso GeneraDashboardUser ");
        echo(date("h:i:sa"));
        $db = new DbExt;
        $stmtUsers = "SELECT * from {{users}} ";
        if ($res = $db->rst($stmtUsers)) {
            foreach ($res as $val) {
                $stmtdelete = " DELETE from {{estadisticas_user}} where id_user=" . Driver::q($val['id']);
                $db->qry($stmtdelete);
                $stmtcantidadOfertasPerfil = "SELECT COUNT(u.id) as total FROM 
                                {{oferta_ocasional}} AS u 
                                WHERE 
                                 MATCH(u.solicito, u.descripcion)
                                 AGAINST('" . $val['descripcion'] . "' IN NATURAL LANGUAGE MODE) 
                                 AND u.estado=TRUE ";
                $rescantidadOfertasPerfil = $db->rst($stmtcantidadOfertasPerfil);

                $stmtcantidadOfertasAplicadas = "SELECT COUNT(u.id) as total FROM 
                                {{aplica_oferta}} AS u where u.id_user=" . Driver::q($val['id']);
                $rescantidadOfertasAplicadas = $db->rst($stmtcantidadOfertasAplicadas);

                $stmtcantidadOfertasPublicadas = "SELECT COUNT(u.id) as total FROM 
                                {{oferta_ocasional}} AS u where u.id_user=" . Driver::q($val['id']);
                $rescantidadOfertasPublicadas = $db->rst($stmtcantidadOfertasPublicadas);


                $stmtInsert = "  INSERT INTO {{estadisticas_user}} (id_user, cantidad_ofertas_perfil, cantidad_ofertas_aplicadas,cantidad_ofertas_publicadas,cantidad_ofertas_trabajadas)
                                   values( " . Driver::q($val['id']) . ", " . $rescantidadOfertasPerfil[0]['total'] . ", " . $rescantidadOfertasAplicadas[0]['total'] . ", " . $rescantidadOfertasPublicadas[0]['total'] . ",0)";


                if (isset($_GET['debug'])) {
                    dump($stmtInsert);
                }
                $db->qry($stmtInsert);
            }
        }
        echo("Fin proceso GeneraDashboardUser ");
        echo(date("h:i:sa"));
    }

    public function actionAutoAssign() {
        $db = new DbExt;
        $distance_exp = 3959;
        $radius = 3000;

        $date_now = date('Y-m-d');

        $stmt = "SELECT a.*,
		b.enabled_auto_assign,
		b.include_offline_driver,
		b.autoassign_notify_email,
		b.request_expire,
		b.auto_assign_type,
		b.assign_request_expire,
		b.driver_assign_radius
		
		 FROM
		{{driver_task}} a
		LEFT JOIN {{customer}} b
        ON
        a.customer_id=b.customer_id
		WHERE 1
		AND a.status IN ('unassigned')  
		AND a.auto_assign_type=''
		AND a.delivery_date like '$date_now%'	
		AND b.enabled_auto_assign='1'	
		ORDER BY task_id ASC
		LIMIT 0,10
		";

        if (isset($_GET['debug'])) {
            dump($stmt);
        }

        if ($res = $db->rst($stmt)) {
            foreach ($res as $val) {

                if (isset($_GET['debug'])) {
                    dump($val);
                }

                if ($val['driver_assign_radius'] > 0) {
                    $radius = $val['driver_assign_radius'];
                }

                $notify_email = $val['autoassign_notify_email'];

                $lat = $val['task_lat'];
                $lng = $val['task_lng'];
                $task_id = $val['task_id'];

                if (isset($_GET['debug'])) {
                    dump($lat);
                    dump($lng);
                    dump($task_id);
                }

                $and = '';
                $todays_date = date('Y-m-d');
                $time_now = time() - 600;

                $limit = "LIMIT 0,100";

                $assignment_status = "waiting for driver acknowledgement";

                if ($val['include_offline_driver'] == "" || $val['include_offline_driver'] == "0") {
                    $and .= " AND a.on_duty ='1' ";
                    $and .= " AND a.last_online >='$time_now' ";
                    $and .= " AND a.last_login like '" . $todays_date . "%'";
                }

                if ($val['auto_assign_type'] == "one_by_one") {

                    if (isset($_GET['debug'])) {
                        dump("one_by_one");
                    }

                    $and .= " AND a.driver_id NOT IN (
					  select driver_id
					  from
					  {{driver_assignment}}
					  where
					  driver_id=a.driver_id
					  and
					  task_id=" . Driver::q($task_id) . "
					) ";

                    $stmt2 = "
					SELECT a.driver_id, a.customer_id,
					 a.first_name,a.last_name,a.location_lat,a.location_lng,
					a.on_duty, a.last_online, a.last_login
					, 
					( $distance_exp * acos( cos( radians($lat) ) * cos( radians( location_lat ) ) 
			        * cos( radians( location_lng ) - radians($lng) ) 
			        + sin( radians($lat) ) * sin( radians( location_lat ) ) ) ) 
			        AS distance
			        FROM {{driver}} a
			        HAVING distance < $radius
					AND a.customer_id=" . Driver::q($val['customer_id']) . " 
					$and
					ORDER BY distance ASC
					$limit
					";
                } else {

                    if (isset($_GET['debug'])) {
                        dump("send_to_all");
                    }

                    $and .= " AND a.driver_id NOT IN (
					  select driver_id
					  from
					  {{driver_assignment}}
					  where
					  driver_id=a.driver_id
					  and
					  task_id=" . Driver::q($task_id) . "					  
					) ";

                    $stmt2 = "SELECT a.* FROM {{driver}} a		
					WHERE 1
					AND customer_id=" . Driver::q($val['customer_id']) . " 
					$and			
					";
                }

                if (isset($_GET['debug'])) {
                    dump($stmt2);
                }
                if ($res2 = $db->rst($stmt2)) {
                    foreach ($res2 as $val2) {
                        $params = array(
                            'auto_assign_type' => $val['auto_assign_type'],
                            'task_id' => $val['task_id'],
                            'driver_id' => $val2['driver_id'],
                            'first_name' => $val2['first_name'],
                            'last_name' => $val2['last_name'],
                            'date_created' => AdminFunctions::dateNow(),
                            'ip_address' => $_SERVER['REMOTE_ADDR']
                        );
                        echo "<h3>driver_assignment</h3>";

                        if (isset($_GET['debug'])) {
                            dump($params);
                        }
                        $db->insertData("{{driver_assignment}}", $params);
                    }
                } else {
                    // unable to assign
                    $assignment_status = "unable to auto assign";
                    if (!empty($val['autoassign_notify_email'])) {
                        $email_enabled = getOption($val['customer_id'], 'FAILED_AUTO_ASSIGN_EMAIL');
                        if ($email_enabled) {
                            $tpl = getOption($val['customer_id'], 'FAILED_AUTO_ASSIGN_EMAIL_TPL');
                            $tpl = Driver::smarty('TaskID', $task_id, $tpl);
                            $tpl = Driver::smarty('CompanyName', getOptionA('website_title'), $tpl);

                            if (isset($_GET['debug'])) {
                                dump($tpl);
                            }
                            sendEmail($notify_email, "", "Unable to auto assign Task $task_id", $tpl);
                        }
                    }
                }

                $less = "-1";
                if ($val['assign_request_expire'] > 0) {
                    $less = "-" . $val['assign_request_expire'];
                }

                $params_task = array(
                    'auto_assign_type' => $val['auto_assign_type'],
                    //'assign_started'=>date('c',strtotime("$less min")),
                    'assign_started' => date('Y-m-d G:i:s', strtotime("$less min")),
                    'assignment_status' => $assignment_status
                );

                if (isset($_GET['debug'])) {
                    dump($params_task);
                }
                $db->updateData("{{driver_task}}", $params_task, 'task_id', $task_id);
            } /* end foreach */
        } else {
            if (isset($_GET['debug'])) {
                echo 'no record to process';
            }
        }
    }

    public function actionProcessAutoAssign() {
        $and = '';

        $and .= "AND task_id IN (
		  select task_id from {{driver_assignment}}
		  where
		  task_id=a.task_id
		  and
		  status='pending'		  
		)";

        $db = new DbExt;
        $stmt = "SELECT a.*,
		b.enabled_auto_assign,
		b.include_offline_driver,
		b.autoassign_notify_email,
		b.request_expire,
		b.auto_assign_type,
		b.assign_request_expire
		FROM
		{{driver_task}} a
		
		LEFT JOIN {{customer}} b
        ON
        a.customer_id=b.customer_id
        
		WHERE 1
		AND a.status IN ('unassigned') 
		$and				
		ORDER BY task_id ASC
		LIMIT 0,10
		";

        if (isset($_GET['debug'])) {
            dump($stmt);
        }

        if ($res = $db->rst($stmt)) {
            foreach ($res as $val) {

                if (isset($_GET['debug'])) {
                    dump($val);
                }

                $task_id = $val['task_id'];
                $assign_type = $val['auto_assign_type'];
                $assign_started = date("Y-m-d g:i:s a", strtotime($val['assign_started']));
                $date_now = date('Y-m-d g:i:s a');
                $request_expire = $val['assign_request_expire'];

                if ($assign_type == "one_by_one") {

                    if (isset($_GET['debug'])) {
                        dump("one_by_one");
                    }

                    $time_diff = Yii::app()->functions->dateDifference($assign_started, $date_now);

                    if (isset($_GET['debug'])) {
                        dump($time_diff);
                    }
                    if (is_array($time_diff) && count($time_diff) >= 1) {
                        if ($time_diff['hours'] > 0 || $time_diff['minutes'] >= $request_expire) {
                            if ($driver = Driver::getUnAssignedDriver($task_id)) {

                                $params['assignment_status'] = "waiting for" . " " . $driver['first_name'] .
                                        " " . $driver['last_name'] . " " . "to acknowledge";

                                $assigment_id = $driver['assignment_id'];
                                $params_driver = array('status' => 'process', 'date_process' => AdminFunctions::dateNow());

                                if (isset($_GET['debug'])) {
                                    dump($params_driver);
                                }
                                $db->updateData('{{driver_assignment}}', $params_driver, 'assignment_id', $assigment_id);

                                $task_info = Driver::getTaskByDriverNTask($task_id, $driver['driver_id']);
                                Driver::sendDriverNotification('ASSIGN_TASK', $task_info);
                            }

                            $params['assign_started'] = AdminFunctions::dateNow();

                            if (isset($_GET['debug'])) {
                                dump($params);
                            }
                            $db->updateData("{{driver_task}}", $params, 'task_id', $task_id);
                        } else
                            echo "Not request $request_expire a";
                    } else
                        echo "Not request $request_expire b";
                } else {

                    if (isset($_GET['debug'])) {
                        dump("send_to_all");
                    }
                    if ($res2 = Driver::getUnAssignedDriver2($task_id)) {
                        foreach ($res2 as $val2) {

                            if (isset($_GET['debug'])) {
                                dump($val2);
                            }
                            $assigment_id = $val2['assignment_id'];
                            $params_driver = array('status' => 'process', 'date_process' => AdminFunctions::dateNow());

                            if (isset($_GET['debug'])) {
                                dump($params_driver);
                            }
                            $db->updateData('{{driver_assignment}}', $params_driver, 'assignment_id', $assigment_id);

                            $task_info = Driver::getTaskByDriverNTask($val2['task_id'], $val2['driver_id']);
                            Driver::sendDriverNotification('ASSIGN_TASK', $task_info);
                        }

                        $params = array();
                        $params['assign_started'] = AdminFunctions::dateNow();

                        if (isset($_GET['debug'])) {
                            dump($params);
                        }
                        $db->updateData("{{driver_task}}", $params, 'task_id', $task_id);
                    }
                }
            }
        } else {
            if (isset($_GET['debug'])) {
                echo 'No results';
            }
        }
    }

    public function actionCheckAutoAssign() {
        $db = new DbExt;

        $stmt = "SELECT a.*,
		b.enabled_auto_assign,
		b.include_offline_driver,
		b.autoassign_notify_email,
		b.request_expire,
		b.auto_assign_type,
		b.assign_request_expire,
		b.auto_retry_assigment
		
		 FROM
		{{driver_task}} a
		
		LEFT JOIN {{customer}} b
        ON
        a.customer_id=b.customer_id
        
		WHERE 1
		AND a.status IN ('unassigned') 	
		AND a.auto_assign_type IN ('one_by_one','send_to_all')	
		AND a.assignment_status NOT IN ('','unable to auto assign')
		AND a.task_id NOT IN (
		  select task_id from {{driver_assignment}}
		  where
		  task_id=a.task_id
		  and
		  status='pending'  
		)
		ORDER BY a.task_id ASC
		LIMIT 0,10
		";
        if (isset($_GET['debug'])) {
            dump($stmt);
        }
        if ($res = $db->rst($stmt)) {
            foreach ($res as $val) {
                if (isset($_GET['debug'])) {
                    dump($val);
                }
                $task_id = $val['task_id'];
                $assign_type = $val['auto_assign_type'];
                $assign_started = date("Y-m-d g:i:s a", strtotime($val['assign_started']));
                $request_expire = $val['request_expire'];
                $date_now = date('Y-m-d g:i:s a');
                $notify_email = $val['autoassign_notify_email'];

                if (isset($_GET['debug'])) {
                    dump("TASK ID :" . $task_id);
                    dump("expire in :" . $request_expire);
                    dump($assign_type);
                    dump("assign_started :" . $assign_started);
                    dump("date now : " . $date_now);
                }

                if (!is_numeric($request_expire)) {
                    $request_expire = 1;
                }

                $time_diff = Yii::app()->functions->dateDifference($assign_started, $date_now);
                if (is_array($time_diff) && count($time_diff) >= 1) {
                    if (isset($_GET['debug'])) {
                        dump($time_diff);
                    }
                    if ($time_diff['hours'] > 0 || $time_diff['minutes'] >= $request_expire) {

                        $params = array('assignment_status' => "unable to auto assign");

                        if (isset($_GET['debug'])) {
                            dump($params);
                        }
                        $db->updateData("{{driver_task}}", $params, 'task_id', $task_id);

                        /* $stmt_assign="
                          UPDATE {{driver_assignment}}
                          SET task_status='unable to auto assign'
                          WHERE
                          task_id=".Driver::q($task_id)."
                          ";
                          dump($stmt_assign);
                          $db->qry($stmt_assign);
                         */

                        if ($res2 = Driver::getUnAssignedDriver3($task_id)) {
                            foreach ($res2 as $val2) {

                                $assigment_id = $val2['assignment_id'];
                                $params_driver = array('task_status' => 'unable to auto assign',
                                    'date_process' => AdminFunctions::dateNow());

                                dump($params_driver);
                                $db->updateData('{{driver_assignment}}', $params_driver, 'assignment_id', $assigment_id);

                                $task_info = Driver::getTaskByDriverNTask($val2['task_id'], $val2['driver_id']);
                                Driver::sendDriverNotification('CANCEL_TASK', $task_info);
                            }
                        }

                        if (!empty($notify_email)) {

                            if (isset($_GET['debug'])) {
                                dump($notify_email);
                            }
                            $email_enabled = getOption($val['customer_id'], 'FAILED_AUTO_ASSIGN_EMAIL');

                            if (isset($_GET['debug'])) {
                                dump($email_enabled);
                            }
                            if ($email_enabled) {
                                $tpl = getOption($val['customer_id'], 'FAILED_AUTO_ASSIGN_EMAIL_TPL');
                                $tpl = Driver::smarty('TaskID', $task_id, $tpl);
                                $tpl = Driver::smarty('CompanyName', getOptionA('website_title'), $tpl);

                                if (isset($_GET['debug'])) {
                                    dump($tpl);
                                }
                                sendEmail($notify_email, "", "Unable to auto assign Task $task_id", $tpl);
                            }
                        }

                        /* retry auto assign */
                        if ($val['auto_retry_assigment'] == 1) {
                            Driver::retryAutoAssign($task_id);
                        }
                    }
                }
            } /* end foreach */
        } else {
            if (isset($_GET['debug'])) {
                echo "No results";
            }
        }
    }

    public function actionCheckCustomerExpiry() {
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

    public function actionProcessBroadcast() {
        $db = new DbExt;
        $stmt = "
    	SELECT * FROM
    	{{push_broadcast}}
    	WHERE
    	status='pending'
    	ORDER BY broadcast_id ASC
    	LIMIT 0,2
    	";

        $date_created = AdminFunctions::dateNow();
        $ip_address = $_SERVER['REMOTE_ADDR'];

        if ($res = $db->rst($stmt)) {
            foreach ($res as $val) {
                $broadcast_id = $val['broadcast_id'];
                $push_title = $val['push_title'];
                $push_message = $val['push_message'];

                $stmt_insert = "
    			INSERT INTO {{driver_pushlog}}(
    			   customer_id,
    			   device_platform,
    			   device_id,
    			   push_title,
    			   push_message,
    			   push_type,
    			   actions,
    			   broadcast_id,
    			   date_created,
    			   ip_address,
    			   driver_id
    			)
    			
    			SELECT 
    			customer_id,
    			device_platform,
    			device_id,
    			" . Driver::q($push_title) . ",
    			" . Driver::q($push_message) . ",
    			'campaign',
    			'private',    			
    			'$broadcast_id',
    			'$date_created',
    			'$ip_address',
    			driver_id
    			FROM {{driver}}
    			WHERE
    			team_id=" . Driver::q($val['team_id']) . "
    			";
                if (isset($_GET['debug'])) {
                    dump($stmt_insert);
                }
                $db->qry($stmt_insert);

                $db->updateData("{{push_broadcast}}", array(
                    'status' => "process",
                    'date_process' => AdminFunctions::dateNow()
                        ), 'broadcast_id', $broadcast_id);
            }
        }
    }

    public function actionClearAgentTracking() {
        $date = date("Y-m-d 23:59:00", strtotime("-7 days"));
        $db = new DbExt;
        $stmt = "
    	DELETE FROM
    	{{driver_track_location}}
    	WHERE 
    	date_created <=" . Driver::q($date) . "
    	";
        if (isset($_GET['debug'])) {
            dump($stmt);
        }
        $db->qry($stmt);
    }

    public function actionRunAllCron() {
        Driver::fastRequest(Driver::getHostURL() . Yii::app()->createUrl("cron/processpush"));
        Driver::fastRequest(Driver::getHostURL() . Yii::app()->createUrl("cron/autoassign"));
        Driver::fastRequest(Driver::getHostURL() . Yii::app()->createUrl("cron/processautoassign"));
        Driver::fastRequest(Driver::getHostURL() . Yii::app()->createUrl("cron/checkautoassign"));
        Driver::fastRequest(Driver::getHostURL() . Yii::app()->createUrl("cron/generadashboarduser"));
    }

}

/*end class*/