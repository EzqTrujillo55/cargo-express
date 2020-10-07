<?php
if (!isset($_SESSION)) { session_start(); }

class AdminController extends CController
{
	
	public $layout='admin_layout';	
	public $body_class='';
	public $is_newupdate=false;
	
	public function init()
	{			
		 // set website timezone
		 $website_timezone=Yii::app()->functions->getOptionAdmin("website_timezone" );		 
		 if (!empty($website_timezone)){		 	
		 	Yii::app()->timeZone=$website_timezone;
		 }		 				 
		 
		 //Yii::app()->language="ph";		 
		 if(isset($_GET['lang'])){
		 	Yii::app()->language=$_GET['lang'];
		 }
	}
	
	public function beforeAction($action)
	{

		$action_name= $action->id ;
		$accept_controller=array('login','ajax','forgotpass');
		if(!AdminFunctions::islogin()){			
			if(!in_array($action_name,$accept_controller)){
				$this->redirect(Yii::app()->createUrl('/admin/login'));
			}
		}
		
		ScriptManagerAdmin::scripts();
		
		$cs = Yii::app()->getClientScript();
		$jslang=json_encode(AdminFunctions::jsLang());
		$cs->registerScript(
		  'jslang',
		 "var jslang=$jslang;",
		  CClientScript::POS_HEAD
		);
				
		$js_lang_validator=Yii::app()->functions->jsLanguageValidator();
		$js_lang=Yii::app()->functions->jsLanguageAdmin();
		$cs->registerScript(
		  'jsLanguageValidator',
		  'var jsLanguageValidator = '.json_encode($js_lang_validator).'
		  ',
		  CClientScript::POS_HEAD
		);				
		$cs->registerScript(
		  'js_lang',
		  'var js_lang = '.json_encode($js_lang).';
		  ',
		  CClientScript::POS_HEAD
		);
				
		$language=Yii::app()->language;
		$cs->registerScript(
		  'language',
		 "var language='$language';",
		  CClientScript::POS_HEAD
		);
		
		return true;
	}
	
	public function actionLogin()
	{
		if (AdminFunctions::islogin()){
			$this->redirect(Yii::app()->createUrl('/admin/dashboard'));
			Yii::app()->end();
		}
		$this->body_class="login";
		$this->render('login');
	}
	
	public function actionLogout()
	{
		unset($_SESSION['kartero_admin']);
		$this->redirect(Yii::app()->createUrl('/admin/login'));
	}
	
