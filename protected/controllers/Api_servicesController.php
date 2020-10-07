<?php
class Api_servicesController extends CController
{	
	public $data;
	public $code=2;
	public $msg='';
	public $details='';
	
	public function __construct()
	{
		$this->data=$_GET;
		
		$website_timezone=Yii::app()->functions->getOptionAdmin("website_timezone");		 
	    if (!empty($website_timezone)){
	 	   Yii::app()->timeZone=$website_timezone;
	    }		 
	    	    
	}
	
	public function beforeAction($action)
	{
		$api_services_key=getOptionA('api_services_key');
		if(!empty($api_services_key)){
		    $keys = isset($this->data['keys'])?$this->data['keys']:'';
			if($api_services_key!=$keys){
				$this->msg=t("api services key is not valid");
				$this->output();
				Yii::app()->end();
			}
		} else {
			$this->msg=t("api services key is empty in your settings");
			$this->output();
			Yii::app()->end();
		}
		return true;
	}
	
	private function output()
    {
       if (!isset($this->data['debug'])){        	
       	  header('Access-Control-Allow-Origin: *');
          header('Content-type: application/json');
       } 
       
	   $resp=array(
	     'code'=>$this->code,
	     'msg'=>$this->msg,
	     'details'=>$this->details,
	     'request'=>json_encode($this->data)		  
	   );		   
	   if (isset($this->data['debug'])){
	   	   dump($resp);
	   }
	   
	   if (!isset($_GET['callback'])){
  	   	   $_GET['callback']='';
	   }    
	   
	   if (isset($_GET['json']) && $_GET['json']==TRUE){
	   	   echo CJSON::encode($resp);
	   } else echo $_GET['callback'] . '('.CJSON::encode($resp).')';		    	   	   	  
	   Yii::app()->end();
    }		
	
	public function actioninsert_task()
	{
		$validator = new Validator;
		$req = array(
		  'merchant_token'=>t("Merchant token is required"),
		  'trans_type'=>t("Transaction type is required"),
		  'email_address'=>t("Email address is required"),
		  'delivery_date'=>t("Delivery date is required"),
		  'delivery_address'=>t("Delivery address is required"),		  
		  'customer_name'=>t("Customer name is required")
		);
		
		$email = array(
		  'email_address'=>t("Invalid email address")
		);
		$validator->email($email,$this->data);
		
		if(isset($this->data['delivery_date'])){
			$delivery_date=$this->data['delivery_date'];
			$delivery_date=explode("-",$delivery_date);
			if(count($delivery_date)!=3){
				$validator->msg[]=t("Invalid delivery date");
			}
		}
		
		if(isset($this->data['trans_type'])){
			$trans_type = isset($this->data['trans_type'])?$this->data['trans_type']:'';			
			$allowed_type = Driver::transactionType();			
			if(!in_array($trans_type,$allowed_type)){
				$validator->msg[]=t("Invalid transaction type");
			}			
		}
		
		$validator->required($req,$this->data);
		if ($validator->validate()){
			$merchant_token = trim($this->data['merchant_token']);			
			if($merchant_info = FrontFunctions::getCustomerByToken($merchant_token)){
				
				$customer_id = $merchant_info['customer_id'];
								
				if(!Driver::planCheckCAnAddTask( $customer_id , $merchant_info['plan_id'] )){
					$this->msg=t("You cannot add more task you account is restrict to add new task");
					$this->output();
				}
								
				$params=array(
				  'customer_id'=>$customer_id,
				  'task_description'=>trim($this->data['task_description']),
				  'trans_type'=>trim($this->data['trans_type']),
				  'contact_number'=>trim($this->data['contact_number']),
				  'email_address'=>trim($this->data['email_address']),
				  'customer_name'=>trim($this->data['customer_name']),
				  'delivery_date'=>trim($this->data['delivery_date']),
				  'delivery_address'=>trim($this->data['delivery_address']),
				  'date_created'=>AdminFunctions::dateNow(),
				  'task_token'=>Driver::generateTaskToken()
				);
				if(isset($this->data['task_lat']) && isset($this->data['task_lng'])){
					$params['task_lat']=trim($this->data['task_lat']);
					$params['task_lng']=trim($this->data['task_lng']);
				} else {
					if ($res_location = Driver::addressToLatLong($this->data['delivery_address'])){
						$params['task_lat']=$res_location['lat'];
					    $params['task_lng']=$res_location['long'];
					}
				}				
				
				$db = new DbExt();
				if ($db->insertData("{{driver_task}}",$params)){
					$this->code = 1;
					$this->msg=t("Successful");
					$this->details=array(
					  'task_token'=>$params['task_token']
					);
				} else $this->msg = t("Cannot insert records please try again later");
				
			} else $this->msg = t("Invalid merchant token");
		} else $this->msg = $validator->getError();
		$this->output();
	}
	
}
/*end class*/