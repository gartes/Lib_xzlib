<?php namespace xzlib\app\api\nova_poshta;


use JForm;
use JPluginHelper;
use JRegistry;
use JResponseJson;
use JSession;
Use NovaPoshta\Config;
use NovaPoshta\ApiModels\Address;
use NovaPoshta\MethodParameters\Address_getStreet;
use NovaPoshta\MethodParameters\Address_getWarehouses;
use NovaPoshta\MethodParameters\Address_getCities;
use NovaPoshta\MethodParameters\Address_getAreas;
use stdClass;
use VmModel;
use vmPlugin;
use VmTable;
use vRequest;

class nova_poshta extends \xzlib\app\api\api
{
	
	public $ApiKey;
	
	/**
	 * @param array $options
	 *
	 * @return \xzlib\app\api\api|nova_poshta
	 * @throws \Exception
	 * @author    Gartes
	 * @since     3.8
	 * @copyright 06.11.18
	 */
	public static $instance;
	public static function instance ( $options = [] )
	{
		if ( self::$instance === null )
		{
			self::$instance = new self( $options );
			include_once "vendor/autoload.php";
			
			
			
			self::$instance->Init();
		}#END IF
		return self::$instance;
	}#END FN
	/**
	 * nova_poshta constructor.
	 *
	 * @param null $options
	 *
	 * @throws \Exception
	 */
	public function __construct ( $options = null )
	{
		parent::__construct( $options );
		self::$instance = $this;
	}#END FN
	
	/**
	 * @return mixed|nova_poshta
	 * @author    Gartes
	 * @since     3.8
	 * @copyright 13.11.18
	 * @throws \Exception
	 */
	public function Init ()
	{
		if (!$this->app->input->get( 'XzlibRequest', false, 'INT' ) )return true ;
		
		Config::setApiKey( $this->getApiKey() /*$setting[ 'ApiKey' ]*/ );
		Config::setFormat( Config::FORMAT_JSONRPC2 );
		Config::setLanguage( Config::LANGUAGE_UA );
		
		$model = $this->app->input->get( 'model', false, 'WORD' );
		$task  = $this->app->input->get( 'task', false, 'WORD' );
		
		if ( $model && $model!= 'local' )
		{
			$obj = '\xzlib\app\api\nova_poshta\models\model' . $model;
			$r   = new $obj();
			$res = $r->{$task}();
			if ( !$res->success )
			{
				foreach ( $res->errors as $error )
				{
					$this->app->enqueueMessage( $error . "\n" );
				}#END FOREACH
				echo new JResponseJson( '', 'API Nova Poshta Error:', true );
				jExit();
			}#END IF
			return $this->getResponse( $res );
		}#END IF
		
		//return $this->{$task}();
		return self::$instance;
	}#END FN
	
	/**
	 * Перевод логов Новой почты
	 *
	 * @param $logs
	 *
	 * @return mixed
	 * @author    Gartes
	 * @since     3.8
	 * @copyright 15.11.18
	 */
	public static function logLanguage($logs){
		$language = \JFactory::getLanguage();
		$language->load('lib_nova_poshta' , dirname(__FILE__), $language->getTag(), true);
		
		$arrLog = ['errors','warnings','info'];
		
		foreach ($arrLog as $ind){
			$r = [] ;
			foreach ($logs->$ind as $i => $str ){
				$r[$i] = \JText::_(str_replace(" ", "_", strtoupper ($str)) ) ;
			}
			$logs->{$ind} = $r ;
		}
		return $logs ;
	}#END FN
	
