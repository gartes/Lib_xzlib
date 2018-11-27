<?php

namespace xzlib\app\ajax\component\com_virtuemart;

use JFactory;
use JLayoutFile;
use Joomla\Registry\Registry as Registry;
use JResponseJson;
use JSession;
use JText;
use VmModel;
use xzlib;
use xzlib\app\components\com_virtuemart\helprers as Helprers;
use xzlib\app\components\com_virtuemart\models as Models;

defined('_JEXEC') or die;

class com_virtuemart extends \xzlib\app\ajax\apl
{
	public $registry;
	
	
	public $JUser_keys = [ 'email' => 'string', 'username' => 'string', 'password' => 'string', 'password1' => 'string', 'name' => 'string', 'phone' => 'text', 'agreed' => 'bool', 'last_name' => 'text', ];
	
	
	/**
	 * @param array $options
	 *
	 * @return com_virtuemart
	 *
	 * @since version
	 */
	public static $instance;
	public static function instance ( $options = array() )
	{
		if( self::$instance === NULL )
		{
			self::$instance = new self($options);
		}#END IF
		return self::$instance;
	}#END FN 
	
	/**
	 * Construct method
	 *
	 * @param   array $options
	 *
	 * @since   version 1
	 *
	 */
	public function __construct ( $options = NULL )
	{
		parent::__construct($options);
		
		$language = \JFactory::getLanguage();
		$language->load('lib_xzlib_app_components_com_virtuemart', JPATH_LIBRARIES . '/xzlib/app/components/com_virtuemart', $language->getTag(), true);
		
		self::$instance = $this;
	}#END FN
	
	/**
	 * Получение форм в Ajax
	 *
	 * @throws \Exception
	 * @author    Gartes
	 * @since     3.8
	 * @copyright 02.11.18
	 *
	 */
	public function getForm(){
		
		\JSession::checkToken() or die( 'Invalid Token' );
		$app = \JFactory::getApplication() ;

		$opt = $app->input->getArray(array('opt' => ['name'=>'WORD ']));
		$forms = new xzlib\app\document\elements\forms\forms();
		
		$html  = $forms->getFormByName( $opt['opt']['name'] ) ;
		
		echo new JResponseJson( [ 'html' => $html , ] , \JText::_('ок!'));
		$app->close();
	}#END FN

	/**
	 * ajax Вход формы Нашли дешевле
	 *
	 * @throws \Exception
	 * @author    Gartes
	 * @since     3.8
	 * @copyright 28.10.18
	 *
	 */
	public function send_downPriceForm(){

		\JSession::checkToken() or die( 'Invalid Token' );
		$xzlib_com_virtuemart = xzlib\app\components\com_virtuemart\com_virtuemart::instance();
		$ret =  $xzlib_com_virtuemart->sendDownPrice() ;
		return $ret ;
	}#END FN
	
	
	/**
	 *
	 * Добавление коментария к товару...
	 *
	 * @component  com_jcomments
	 *
	 * @since version
	 * @throws \Exception
	 *
	 */
	public function comment_addComment(){
		$xzlib_com_virtuemart = xzlib\app\components\com_virtuemart\com_virtuemart::instance();
		$ret =  $xzlib_com_virtuemart->addComment() ;
		return $ret ;
	}#END FN
	
	
	/**
	 * Обновление модуля корзины
	 *
	 * @return array
	 *
	 * @since version
	 * @throws \Exception
	 */
	public function updateCart(){
		$xzlib_com_virtuemart = xzlib\app\components\com_virtuemart\com_virtuemart::instance();
		$ret =  $xzlib_com_virtuemart->getCartHtml() ;
		return $ret ;
	}#END FN
	
	/**
	 * Залогинить пользователя
	 *
	 * @since version
	 * @throws \Exception
	 */
	public function loginVmUser ()
	{
		
		JSession::checkToken() or die('Invalid Token');
		
		$app    = JFactory::getApplication();
		$jinput = $app->input;
		
		$credentials[ 'username' ] = $jinput->get('username', false, 'STRING');
		$credentials[ 'password' ] = $jinput->get('password', false, 'STRING');
		
		$xzlib_com_users = xzlib::getComponent('com_users');
		$res = $xzlib_com_users->loginUser($credentials);
		
		if( $res ){
			echo new JResponseJson(NULL, \JText::_('ок!'));
			$app->close();
		}
	}#END FN
	
