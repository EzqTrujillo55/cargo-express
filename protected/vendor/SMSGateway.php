<?php
class SMSGateway
{

	public static function sendSMS($to='', $message='')
	{
		if (empty($to)){
			throw new Exception(t("To is required"));
		}
		
		if (empty($message)){
			throw new Exception(t("Message is required"));
		}
		
		$to = str_replace("+",'',$to);
		
		$sms_gateway_username=getOptionA('sms_gateway_username');
		$sms_gateway_password=getOptionA('sms_gateway_password');		
		$sms_gateway_sender=getOptionA('sms_gateway_sender');
		$sms_user_curl=getOptionA('sms_user_curl');
		
		if (empty($sms_gateway_username)){
			throw new Exception(t("SMS Username is required"));
		}
		if (empty($sms_gateway_password)){
			throw new Exception(t("SMS Password is required"));
		}
		if (empty($sms_gateway_sender)){
			throw new Exception(t("SMS Sender is required"));
		}
		
		$url = "http://apps.gateway.sa/vendorsms/pushsms.aspx?user=[user]&password=[password]&msisdn=[to]&sid=[sender]&msg=[message]&fl=0";
		$url = self::smarty('user',$sms_gateway_username,$url);
		$url = self::smarty('password',$sms_gateway_password,$url);
		$url = self::smarty('sender',$sms_gateway_sender,$url);
		$url = self::smarty('to',urlencode($to),$url);
		$url = self::smarty('message',urlencode($message),$url);		
		if($sms_user_curl==1){			
			$resp=self::Curl($url);
		} else $resp=file_get_contents($url);
		
		if(!empty($resp)){
			//$resp='{"ErrorCode":"000","ErrorMessage":"Success","JobId":"184103","MessageData":[{"Number":"+91233434566","MessageParts":[{"MsgId":"+91233434566-03bf247da5ff4d5db7959e557c836861","PartId":1,"Text":"This is a test message"}]}]}';
			$resp_array=json_decode($resp,true);				
			if (is_array($resp_array) && count($resp_array)>=1){				
				if($resp_array['ErrorCode']<=0){
					return $resp_array['JobId'];
				} else throw new Exception( t($resp_array['ErrorMessage'])  );
			} else throw new Exception(t("invalid response from api"));
		} else throw new Exception(t("empty response from api"));
		
		throw new Exception(t("Undefined error"));
	}
	
	public static function smarty($search='',$value='',$subject='')
    {	
	   return str_replace("[".$search."]",$value,$subject);
    }
    
    public static function Curl($uri="",$post="")
	{
		 $error_no='';
		 $ch = curl_init($uri);
		 curl_setopt($ch, CURLOPT_POST, 1);		 
		 curl_setopt($ch, CURLOPT_POSTFIELDS, $post);		 
		 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		 curl_setopt($ch, CURLOPT_HEADER, 0);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		 $resutl=curl_exec ($ch);		
		 		 		 		 
		 if ($error_no==0) {
		 	 return $resutl;
		 } else return false;			 
		 curl_close ($ch);		 				 		 		 		 		 		
	}
} /*end class*/