<?php
class UrlManager extends CUrlManager
{
        public function createUrl($route,$params=array(),$ampersand='&')
        {        	
            if(preg_match('/[A-Z]/',$route)!==0)
            {
                    $route=strtolower(preg_replace('/(?<=\\w)([A-Z])/','-\\1',$route));
            }            
            if(isset($_GET['lang'])){
            	if(!empty($_GET['lang'])){
            	   $params['lang']=$_GET['lang'];
            	}
            }                         
            /*if(isset($_GET['language'])){
            	if(!empty($_GET['language'])){
            	   $params['language']=$_GET['language'];
            	   unset($params['lang']);
            	}
            } */                       
            return parent::createUrl($route,$params,$ampersand);
        }

        public function parseUrl($request)
        {               
            $route=parent::parseUrl($request);                                
            //echo $route;
            if(substr_count($route,'-')>0)
            {                            	
               $route=lcfirst(str_replace(' ','',ucwords(str_replace('-',' ',$route))));
            }                
                                    
            if (preg_match("/front/i",$route)) {
	            if (preg_match("/page/i",$route)) {
	            	$route="front/page";
	            }
            }
                        
            return $route;
        }
        
        public function dump($data='')
        {
        	echo '<pre>';
        	print_r($data);
        	echo '</pre>';
        }
}