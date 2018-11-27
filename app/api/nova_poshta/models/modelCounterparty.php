<?php
	
	namespace xzlib\app\api\nova_poshta\models;
	/**
	 * Created by PhpStorm.
	 * User: oleg
	 * Date: 07.11.18
	 * Time: 9:00
	 */
	
	use NovaPoshta\ApiModels\Counterparty;
	use NovaPoshta\MethodParameters\Counterparty_getCounterparties;
	use NovaPoshta\MethodParameters\Counterparty_getCounterpartyAddresses;
	use NovaPoshta\MethodParameters\Counterparty_getCounterpartyContactPersons;
	use NovaPoshta\MethodParameters\Counterparty_getCounterpartyOptions;
	use NovaPoshta\MethodParameters\Counterparty_getCounterpartyByEDRPOU;
	use NovaPoshta\MethodParameters\Counterparty_cloneLoyaltyCounterpartySender;
	use NovaPoshta\MethodParameters\MethodParameters;
	
	/**
	 * Class modelCounterparty
	 * @package xzlib\app\api\nova_poshta\models
	 */
	class modelCounterparty extends \xzlib\app\api\nova_poshta\nova_poshta
	{
		
		
		
		/**
		 * @param array $options
		 *
		 * @return \xzlib\app\api\api|modelCounterparty|\xzlib\app\api\nova_poshta\nova_poshta
		 * @throws \Exception
		 * @author    Gartes
		 * @since     3.8
		 * @copyright 11.11.18
		 */
		public static $instance ;
		public static function instance( $options =  array () )
		{
			if (self::$instance === null)
			{
				self::$instance = new self( $options );
			}
			return self::$instance;
		}#END FN
		
		/**
		 * Создать котрагента
		 * 
		 * @return \NovaPoshta\Models\DataContainerResponse
		 * @author    Gartes
		 * @since     3.8
		 * @copyright 11.11.18
		 */
		public static function save ( $data )
		{
			$counterparty = new Counterparty(); // $data['']
			$counterparty->setCounterpartyProperty( $data[ 'CounterpartyProperty' ] );
			$counterparty->setCityRef( $data[ 'CityRef' ] );
			$counterparty->setCounterpartyType( 'PrivatePerson' );
			$counterparty->setFirstName( $data[ 'FirstName' ] );
			$counterparty->setLastName( $data[ 'LastName' ] );
			$counterparty->setPhone( $data[ 'Phone' ] );
			
			if ( isset( $data[ 'MiddleName' ] ) )
			{
				$counterparty->setMiddleName( $data[ 'MiddleName' ] );
			}#END IF
			
			if ( isset( $data[ 'Email' ] ) )
			{
				$counterparty->setEmail( $data[ 'Email' ] );
			}#END IF
			
			$res = $counterparty->save();
			
			if ( $res->success )
			{
				$res->info[] = 'Конрагент создан в кабинете Новой Почты';
				return $res;
			}#END IF
			
			$res = parent::logLanguage($res);
			
			return $res;
			
		}#END FN
		
		/**
		 * Обновить данные Контрагента Сервер Новой почты
		 *
		 * @return \NovaPoshta\Models\DataContainerResponse
		 * @author    Gartes
		 * @since     3.8
		 * @copyright 13.11.18
		 */
		public static function update($data)
		{
			$counterparty = new Counterparty();
			$counterparty->setRef($data->Ref);
			$counterparty->setCounterpartyProperty($data->CounterpartyProperty);
			$counterparty->setCityRef($data->CityRef );
			$counterparty->setCounterpartyType($data->CounterpartyType);
			$counterparty->setFirstName($data->FirstName);
			$counterparty->setLastName($data->LastName);
			$counterparty->setMiddleName($data->MiddleName);
			$counterparty->setPhone($data->Phone);
			#$counterparty->setEmail('test2@i.ua');
			
			$res = $counterparty->update();
			$res->info[] = 'modelCounterparty - update';
			return $res;
		}
		
		
		/** 
		 * Загрузить все данные контрагента 
		 * 
		 * @return \NovaPoshta\Models\DataContainerResponse
		 * @author    Gartes
		 * @since     3.8
		 * @copyright 11.11.18
		 */
		public function getCounterpartiesAllData ()
		{
			# Загрузить список сонтрагентов
			$Counterparties = $this->getCounterparties();
			
			if ( !$Counterparties->success )
			{
				return $Counterparties;
			}
			
			foreach ( $Counterparties->data as $i => $obj )
			{
				
				# Загрузить список контактных лиц Контрагента
				$ContactPersons = $this->getCounterpartyContactPersons( $obj );
				if ( !$ContactPersons->success )
				{
					return $Counterparties;
				}
				$Counterparties->data[ $i ]->ContactPersons = $ContactPersons;
				
				
				
				
				
				
				
			}#END FOREACH
			
			return $Counterparties;
		}#END FN
		
		
		/**
		 * Загрузить список Контрагентов отправителей/получателей/третье лицо
		 *
		 * @return \NovaPoshta\Models\DataContainerResponse
		 * @author    Gartes
		 * @since     3.8
		 * @copyright 07.11.18
		 */
		public function getCounterparties ($CounterpartyProperty = false )
		{
			if (!$CounterpartyProperty){
				$opt  = $this->app->input->getArray(
					[
						'opt' => [
							'CounterpartyProperty' => 'WORD',
						],
					]
				);
				$CounterpartyProperty = $opt[ 'opt' ][ 'CounterpartyProperty' ];
			}#END IF
			
			$data = new Counterparty_getCounterparties();
			$data->setCounterpartyProperty( $CounterpartyProperty );
			// $data->setPage(1);
			// $data->setCityRef('8d5a980d-391c-11dd-90d9-001a92567626');
			// $data->setFindByString('Петр');
			// или
			// $data->setRef('1c8c1c073415a661cdee2915d3ffc533');
			
			$res = Counterparty::getCounterparties( $data );
			
			
			
			
			
			
			return $res;
		}#END FN
		
		public function getCounterpartyContactPersons ( $objCounterpartie )
		{
			$data = new Counterparty_getCounterpartyContactPersons();
			$data->setRef( $objCounterpartie->Ref );
			$data->setPage( '1' );
			
			return Counterparty::getCounterpartyContactPersons( $data );
			
		}#END FN
		
		/**
		 * Загрузить список адресов Контрагентов
		 *
		 * @param $obj
		 *
		 * @return \NovaPoshta\Models\DataContainerResponse
		 * @author    Gartes
		 * @since     3.8
		 * @copyright 26.11.18
		 *
		 */
		public function getCounterpartyAddresses($Ref)
		{
			 
			$data = new Counterparty_getCounterpartyAddresses();
			$data->setRef($Ref);
			$data->CounterpartyProperty = 'Recipient';
			return Counterparty::getCounterpartyAddresses($data);
		}#END FN
		
		
		
		
		
		
	}#END CLASS
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	