	/**
	 * Создание ЕН
	 *
	 * @author    Gartes
	 * @since     3.8
	 * @copyright 15.11.18
	 * @throws \Exception
	 */
	public function saveIntDocNumber(){
		
		JSession::checkToken() or die( 'Invalid Token!!' );
		$shipmentModel = VmModel::getModel( 'shipmentmethod' );
		$virtuemart_shipmentmethod_id = $this->app->input->get('method_id' , false , 'INT') ;
		$method = $shipmentModel->getShipment( $virtuemart_shipmentmethod_id );
		
		# Получить id заказа
		$virtuemart_order_id = $this->app->input->get('virtuemart_order_id',false,'INT');
		if (!$virtuemart_order_id){
			throw new \Exception('Отсутствует номер заказа');
		}
		
		$this->setApiKey( $method->keyAPI );
		
		#  Получить данные формы
		$formNovaPochta = self::getFormNovaPochta($this->app);
		
		#Модель Контрагентов
		$modelCounterparty = $this->getModel ( 'Counterparty' ) ;
		#Получить Котрагента отправителя
		$sender = $modelCounterparty->getCounterparties ('Sender');
		if (!$sender->success){
			throw new \Exception('Не удалось получить Котрагента отправителя');
		}#END IF
		
		#Получить Контакт персона Контрагена
		$senderContact = $modelCounterparty->getCounterpartyContactPersons ( $sender->data[0] );
		if (!$senderContact->success){
			throw new \Exception('Не удалось получить Контакт персону - котрагента отправителя');
		}#END IF
		
		
		
		/*$CounterpartyAddresses = $modelCounterparty->getCounterpartyAddresses( $formNovaPochta['nova_pochta']['Recipient'] );
		echo'<pre>';print_r( $CounterpartyAddresses  );echo'</pre>'.__FILE__.' '.__LINE__;
		die(__FILE__ .' Lines '. __LINE__ );
		*/
		
		
		
		
		
		#Модель ЕН
		$modelInternetDocument = $this->getModel ( 'InternetDocument' ) ;
		$res = $modelInternetDocument->save(
			$sender->data[0],
			$senderContact->data[0],
			$method,
			$formNovaPochta['nova_pochta'] ,
			$virtuemart_order_id
		);
		
		if (!$res->success){
			$log = self::logLanguage($res);
			// echo'<pre>';print_r( $res );echo'</pre>'.__FILE__.' '.__LINE__;
			$formNovaPochta = self::prepareResult($formNovaPochta, $log);
			return $formNovaPochta ;
			// throw new \Exception('Ошибка при создании ЕН');
		}#END IF
		
		
		
		
		
		
		$formNovaPochta['nova_pochta']['InternetDocument'] = (array)$res->data[0] ;
		# Подписать логи операции
		$formNovaPochta = self::prepareResult($formNovaPochta, $res);
		if ($res->success){
			$formNovaPochta['INFO']->info[] = 'Экспресс-накладная создана:'. $formNovaPochta['nova_pochta']['InternetDocument']['IntDocNumber'];
		}
		return $formNovaPochta ;
	}#END FN
	
	/**
	 * Подписать логи операции
	 * @param $formNovaPochta
	 * @param $res
	 *
	 * @return mixed
	 * @author    Gartes
	 * @since     3.8
	 * @copyright 18.11.18
	 */
	static function prepareResult( $formNovaPochta, $res ){
		foreach ($formNovaPochta['INFO'] as $index => $val ){
			$formNovaPochta['INFO']->{$index} = $res->{$index} ;
		}#END FOREACH
		return $formNovaPochta ;
	}#END FN
	
	static function getFormNovaPochta( $app ){
		$formNovaPochta = $app->input->getArray(['nova_pochta'=>'ARRAY']);
		$formNovaPochta['INFO'] = new stdClass();
		$formNovaPochta['INFO']->errors = [];
		$formNovaPochta['INFO']->errorCodes = [];
		$formNovaPochta['INFO']->warnings = [];
		$formNovaPochta['INFO']->info = [];
		
		return $formNovaPochta ;
	}
	
	
	
