<?php
$db=new DbExt;

if(isset($_SESSION['kt_export_stmt'])){
	$stmt=$_SESSION['kt_export_stmt'];	
	
	$pos=strpos($stmt,"LIMIT");
	$stmt=substr($stmt,0,$pos);
		
	//dump($stmt);
	$feed_data=array();
	$filename=isset($_GET['filename'])?$_GET['filename']:'';
	
	switch ($filename) {
		
		case "customer_signup":
			
			$header=array(
		     t("ID"),
		     t("Name"),
		     t("Mobile number"),
		     t("Email address"),	    			    
		     t("Plan"),
		     t("Status"),
		     t("Date"),
		    );
		    			
			if($res=$db->rst($stmt)){
				foreach ($res as $val) {
					$feed_data[]=array(
					  $val['customer_id'],
					  $val['first_name']." ".$val['last_name'],
					  $val['mobile_number'],
					  $val['email_address'],
					  $val['plan_name'],				  
					  $val['status'],
					  AdminFunctions::prettyDate($val['date_created'])				  
					);
				}								
			}
			$filename = $filename.'-'. date('Ymd') .'.csv';    	    
	    	$excel  = new ExcelFormat($filename);
	    	$excel->addHeaders($header);
            $excel->setData($feed_data);	  
            $excel->prepareExcel();	
            Yii::app()->end();                     
			break;
	
		case "sales_report":	
		
		    $header=array(
		     t("Date"),
		     t("Trans Type"),
		     t("Payment Provider"),
		     t("Memo"),	    			    
		     t("Total"),
		     t("Transaction Ref")		     
		    );
		    			
			if($res=$db->rst($stmt)){
				foreach ($res as $val) {
					$feed_data[]=array(
					   AdminFunctions::prettyDate($val['date_created']),
					  $val['transaction_type'],
					  AdminFunctions::prettyGateway($val['payment_provider']),
					  $val['memo'],
					  prettyPrice($val['total_paid']),
					  $val['transaction_ref'],
					);
				}								
			}
			$filename = $filename.'-'. date('Ymd') .'.csv';    	    
	    	$excel  = new ExcelFormat($filename);
	    	$excel->addHeaders($header);
            $excel->setData($feed_data);	  
            $excel->prepareExcel();	
            Yii::app()->end();                     
			break;
		   
		case "sms_logs":	
		
		   $header=array(
		     t("Date"),
		     t("Mobile number"),
		     t("SMS"),
		     t("Provider"),	    			    
		     t("Status"),		     
		    );
		    			
			if($res=$db->rst($stmt)){
				foreach ($res as $val) {
					$feed_data[]=array(
					   AdminFunctions::prettyDate($val['date_created']),
				       $val['to_number'],
				       $val['sms_text'],
				       $val['provider'],
				       $val['msg'],
					);
				}								
			}
			$filename = $filename.'-'. date('Ymd') .'.csv';    	    
	    	$excel  = new ExcelFormat($filename);
	    	$excel->addHeaders($header);
            $excel->setData($feed_data);	  
            $excel->prepareExcel();	
            Yii::app()->end();                     
			break;
			
		case "email_logs":	
		
		   $header=array(
		     t("Date"),
		     t("Email address"),
		     t("Subject"),
		     t("Content"),	    			    
		     t("Status"),		     
		    );
		    			
			if($res=$db->rst($stmt)){
				foreach ($res as $val) {
					$feed_data[]=array(
					      AdminFunctions::prettyDate($val['date_created']),
						  $val['email_address'],
						  $val['subject'],
						  $val['content'],
						  $val['status']
					);
				}								
			}
			$filename = $filename.'-'. date('Ymd') .'.csv';    	    
	    	$excel  = new ExcelFormat($filename);
	    	$excel->addHeaders($header);
            $excel->setData($feed_data);	  
            $excel->prepareExcel();	
            Yii::app()->end();                     
			break;
		
		case "push_logs":	
		
		   $header=array(
		     t("Date"),
		     t("Device"),
		     t("Device ID"),
		     t("Push Title"),	    			    
		     t("Content"),	    			    
		     t("Type"),	    			    
		     t("Status"),		     
		    );
		    			
			if($res=$db->rst($stmt)){
				foreach ($res as $val) {
					$feed_data[]=array(
					      AdminFunctions::prettyDate($val['date_created']),
						  $val['device_platform'],
						  $val['device_id'],
						  $val['push_title'],
						  $val['push_message'],
						  $val['push_type'],
						  $val['status']
					);
				}								
			}
			$filename = $filename.'-'. date('Ymd') .'.csv';    	    
	    	$excel  = new ExcelFormat($filename);
	    	$excel->addHeaders($header);
            $excel->setData($feed_data);	  
            $excel->prepareExcel();	
            Yii::app()->end();                     
			break;
		
		default:
			break;
	}
}