	/**
	 * Регистрация пользователя в Virtuemart
	 *
	 * @return array
	 *
	 * @since version 1
	 * @throws \Exception
	 */
	public function regVmUser ()
	{
		
		// JSession::checkToken() or die( 'Invalid Token' );
		
		$app    = \JFactory::getApplication();
		$jinput = $app->input;
		
		$xzlib_vmComponent = xzlib::getComponent('com_virtuemart');
		
		$phone_1 = $jinput->get('phone_1', false, 'STRING');
		$email   = $jinput->get('email', false, 'STRING');
		
		# проверка наличия пользователя по емайлу и телефону
		$_RETUN = $xzlib_vmComponent->checkVmUser([ 'phone_1' => $phone_1, 'email' => $email ]);
		
		# Выход Если пользователь был зарегин ранее...
		if( $_RETUN )
		{
			$app->enqueueMessage(\JText::_('LIB_XZLIB_COM_VIRTUEMART_NOTICE_USED_LOGIN_FORM'), 'Notice');
			echo new JResponseJson(NULL, \JText::_('LIB_XZLIB_COM_VIRTUEMART_DENIED_REG'), true);
			$app->close();
		}#END IF
		
		
		# xzlib Models user
		$XZ_userModel = new Models\user();
		# Регистрация пользователя Joomla
		$JUser = $XZ_userModel->joomlaRegUser();
		
		# Регистрация пользователя
		$vmUser = $XZ_userModel->virtuemartRegUser($JUser);
		
		if( !$vmUser[ 'userType' ] == 'new' )
		{
			echo new JResponseJson(NULL, \JText::_('LIB_XZLIB_COM_VIRTUEMART_DENIED_REG_ERROR_VM'), true);
			$app->close();
		}
		# Макет для модального окна
		# Данные о регистрации + код купона для скидки отправленны на email
		$data      = [ 'JUser' => $JUser ];
		$layout    = new JLayoutFile('user.registration_completed', JPATH_LIBRARIES . '/xzlib/app/components/com_virtuemart/layouts');
		$modal_mes = $layout->render($data); // $displayData
		
		$RET = array( 'fancibox_body' => $modal_mes );
		
		return $RET;
	}
	
	
	/**
	 * Создание купона для незарегистрированного пользователя
	 * Проверка пользователя по:
	 *          Номеру телефона
	 *          Емайл
	 *
	 * @return array
	 *
	 * @since version
	 * @throws \Exception
	 */
	public function add_coupon_code_nUser ()
	{
		$app    = \JFactory::getApplication();

		
		// Параметры плагина system - Xlib
		$ParamsXlibPlg = $this->getXlibPlgParams('coupon');
		
		$xzlib_vmComponent = xzlib::getComponent('com_virtuemart');
		
		$phone_1 = $app->input->get('phone_1', false, 'STRING');
		$email   = $app->input->get('email', false, 'STRING');
		
		# проверка наличия пользователя по емайлу и телефону
		$_RETUN = $xzlib_vmComponent->checkVmUser([ 'phone_1' => $phone_1, 'email' => $email ]);
		
		
		// Выход Если пользователь был зарегин ранее...
		if( $_RETUN )
		{
			$app->enqueueMessage(\JText::_('LIB_XZLIB_COM_VIRTUEMART_NOTICE_GET_CUPON_IS_NEW_USER'), 'Notice');
			echo new JResponseJson(NULL, \JText::_('LIB_XZLIB_COM_VIRTUEMART_DENIED'), true);
			$app->close();
		}#END IF
		
		// xzlib Models user
		$XZ_userModel = new Models\user();
		// Регистрация пользователя Joomla
		$JUser = $XZ_userModel->joomlaRegUser();
		
		// Регистрация пользователя
		$vmUser = $XZ_userModel->virtuemartRegUser($JUser);
		if( $vmUser[ 'userType' ] == 'new' )
		{
			
			
			// Создание нового купона
			$coupon_code = strtoupper($ParamsXlibPlg->code_prefix . \JUserHelper::genRandomPassword(6));
			
			$data[ 'coupon_code' ]        = $coupon_code;
			$data[ 'coupon_value' ]       = $ParamsXlibPlg->value;
			$data[ 'coupon_start_date' ]  = new \JDate('now');
			$data[ 'coupon_expiry_date' ] = new \JDate('now +' . $ParamsXlibPlg->expiry_date . ' day');
			$data[ 'coupon_type' ]        = $ParamsXlibPlg->type;
			$data[ 'percent_or_total' ]   = $ParamsXlibPlg->percent_or_total;
			$data[ 'coupon_value_valid' ] = $ParamsXlibPlg->value_valid;
			$vmCouponModel                = new Models\vmCoupon();
			$virtuemart_coupon_id         = $vmCouponModel->store($data);
			
			
			# Если включена поддержка компоненнта
			# AwoCoupon Virtuemart
			if( $ParamsXlibPlg->suport_Awo_Coupon )
			{
				$this->storeAwo_Coupon($data);
			}#END IF
			
			
			if( $virtuemart_coupon_id > 0 )
			{
				# Макет для кода купона
				# для отправки по email
				$layout      = new JLayoutFile('coupons.auto_coupon', JPATH_LIBRARIES . '/xzlib/app/components/com_virtuemart/layouts');
				$html_coupon = $layout->render($data); // $displayData
			}#END IF
			
			$DATA_HTML_EMAILE_BODY                     = array();
			$DATA_HTML_EMAILE_BODY[ 'name' ]           = $JUser->name;
			$DATA_HTML_EMAILE_BODY[ 'username' ]       = $JUser->username;
			$DATA_HTML_EMAILE_BODY[ 'password_clear' ] = $JUser->password_clear;
			$DATA_HTML_EMAILE_BODY[ 'registerDate' ]   = $JUser->registerDate;
			
			# Макет для завершения регистрации
			# Данные пользователя для входа на сайт
			# для отправки по email
			$layout          = new JLayoutFile('registration.complete', JPATH_LIBRARIES . '/xzlib/app/components/com_users/layouts');
			$regCompleteHtml = $layout->render($DATA_HTML_EMAILE_BODY); // $displayData
			
			$RET = array( 'fancybox_content' => $regCompleteHtml . $html_coupon, );
			
			$config = \JFactory::getConfig();
			
			# определяем необходимые параметры
			$jmailData[ 'subject' ] = $config->get('fromname') . ' ' . \JText::_('LIB_XZLIB_COM_VIRTUEMART_SUBJECT_MAIL_AUTO_COUPON');
			$jmailData[ 'body' ]    = $regCompleteHtml . $html_coupon;
			$jmailData[ 'to' ]      = $email;
			$jmailData[ 'from' ]    = array( $config->get('mailfrom'), $config->get('fromname') );
			# отправить сообщение пользователю
			$this->sendMaile($jmailData);
			
			
			# Макет для модального окна
			# Данные о регистрации + код купона для скидки отправленны на email
			$jmailData[ 'coupon_expiry_date' ] = $data[ 'coupon_expiry_date' ];
			$layout                            = new JLayoutFile('coupons.coupon_modal_mes', JPATH_LIBRARIES . '/xzlib/app/components/com_virtuemart/layouts');
			$modal_mes                         = $layout->render($jmailData); // $displayData
			
			$RET = array( 'fancibox_body' => $modal_mes );
			
			return $RET;
			
		}#END IF
	}#END FN