	/**
	 * Сохранить данные о даставке и обновить данные контрагента на сервере Новой Почты
	 *  Ajax вызов.
	 * @return mixed
	 * @throws \Exception
	 * @author    Gartes
	 * @since     3.8
	 * @copyright 13.11.18
	 */
	 function saveAdminForm(){
		
		JSession::checkToken() or die( 'Invalid Token!!' );
		$shipmentModel = VmModel::getModel( 'shipmentmethod' );
		$ordersModel = VmModel::getModel( 'Orders' );
		
		$virtuemart_shipmentmethod_id = $this->app->input->get('method_id' , false , 'INT') ;
		
		
		$method = $shipmentModel->getShipment( $virtuemart_shipmentmethod_id );
		$this->setApiKey( $method->keyAPI );
		
		
		
		$formNovaPochta = self::getFormNovaPochta($this->app);
		
		$formNovaPochta['nova_pochta']['virtuemart_shipmentmethod_id'] = $virtuemart_shipmentmethod_id ;
		# Тип груза
		$formNovaPochta['nova_pochta'][ 'CargoType' ] = $method->CargoType;
		$formNovaPochta['nova_pochta'][ 'PaymentMethod' ] = $method->PaymentMethod;
		
		$formNovaPochta['nova_pochta'][ 'virtuemart_order_id' ] = $this->app->input->get( 'virtuemart_order_id' , false , 'INT' );
		
		$order = $ordersModel->getOrder(  $formNovaPochta['nova_pochta'][ 'virtuemart_order_id' ] );
		$formNovaPochta['nova_pochta']['order_number'] = $order['details']['BT']->order_number  ;
		
		 
		
		
		# Обновление данных контрагента
		$modelCounterparty = $this->getModel ( 'Counterparty' ) ;
		
		
		$data= new \stdClass();
		$data->CounterpartyProperty =  'Recipient';
		$data->CounterpartyType =  'PrivatePerson' ;
		$data->CityRef =  $formNovaPochta['nova_pochta']['CityRecipient'] ;
		$data->FirstName =  $formNovaPochta['nova_pochta']['FirstName'] ;
		$data->LastName =  $formNovaPochta['nova_pochta']['LastName'] ;
		
		$data->MiddleName= $formNovaPochta['nova_pochta']['MiddleName'] ;
		
		$data->Phone =  $formNovaPochta['nova_pochta']['RecipientsPhone'] ;
		
		
		# Если есть Recipient - обновляем
		if ( $formNovaPochta[ 'nova_pochta' ][ 'Recipient' ] )
		{
			$data->Ref       = $formNovaPochta[ 'nova_pochta' ][ 'Recipient' ];
			$resCounterparty = $modelCounterparty->update( $data );
			
			if ( !$resCounterparty->success )
			{
				throw new \Exception( 'Не удалось обновить данные контрагена на сервере Новой Почты: ' . implode( ", ", $resCounterparty->errors ), 101 );
			}#END IF
			$formNovaPochta['INFO']->info[] = 'Данные контрагента обнавлены' ;
			$formNovaPochta[ 'nova_pochta' ]['Recipient'] = $resCounterparty->data[0]->Ref;
			$formNovaPochta[ 'nova_pochta' ]['ContactRecipient'] = $resCounterparty->data[0]->ContactPerson->data[0]->Ref;
		}
		else
		{
			$resCounterparty = $modelCounterparty->save( (array) $data );
			if ( !$resCounterparty->success )
			{
				$formNovaPochta[ 'INFO' ]->warnings[] = 'Не удалось сохранить данные контрагена на сервере Новой Почты: ' . implode( ", ", $resCounterparty->errors ) ;
				// throw new \Exception( 'Не удалось сохранить данные контрагена на сервере Новой Почты: ' . implode( ", ", $resCounterparty->errors ), 101 );
			}else{
				$formNovaPochta['INFO']->info[] = 'Созданн новый котрагент' ;
				$formNovaPochta[ 'nova_pochta' ]['Recipient'] = $resCounterparty->data[0]->Ref;
				$formNovaPochta[ 'nova_pochta' ]['ContactRecipient'] = $resCounterparty->data[0]->ContactPerson->data[0]->Ref;
			}#END IF
			
			
			
			
			
			
			/*echo'<pre>';print_r(  $formNovaPochta[ 'nova_pochta' ] );echo'</pre>'.__FILE__.' '.__LINE__;
			echo'<pre>';print_r( $resCounterparty );echo'</pre>'.__FILE__.' '.__LINE__;*/
			
		}#END IF
		 
		
		
		 #Обновление адреса контрагента.
		 #при способе доставки к двери
		if ( $formNovaPochta[ 'nova_pochta' ][ 'ServiceType' ] == 'WarehouseDoors' )
		{
			$modelAddress          = $this->getModel( 'Address' );
			
			$data                  = new \stdClass();
			// $data->Ref             = $formNovaPochta[ 'nova_pochta' ][ 'RecipientAddress' ];
			$data->CounterpartyRef = $formNovaPochta[ 'nova_pochta' ][ 'Recipient' ];
			$data->BuildingNumber  = $formNovaPochta[ 'nova_pochta' ][ 'Address' ][ 'house' ];
			$data->Flat            = $formNovaPochta[ 'nova_pochta' ][ 'Address' ][ 'flat' ];
			$data->Note            = '';
			$data->StreetRef       = $formNovaPochta[ 'nova_pochta' ][ 'RefStreet' ];
			
			$resAddress            = $modelAddress->save( $data );
			
			if ( !$resAddress->success )
			{
				throw new \Exception( 'Не удалось обновить данные Адреса контрагена на сервере Новой Почты: ' . implode( ", ", $resAddress->errors ), 102 );
			}#END IF
			
			$formNovaPochta[ 'INFO' ]->info[] = 'Созданн новый адрес котрагента ' . $resAddress->data[ 0 ]->Description;
			
			$formNovaPochta[ 'nova_pochta' ][ 'RecipientAddressDoors' ]         = $resAddress->data[ 0 ]->Ref;
			$formNovaPochta[ 'nova_pochta' ][ 'Address' ][ 'Description' ] = $resAddress->data[ 0 ]->Description;
		}#END IF
		
		
		# Сохранить логи
		$formNovaPochta[ 'nova_pochta' ][ 'log' ] = json_encode( $formNovaPochta[ 'INFO' ] );
		
		# Сохранить параметры Адреса
		$formNovaPochta[ 'nova_pochta' ][ 'Address' ][ 'RefStreet' ] = $formNovaPochta[ 'nova_pochta' ][ 'RefStreet' ];
		$formNovaPochta[ 'nova_pochta' ][ 'Address' ]                = json_encode( $formNovaPochta[ 'nova_pochta' ][ 'Address' ] );
		
		#Сохранить праметры обратной доставки
		$formNovaPochta[ 'nova_pochta' ][ 'BackwardDeliveryData' ] = json_encode( $formNovaPochta[ 'nova_pochta' ][ 'BackwardDeliveryData' ] );
		
		
		# Ref некладной
		$formNovaPochta[ 'nova_pochta' ] [ 'IntDocNumber' ] =    $formNovaPochta['nova_pochta']['InternetDocument']['IntDocNumber'] ;
		#Прогноз даты доставки:
		$formNovaPochta[ 'nova_pochta' ][ 'EstimatedDeliveryDate' ] = $formNovaPochta['nova_pochta']['InternetDocument']['EstimatedDeliveryDate'] ;
		# Информация о Экспресс-накладной
		$formNovaPochta[ 'nova_pochta' ] [ 'InternetDocument' ] =  json_encode( $formNovaPochta['nova_pochta']['InternetDocument']);
		
//		echo'<pre>';print_r( $formNovaPochta );echo'</pre>'.__FILE__.' '.__LINE__;
//		die(__FILE__ .' Lines '. __LINE__ );
		
		
		
		
		# Тригер обновить таблице плагина Новой почты - Параметры доставки заказа.
		$dispatcher = \JEventDispatcher::getInstance();
		$plugins = \JPluginHelper::importPlugin('vmshipment','nova_pochta' , true , $dispatcher);
		$dispatcher->trigger( 'plgVmNpOnUpdateOrderBEShipment', array( $formNovaPochta['nova_pochta'] ) );
		
		echo new JResponseJson( $formNovaPochta );
		$this->app->close();
		
		return $formNovaPochta ;
	}#END FN
	
	
	
