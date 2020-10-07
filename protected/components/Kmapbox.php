<?php
class Kmapbox
{
	static $mapbox_key;
	static $matrix_url='https://api.mapbox.com/directions-matrix/v1/mapbox';
	
	
	public static function getMatrix($lat, $long='' , $lat2='', $long2='', $transport_type='')
	{		
		$mapbox_access_token=getOptionA('mapbox_access_token');
		self::$mapbox_key = $mapbox_access_token;
						
		$distance_type='';
		switch ($transport_type) {
			case "truck":
			case "car":	
				$distance_type='driving';
				break;
				
			case "bike":
			case "bicycle":		
			case "scooter":
				$distance_type='cycling';
				break;
				
			case "walk":
				$distance_type='walking';
				break;	
		
			default:
				$distance_type='driving';
				break;
		}
		
		$api_url = self::$matrix_url."/$distance_type/$long,$lat;$long2,$lat2";
		
		
		$qry_str="?annotations=duration&access_token=".urlencode(self::$mapbox_key);
		/*dump($api_url);
		dump($qry_str);*/
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $api_url . $qry_str);  		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        
        $content = trim(curl_exec($ch));
        if(curl_errno($ch)){	     
	       throw new Exception( 'error:' . curl_error($ch) );
	    }        		 	   
        curl_close($ch); 
        //dump($content);
        
        Driver::logsApiCall('matrix','mapbox',json_encode($content));
        
        if(!empty($content)){
        	$json = json_decode($content,true);        	
        	if(is_array($json) && count($json)>=1){         		
        		if(isset($json['code'])){
        			if(strtolower($json['code'])=="ok"){
        				$duration = '';
        				foreach ($json['durations'] as $val_duration) {        					
        					if(!empty($val_duration[0])){
        						$duration=$val_duration[0];
        					} else $duration=$val_duration[1];
        					
        					if(!empty($duration)){
        					    break;
        					}
        				}        				
        				$time='';
        				if(!empty($duration)){
        					$time = self::secondsToTime($duration);
        					return $time;
        				} else throw new Exception( "not valid duration" );
        				
        			} else throw new Exception( "failed response" );
        		} else {
        			if(isset($json['message'])){
        				throw new Exception( $json['message'] );
        			} else throw new Exception( "Undefined error message" );
        		}
        	} else throw new Exception( "Response is not an array" );
        } else throw new Exception( "invalid response" );
	}
		
	public static function secondsToTime($seconds_time)
	{
	    if ($seconds_time < 24 * 60 * 60) {
	        return gmdate('H:i:s', $seconds_time);
	    } else {
	        $hours = floor($seconds_time / 3600);
	        $minutes = floor(($seconds_time - $hours * 3600) / 60);
	        $seconds = floor($seconds_time - ($hours * 3600) - ($minutes * 60));
	        return "$hours:$minutes:$seconds";
	    }
	}
} 
/*end class */