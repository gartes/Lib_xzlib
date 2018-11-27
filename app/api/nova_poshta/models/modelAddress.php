<?php namespace xzlib\app\api\nova_poshta\models;
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 06.11.18
 * Time: 7:30
 */

use JFactory;
Use NovaPoshta\Config;
use NovaPoshta\ApiModels\Address;
use NovaPoshta\MethodParameters\Address_getStreet;
use NovaPoshta\MethodParameters\Address_getWarehouses;
use NovaPoshta\MethodParameters\Address_getCities;
use NovaPoshta\MethodParameters\Address_getAreas;

class modelAddress extends \xzlib\app\api\nova_poshta\nova_poshta
{
	
	/**
	 * @param array $options
	 *
	 * @return \xzlib\app\api\api|modelAddress|\xzlib\app\api\nova_poshta\nova_poshta
	 * @throws \Exception
	 * @author    Gartes
	 * @since     3.8
	 * @copyright 07.11.18
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
	 * Сохранить Адрес контрагента
	 *
	 * @param $data
	 *
	 * @return \NovaPoshta\Models\DataContainerResponse
	 * @author    Gartes
	 * @since     3.8
	 * @copyright 14.11.18
	 */
	public static function save($data)
	{
		$address = new Address();
		$address->setCounterpartyRef($data->CounterpartyRef);
		$address->setBuildingNumber($data->BuildingNumber);
		$address->setFlat($data->Flat);
		$address->setNote($data->Note);
		$address->setStreetRef( $data->StreetRef );
		return $address->save();
	}
	
	/**
	 * Обновить адрес контрагента
	 *
	 * @return \NovaPoshta\Models\DataContainerResponse
	 * @author    Gartes
	 * @since     3.8
	 * @copyright 14.11.18
	 */
	public static function update($data)
	{
		$address = new Address();
		$address->setRef('e29115c8-6f59-11e4-acce-0050568002cf');
		$address->setCounterpartyRef('2718756a-b39b-11e4-a77a-005056887b8d');
		$address->setBuildingNumber('92');
		$address->setFlat('22');
		$address->setNote('Первый');
		$address->setStreetRef('c55c9056-4148-11dd-9198-001d60451983');
		return $address->update();
	}
	
	/**
	 * Получить список городов компании
	 * @param bool $Ref - ссылка города
	 *
	 * @return mixed|\NovaPoshta\Models\DataContainerResponse
	 * @author    Gartes
	 * @since     3.8
	 * @copyright 08.11.18
	 */
	public function getCity ( $Ref = false )
	{
		
		$data = new Address_getCities();
		// $data->setPage(1);
		# Поиск города по REF ссылке
		if ( $Ref ) { $data->setRef( $Ref ); }#END IF
		# Поиск города по подстроке
		# $data->setFindByString( 'Пол' );
		return Address::getCities( $data );
	}#END FN
	
	/**
	 *
	 * @return \NovaPoshta\Models\DataContainerResponse
	 * @throws \Exception
	 * @author    Gartes
	 * @since     3.8
	 * @copyright 06.11.18
	 */
	public function getWarehouses ($_Ref = false ){
		
		
		
		if ( !$_Ref  ){
			$app = JFactory::getApplication() ;
			$Ref = $app->input->getArray(
				array(
					'opt'=>array(
						'objCity'=>array(
							'Ref'=>'CMD',
						)
					)
				)
			);
			$_Ref = $Ref['opt']['objCity']['Ref'] ;
		}
		
	    
		
		$data = new Address_getWarehouses();
		$data->setCityRef( $_Ref );
		$data->setPage(1);
		
	    $Res_data =  Address::getWarehouses($data);
	    
	    
	    
		$_newArr = [];
	    foreach ($Res_data->data as $i=> $v){
		
		
		    $_newArr[$i] =$v ;
		    $_newArr[$i]->Description = str_replace('№' , '№ ' , $_newArr[$i]->Description ) ;
		    $_newArr[$i]->DescriptionRu = str_replace('№' , '№ ' , $_newArr[$i]->DescriptionRu ) ;
		
	    }#END IF
		$Res_data->data = $_newArr ;
	    
	    
	 
	    
		return $Res_data ; 
	
	}#END FN
	
	/**
	 * Поиск улицы в городе
	 *
	 * @return \NovaPoshta\Models\DataContainerResponse
	 * @throws \Exception
	 * @author    Gartes
	 * @since     3.8
	 * @copyright 11.11.18
	 */
	public function getStreet()
	{
		$app = \JFactory::getApplication() ; 
		$pData = $app->input->getArray(
			array(
				'opt'=>array(
					'StreetName'=>'STRING',
					'objCity'=> array (
						'Ref' => 'CMD'
					),
				)
			)
		);
		/*echo'<pre>';print_r( $pData );echo'</pre>'.__FILE__.' '.__LINE__;*/
		/*echo'<pre>';print_r( $pData['opt']['StreetName'] );echo'</pre>'.__FILE__.' '.__LINE__;*/
		
		
		$data = new Address_getStreet();
		$data->setCityRef($pData['opt']['objCity']['Ref']);
		$data->setFindByString($pData['opt']['StreetName']);
		$data->setPage(1);
		return Address::getStreet($data);
	}
	
	
	
	
}#END CLASS

