	/**
	 * Получить параметры настроек Новой Почты
	 *
	 * @return array
	 * @author    Gartes
	 * @since     3.8
	 * @copyright 06.11.18
	 */
	private function getSetting ()
	{
		
		die(__FILE__ .' Lines '. __LINE__ );
		
		/*$xlibPLG   = JPluginHelper::getPlugin( 'vmshipment', 'nova_pochta' );
		$paramsPlg = new JRegistry( $xlibPLG->params );
		
		$methodId = false ; 
		
		$shipmentModel = VmModel::getModel( 'shipmentmethod' );
		$jinputArr     = $this->app->input->getArray(
			[
				'opt' => [
					'keyAPI'   => 'ALNUM',
					'methodId' => 'INT',
				],
			]
		);
		
		if ( $jinputArr[ 'opt' ][ 'methodId' ] ) {
			$methodId =  $jinputArr[ 'opt' ][ 'methodId' ] ;
		}
		if (!$methodId ) {
//			echo'<pre>';print_r( $this->app->input->getArray(['_plgNovaPochta' =]) );echo'</pre>'.__FILE__.' '.__LINE__;
			die(__FILE__ .' Lines '. __LINE__ );
		}
		
		
		
		
		echo'<pre>';print_r( $jinputArr[ 'opt' ][ 'methodId' ] );echo'</pre>'.__FILE__.' '.__LINE__;
		
		$method = $shipmentModel->getShipment( $jinputArr[ 'opt' ][ 'methodId' ] );
		$setting             = [];
		$setting[ 'ApiKey' ] = $jinputArr[ 'opt' ][ 'keyAPI' ];
		return $setting;*/
	}#END FN
	