    public function  getCountdownCouponForm(){



        // Параметры плагина system - Xlib
        $ParamsXlibPlg = $this->getXlibPlgParams('coupon');


        // Создание нового купона
        $coupon_code = strtoupper( 'CD-'. \JUserHelper::genRandomPassword(6)) ;

        $data[ 'coupon_code' ]        = $coupon_code;
        $data[ 'coupon_value' ]       = $ParamsXlibPlg->value;
        $data[ 'coupon_start_date' ]  = new \JDate('now');
        $data[ 'coupon_expiry_date' ] = new \JDate('now +' . $ParamsXlibPlg->expiry_date . ' day');
        $data[ 'coupon_type' ]        = $ParamsXlibPlg->type;
        $data[ 'percent_or_total' ]   = $ParamsXlibPlg->percent_or_total;
        $data[ 'coupon_value_valid' ] = $ParamsXlibPlg->value_valid;


        # Если включена поддержка компоненнта
        # AwoCoupon Virtuemart
        if( $ParamsXlibPlg->suport_Awo_Coupon )
        {
            $this->storeAwo_Coupon($data);
        }#END IF


        $layout      = new JLayoutFile( 'footer.countdownCoupon', $basePath = null);
        $res['html'] = $layout->render($data);

        return $res ;

    }#END FN