	public function actionIndex()
	{
		$cs = Yii::app()->getClientScript(); 
		
		Yii::app()->clientScript->registerScriptFile(
		Yii::app()->baseUrl . '/assets/amcharts/amcharts.js'
        ,CClientScript::POS_END);		
        
        Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/assets/amcharts/serial.js',
        CClientScript::POS_END);		
        
        Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/assets/amcharts/themes/light.js',
        CClientScript::POS_END);		
        
        if(AdminFunctions::checkNewVersion()){
        	$this->is_newupdate=true;
        }
        
		$this->render('dashboard');
	}
	
	public function actionDashBoard()
	{
		$cs = Yii::app()->getClientScript(); 
		
		Yii::app()->clientScript->registerScriptFile(
		Yii::app()->baseUrl . '/assets/amcharts/amcharts.js'
        ,CClientScript::POS_END);		
        
        Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/assets/amcharts/serial.js',
        CClientScript::POS_END);		
        
        Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/assets/amcharts/themes/light.js',
        CClientScript::POS_END);		
		
        /*check for new update tables*/
        if(AdminFunctions::checkNewVersion()){
        	$this->is_newupdate=true;
        }
        
		$this->render('dashboard');		
	}
	
	public function actionPlanList()
	{
		$this->render('plan-list');
	}
	
	public function actioncustomerList()
	{
		$this->render('customer-list');
	}
	
	public function actionpaymentList()
	{
		$this->render('payment-settings',array(
		  'payment_list'=>AdminFunctions::paymentGatewayList()
		));
	}
	
	public function actionSms()
	{
		$this->render('sms-setttings');
	}
	
	public function actionSettings()
	{
		$logo=AdminFunctions::getImageLink('website_logo');
		$this->render('general-setttings',array(
		 'logo'=>$logo
		));
	}
	
	public function actionProfile()
	{
		$admin_id= AdminFunctions::getAdminID();
		if ( $res=AdminFunctions::getAdminDetailsByID($admin_id)){
			$this->render('profile',array(
			 'data'=>$res
			));
		} else $this->redirect(Yii::app()->createUrl('/admin/login'));
	}
	
	public function actionplanNew()
	{
		$data=array();
		if (isset($_GET['id'])){
			if($data=AdminFunctions::getRecordsFromTable("plan",'plan_id',$_GET['id'])){				
			} 
		}
		$this->render('plans',array(		 
		 'data'=>$data
		));
	}
	
	public function actioncustomerNew()
	{
		$data=array();
		if (isset($_GET['id'])){
			if($data=AdminFunctions::getRecordsFromTable("customer",'customer_id',$_GET['id'])){				
			} 
		}
		$this->render('customer-new',array(		 
		 'data'=>$data
		));
	}
	
	public function actionReports()
	{
		$this->render('reports');
	}
	
	public function actionTemplates()
	{
		$this->render('templates');
	}
	
	public function actionCurrency()
	{
		$this->render('currency');
	}
	
	public function actioncurrencyNew()
	{
		$res='';
		if ( isset($_GET['id'])){
			if (!$res=AdminFunctions::getRecordsFromTable("currency",'curr_id',$_GET['id'])){				
			    $this->redirect(Yii::app()->createUrl('/admin/currency'));
			} 
		}
		$this->render('currency-new',array(
		  'data'=>$res
		));
	}
	
	public function actionLanguage()
	{
		$lang_list=AdminFunctions::getLanguage();
		$selected_lang=getOptionA('language_list');
		$selected_lang=!empty($selected_lang)?json_decode($selected_lang):false;		
		$this->render('language',array(
		  'list'=>$lang_list,
		  'selected_lang'=>$selected_lang
		));
	}
	
	public function actionmobileSettings()
	{
		$this->render('mobile-settings');
	}
	
	public function actionCustomPage()
	{
		$this->render('custom-page');
	}
	
	public function actionrptCustomer()
	{
		$this->render('rpt-customer');
	}
	
	public function actionrptSales()
	{
		$this->render('rpt-sales');
	}
	
	public function actionrptSms()
	{
		$this->render('rpt-sms');
	}
	
	public function actionrptEmail()
	{
		$this->render('rpt-email');
	}
	
	public function actionrptPush()
	{
		$this->render('rpt-push');
	}
	
	public function actionExport()
	{		
		require_once('export.php');
	}	
	
	public function actioncustompageNew()
	{
		Yii::app()->clientScript->registerScriptFile(
        Yii::app()->baseUrl . '/assets/SCEditor/jquery.sceditor.bbcode.min.js',
		CClientScript::POS_END
		);			
				
		$baseUrl = Yii::app()->baseUrl.""; 
		$cs = Yii::app()->getClientScript();				
		$cs->registerCssFile($baseUrl."/assets/SCEditor/themes/default.min.css");		
		
		
		$this->render('custom-page-new',array(
		 'data'=>AdminFunctions::getCustomPage( isset($_GET['id'])?$_GET['id']:'' )
		));
	}
	
	public function actioncustompageAssign()
	{		
		$data=AdminFunctions::getCustomList("published");
		$this->render('custom-page-assign',array(
		 'data'=>$data	
		));
	}
			
	public function actionsetlang()
	{		
		if(!empty($_GET['action'])){
			$url=Yii::app()->createUrl("admin/".$_GET['action'],array(
			  'lang'=>$_GET['lang']
			));
		} else {
			$url=Yii::app()->createUrl("admin/dashboard",array(
			  'lang'=>$_GET['lang']
			));
		}
		/*dump($url);				
		die();*/
		$this->redirect($url);
	}
	
	public function actionPayments()
	{
		if(!isset($_GET['customer_id'])){
			$_GET['customer_id']='';
		}
		$res=AdminFunctions::getCustomerPaymentLogs($_GET['customer_id']);
		$customer_data=AdminFunctions::getCustomerByID($_GET['customer_id']);
		$this->render('payments',array(
		 'data'=>$res,
		 'customer_data'=>$customer_data
		));
	}
	
	public function actioncron()
	{
		$this->render('cron');
	}
	
	public function actionForgotPass()
	{		
		$token=isset($_GET['token'])?$_GET['token']:'';
		if ($res=AdminFunctions::getAdminByPassword($token)){
		   $this->body_class="login";
		   $this->render('change-password',array(
		     'token'=>$token
		   ));
		} else {
			$this->render('error',array(
			  'msg'=>t("Token is not valid")
			));
		}
	}
	
	public function actionServices()
	{
		$this->render('services-list',array(
		'data'=>AdminFunctions::servicesFullList(0)
		));
	}
	
	public function actionServicesNew()
	{
		$res='';
		if ( isset($_GET['id'])){
			if (!$res=AdminFunctions::getRecordsFromTable("services",'services_id',$_GET['id'])){				
			    $this->redirect(Yii::app()->createUrl('/admin/services'));
			} 
		}
		$this->render('services-new',array(
		  'data'=>$res
		));
	}
	
	public function actionSeo()
	{
		$this->render('seo');
	}
	
	public function actionPromocode()
	{
		$this->render('promo-code-list');
	}
	
	public function actionPromoCodeNew()
	{
		$data=AdminFunctions::getPromoCodeByID(isset($_GET['id'])?$_GET['id']:'');
		$this->render('promo-code-add',array(
		  'data'=>$data
		));
	}
	
	public function actionTestEmail()
	{
		$this->render('testemail');
	}
	
	public function actionmap_settings()
	{
		$country_list=require_once('CountryCode.php');
		$this->render('map_settings',array(
		  'country_list'=>$country_list
		));
	}
	
	public function actiontest_map_api()
	{
		$map_provider = getOptionA('map_provider');
		echo "<H3>Map provider : $map_provider</h3>";
		$address = "los angeles california";
		if ( $res = Driver::addressToLatLong($address)){
			echo "<h4>Successful</h4>";
			dump($res);
		} else {
			echo "<h4 style=\"color:red;\">Failed</h4>";
			echo "<P>".Driver::$message."</p>";
		}
	}
	
	public function actionapi_logs()
	{
		$this->render('api_logs',array(
		  'test'=>1
		));
	}
	
	public function actionapi()
	{
		$this->render('api_settings');
	}
		
}/* end class*/