	public function getModel ( $model = false )
	{
		
		
		
		Config::setApiKey( $this->getApiKey() );
		Config::setFormat( Config::FORMAT_JSONRPC2 );
		Config::setLanguage( Config::LANGUAGE_UA );
		
		$app = \JFactory::getApplication();
		if ( !$model )
		{
			$model = $app->input->get( 'model', $model, 'WORD' );
		}
		$modelName = 'model' . ucfirst( $model );
		$obj = '\\xzlib\\app\\api\\nova_poshta\\models\\' . $modelName;
		
		
		$model = $obj::instance();
		
		return $model;
	}#END FN
	
	
	/**
	 * D!!!
	 *
	 * Загрузить список складов в городе
	 *
	 * @param bool $Ref
	 *
	 * @return mixed
	 * @throws \Exception
	 * @author    Gartes
	 * @since     3.8
	 * @copyright 07.11.18
	 */
	public function getWarehouses ( $Ref = false )
	{
		
		
		
		
		$model = new \xzlib\app\api\nova_poshta\models\modelAddress();
		$data  = $model->getWarehouses( $Ref );
		
		echo '<pre>';
		print_r( $data );
		echo '</pre>' . __FILE__ . ' ' . __LINE__;
		
		return $this->getResponse( $data );
	}#END FN
	
	
	/**
	 * Подготовка Ajax ответа
	 *
	 * @param $data
	 *
	 * @return mixed
	 * @author    Gartes
	 * @since     3.8
	 * @copyright 07.11.18
	 */
	private function getResponse ( $data )
	{
		return $data->data;
	}
	
	/**
	 * Установка Ключа
	 *
	 * @param mixed $ApiKey
	 */
	public function setApiKey ( $ApiKey )
	{
		$this->ApiKey = $ApiKey;
	}
	
	/**
	 * Получение Ключа API Новой Почты
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function getApiKey ()
	{
		
		
		$optArr = $this->app->input->getArray(['opt'=>['methodId'=>'INT']]) ;
		
		$method_id = $this->app->input->get('method_id' , $optArr['opt']['methodId'] , 'INT') ;
		$virtuemart_shipmentmethod_id = $this->app->input->get('virtuemart_shipmentmethod_id' , $method_id , 'INT') ;
		
		
		if ($virtuemart_shipmentmethod_id){
			
			$shipmentModel = VmModel::getModel('Shipmentmethod');
			$shipment = $shipmentModel->getShipment($virtuemart_shipmentmethod_id);
			$this->ApiKey = $shipment->keyAPI;
			
		}#END IF
		
		
		
		
		
		if (!$this->ApiKey){
			$np = $this->app->input->getArray(['_nova_pohsta'=>['keyAPI'=>'ALNUM']]) ;
			
			
			if ( !empty( $np['_nova_pohsta']['keyAPI'] ) ){
				$this->setApiKey( $np['_nova_pohsta']['keyAPI'] );
			}else{
				$this->app->enqueueMessage('Ключ ApiKey Новой Почты не установлен' , 'warning');
				// throw new \Exception('Ключ ApiKey Новой Почты не установлен');
			}
		}
		return $this->ApiKey;
	}#END FN
	
}#END CLASS







/**
 *
 *
 *
 *
 *
 *
 */






