	/**
	 * Предзаказ товара которого нет в наличи
	 *
	 * @author    Gartes
	 * @since     3.8
	 * @copyright 15.10.18
	 *
	 */
	public function addPreOrder(){


		if( !class_exists('VmConfig') ){
			require( JPATH_ROOT . '/administrator/components/com_virtuemart/helpers/config.php' );
			\VmConfig::loadConfig();
			\vmLanguage::loadJLang('com_virtuemart');
		}
		\VmConfig::set('stockhandle' , 'none');

		// vmJsApi::vmValidator();



		$virtuemart_order_id = $this->_setFastOrder('preOrder');

		$orderModel = VmModel::getModel('orders');
		$order = $orderModel->getOrder($virtuemart_order_id ) ;

		$order['order_status'] = 'W'    ;
		$order['customer_notified'] = 0 ;
		$order['comments'] = 'Предзаказ товара';
		$orderModel->updateStatusForOneOrder ( $order['details']['BT']->virtuemart_order_id , $order , false );

		$layout = new \JLayoutFile('oneClickResponse');
		$html =  $layout->render(
			[
				'order_number' => $order['details']['BT']->order_number
			]
		); // $displayData

		$result = array(
			'id' => $virtuemart_order_id ,
			'order_number' => $order->details['BT']->order_number ,
			'hrml_response' => $html,
		);
		echo new JResponseJson($result, \JText::_('OK_SUCCESS'));
		jexit();

		
	}#END FN

	/**
	 * Заказ в один клик
	 *
	 * @author    Gartes
	 * @since     3.8
	 * @copyright 15.10.18
	 *
	 */
    public function addOderOneClick(){

	    $virtuemart_order_id = $this->_setFastOrder();

		$orderModel = VmModel::getModel('orders');
	    $order = $orderModel->getOrder($virtuemart_order_id ) ;
		
	    
	    
	    
		$order['order_status'] = 'K'    ;
	    $order['customer_notified'] = 0 ;
	    $order['comments'] = 'Заказ в 1 клик';
	    $orderModel->updateStatusForOneOrder ( $order['details']['BT']->virtuemart_order_id , $order , false );
		

		$layout = new \JLayoutFile('oneClickResponse');
		$html =  $layout->render(
			[
				'order_number' => $order['details']['BT']->order_number
			]
		); // $displayData

		$result = array(
			'id' => $virtuemart_order_id ,
			'order_number' => $order->details['BT']->order_number ,
			'hrml_response' => $html,
		);
		echo new JResponseJson($result, \JText::_('OK_SUCCESS'));
		jexit();

	}#END FN

