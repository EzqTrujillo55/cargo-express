<?php
class DriverFCMPush
{
	
	public static function pushAndroid($data=array(), $device_id='', $server_key='')
	{
		$registrationIds = array( $device_id );
				
		$fields = array(
			'registration_ids' 	=> $registrationIds,
			'data' => $data
		);
		
		//dump($fields);
		
		$headers = array (
			'Authorization: key=' . $server_key,
			'Content-Type: application/json'
		);
		
		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
		$result = curl_exec($ch );
		if(curl_errno($ch)){		   
		   throw new Exception(curl_error($ch));
		}
		curl_close( $ch );		
		//dump($result);
		$json = json_decode($result,true);		
		//dump($json);
		if(is_array($json) && count($json)>=1){			
			if($json['success']==1){
				return $json;
			} else {
				$error = 'undefined error';
				if (is_array($json['results']) && count($json['results'])>=1){
					$error='';
					foreach ($json['results'] as $val) {
						$error.="$val[error]\n";
					}
				}
				throw new Exception($error);
			}
		} else {
		   throw new Exception("invalid response: ".$result);
		}		
	}
	
	public static function pushIOS($data=array(), $device_id='', $server_key='')
	{
		$registrationIds = array( $device_id );
				
		$fields = array(
		   'to' => $device_id, 
	       'notification' => $data,
	       'priority'=>'high',
	       'content_available' => true,     
		);
		//dump($fields);
		
		$headers = array (
			'Authorization: key=' . $server_key,
			'Content-Type: application/json'
		);
		
		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
		$result = curl_exec($ch );
		if(curl_errno($ch)){		   
		   throw new Exception(curl_error($ch));
		}
		curl_close( $ch );		
		//dump($result);
		$json = json_decode($result,true);		
		//dump($json);
		if(is_array($json) && count($json)>=1){			
			if($json['success']==1){
				return $json;
			} else {
				$error = 'undefined error';
				if (is_array($json['results']) && count($json['results'])>=1){
					$error='';
					foreach ($json['results'] as $val) {
						$error.="$val[error]\n";
					}
				}
				throw new Exception($error);
			}
		} else {
		   throw new Exception("invalid response: ".$result);
		}		
	}
		
} /*end class*/