	/**
	 * Создание заказа для форм Заказ в 1 клик и Предзаказ
	 *
	 * @return INT virtuemart_order_id
	 * @throws \Exception
	 * @author    Gartes
	 * @since     3.8
	 * @copyright 15.10.18
	 */
	protected function _setFastOrder( $formName = 'oneClick'){
		$app    = \JFactory::getApplication();

		if (!class_exists('VirtueMartCart')){
			require(VMPATH_SITE . DS . 'helpers' . DS . 'cart.php');
		}

		if ( !JSession::checkToken() )
		{
			echo new JResponseJson( false , \JText::_('ERR_TOKEN'), true );
			jexit();
		}

		$recaptcha_result = $app->input->get( 'g-recaptcha_result' , false , 'ARRAY') ;

		if ($recaptcha_result['success'] !=  1 )
		{
			 echo new \JResponseJson( false , \JText::_('ERR_G_RECAPHCA'), true );
			 jexit();
		}


		$app->input->set('create_invoice' , 1) ;

		$cart = \VirtueMartCart::getCart(true);
		$products = $cart->add( null , $error);
		$cart->prepareCartData();
		

		
		$fields = $app->input->getArray(
			array(
				$formName => array(
					'last_name' => 'STRING',
					'email' => 'EM,AIL' ,
					'phone_1' => 'TEL',
                    'customer_note' => 'STRING'
                )
			)
		);

		$cart->cartfields = array('customer_note' =>  $fields[$formName]['customer_note'] ) ;

		$cart->saveAddressInCart($fields[$formName] ,'BT');
		$cart->saveCartFieldsInCart() ;

		$cart->setDataValidation(1);
		$cart->_confirmDone= 1;
		$cart->setShipmentMethod($force=true, $redirect=false, $virtuemart_shipmentmethod_id = 32 );
		$cart->setPaymentMethod($force=true, $redirect=false, $virtuemart_paymentmethod_id = 7);

		try
		{
			// Code that may throw an Exception or Error.
			$virtuemart_order_id = $cart->confirmedOrder();

		}
		catch (Throwable $e)
		{
			// Executed only in PHP 7, will not match in PHP 5
			echo 'Err:: ', $e->getMessage(), "\n";

		}
		catch (Exception $e)
		{
			// Executed only in PHP 5, will not be reached in PHP 7
			echo 'Err:: ', $e->getMessage(), "\n";
		}
		\VirtuemartCart::emptyCartValues($cart,true);

		return $virtuemart_order_id ;

	}


	/**
	 * Создание купона для Компонента Awo_Coupon
	 *
	 * @param $couponData
	 *
	 *
	 * @since version
	 */
	public function storeAwo_Coupon ( $couponData )
	{
		# Параметры плагина system - Xlib
		$ParamsXlibPlg = $this->getXlibPlgParams('coupon');
		
		$db      = \JFactory::getDBO();
		$query   = $db->getQuery(true);
		$columns = array(
		        'coupon_code',
                'num_of_uses',
                'coupon_value_type',
                'coupon_value',
                'min_value',
                'discount_type',
                'function_type',
                'function_type2',
                'startdate',
                'expiration'
        );
		
		$values = $db->quote($couponData[ 'coupon_code' ]) . ","
                . $db->quote(1) . ","
                . $db->quote($ParamsXlibPlg->percent_or_total) . ","
                . $db->quote($couponData[ 'coupon_value' ]) . ","
                . $db->quote($ParamsXlibPlg->value_valid) . ","
                . $db->quote(2) . ","
                . $db->quote(2) . ","
                . $db->quote(NULL) . ","
                . $db->quote( new \JDate( 'now' ) ) . ","
                . $db->quote( new \JDate( 'now +' . $ParamsXlibPlg->expiry_date . ' day' ) );
		
		$query->values($values);
		
		$query->insert($db->quoteName(/** @lang text */
			'#__awocoupon_vm'))->columns($db->quoteName($columns));
		$db->setQuery($query);
		//echo $query->dump();
		$db->execute();
	}#END FN
	
	
	/**
	 * Отправка письма пользователю
	 *
	 * @param $jmailDat
	 *
	 *
	 * @since version
	 */
	public function sendMaile ( $jmailDat )
	{
		# создаем объект JMail класса
		$mailer = \JFactory::getMailer();
		
		# присваиваем отправителя
		$mailer->setSender($jmailDat[ 'from' ]);
		
		# определяем получателя, тему и тело письма
		$mailer->addRecipient($jmailDat[ 'to' ]);
		$mailer->setSubject($jmailDat[ 'subject' ]);
		$mailer->setBody($jmailDat[ 'body' ]);
		
		# если хотите отправить письмо как HTML
		$mailer->isHTML(true);
		
		# отправляем письмо
		$mailer->send();
		
	}#END FN
	
	
	/**
	 *  Получение параметров плагина  system Xlib
	 *
	 * @param bool $fields
	 *
	 * @return mixed
	 *
	 * @since version 1
	 */
	function getXlibPlgParams ( $fields = false )
	{
		$plg_xlib        = \JPluginHelper:: getPlugin($type = "system", $plugin = 'xlib');
		$plg_xlib_params = json_decode($plg_xlib->params);
		
		if( $fields )
		{
			return $plg_xlib_params->{$fields};
		}
		
		return $plg_xlib_params;
	}
	
	
}#END CLASS