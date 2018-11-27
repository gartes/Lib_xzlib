<?php namespace xzlib\app\api\nova_poshta\models;
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 06.11.18
 * Time: 7:30
 */

use JFactory;
use NovaPoshta\ApiModels\InternetDocument;
use NovaPoshta\Models\CounterpartyContact;
use NovaPoshta\MethodParameters\InternetDocument_getDocumentList;

class modelInternetDocument extends \xzlib\app\api\nova_poshta\nova_poshta
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
	
	public static function save($senderData , $senderContact, $method, $formNovaPochta , $virtuemart_order_id  )
	{
		
		//  echo'<pre>';print_r( $formNovaPochta  );echo'</pre>'.__FILE__.' '.__LINE__;
		
		
		$sender = new CounterpartyContact();
		$sender->setCity( $method->CitySender );
		$sender->setRef( $senderData->Ref );
		$sender->setAddress( $method->SenderAddress );
		$sender->setContact( $senderContact->Ref );
		$sender->setPhone( $senderContact->Phones );
		
		$recipient = new CounterpartyContact();
		$recipient->setCity( $formNovaPochta[ 'CityRecipient' ] );
		$recipient->setRef( $formNovaPochta[ 'Recipient' ] );
		
		
		
		if ( $formNovaPochta['ServiceType'] =='WarehouseDoors'){
			
			$recipient->setAddress( $formNovaPochta['RecipientAddressDoors'] );
		}else{
			$recipient->setAddress( $formNovaPochta[ 'RecipientAddress' ] );
		}
		
		
		
		
		
		$recipient->setContact( $formNovaPochta[ 'ContactRecipient' ] );
		$recipient->setPhone( $formNovaPochta[ 'RecipientsPhone' ] );
		
		$internetDocument = new InternetDocument();
		$internetDocument->setSender( $sender );
		$internetDocument->setRecipient( $recipient );
		
		
	
		
		#значение из справочника Тип плательщика*
		$internetDocument->setPayerType( $formNovaPochta[ 'PayerType' ] );
		#Форма оплаты
		$internetDocument->setPaymentMethod('Cash');
		
		#Дата отправки в формате дд.мм.гггг*
		$internetDocument->setDateTime( str_replace('-','.',$formNovaPochta['DateTime']) );
		#Тип груза
		$internetDocument->setCargoType($method->CargoType);
		
		# Загальний об'єм відправлення*
		$internetDocument->setVolumeGeneral($formNovaPochta['VolumeGeneral']);
		
		#Вес фактический, кго
		$internetDocument->setWeight($formNovaPochta['Weight']);
		#Значение из справочника Технология доставки*
		$internetDocument->setServiceType( $formNovaPochta[ 'ServiceType' ] );
		# Кількість місць*
		$internetDocument->setSeatsAmount($formNovaPochta['SeatsAmount']);
		#Текстовое поле, вводиться для доп. описания
		$internetDocument->setDescription('ID -' . $virtuemart_order_id );
		# Целое число, объявленная стоимость (если объявленная стоимость не указана, API автоматически подставит минимальную объявленную цену - 300.00
		$internetDocument->setCost($formNovaPochta['BackwardDeliveryData']['RedeliveryString']);
		
		
		if ($formNovaPochta['BackwardDeliveryData']['BackwardDeliveryData_On']){
			$backwardDelivery = self::createBackwardDelivery( $formNovaPochta );
			$internetDocument->addBackwardDeliveryData($backwardDelivery);
		}
		
		/*echo'<pre>';print_r( $formNovaPochta );echo'</pre>'.__FILE__.' '.__LINE__;
		echo'<pre>';print_r( $internetDocument );echo'</pre>'.__FILE__.' '.__LINE__;
		die(__FILE__ .' Lines '. __LINE__ );*/
		
		
		
		$res = $internetDocument->save();
		return $res;
		
//		echo'<pre>';print_r( $res  );echo'</pre>'.__FILE__.' '.__LINE__;
//		echo'<pre>';print_r( $internetDocument );echo'</pre>'.__FILE__.' '.__LINE__;
//		echo'<pre>';print_r( str_replace('-','.',$formNovaPochta['DateTime']) );echo'</pre>'.__FILE__.' '.__LINE__;
//
//		echo'<pre>';print_r( $method );echo'</pre>'.__FILE__.' '.__LINE__;
//		echo'<pre>';print_r( $sender );echo'</pre>'.__FILE__.' '.__LINE__;
//		echo'<pre>';print_r( $recipient );echo'</pre>'.__FILE__.' '.__LINE__;
//		die(__FILE__ .' Lines '. __LINE__ );
		
		
		
		
		
		/*
		
		$backwardDeliveryData2 = new \NovaPoshta\Models\BackwardDeliveryData();
		$backwardDeliveryData2->setPayerType('Recipient');
		$backwardDeliveryData2->setCargoType('Documents');
		$backwardDeliveryData2->setRedeliveryString('Тех. документация');*/
		
		
		/*$internetDocument->setPreferredDeliveryDate('20.06.2015');
		$internetDocument->setTimeInterval('CityDeliveryTimeInterval2');
		$internetDocument->setPackingNumber('55');
		$internetDocument->setInfoRegClientBarcodes('55552');
		$internetDocument->setSaturdayDelivery('true');
		$internetDocument->setNumberOfFloorsLifting('12');
		$internetDocument->setAccompanyingDocuments('Большая корзина');
		$internetDocument->setAdditionalInformation('Стекло');
		$internetDocument->addBackwardDeliveryData($backwardDeliveryData1);
		$internetDocument->addBackwardDeliveryData($backwardDeliveryData2);*/
		
	}#END FN
	
	/**
	 * Создать обратную доставку
	 * 
	 * @param $formNovaPochta
	 *
	 * @return \NovaPoshta\Models\BackwardDeliveryData
	 * @author    Gartes
	 * @since     3.8
	 * @copyright 26.11.18
	 *            
	 */
	private static function createBackwardDelivery ($formNovaPochta){
		
		$backwardDeliveryData = new \NovaPoshta\Models\BackwardDeliveryData();
		$backwardDeliveryData->setPayerType($formNovaPochta['BackwardDeliveryData']['PayerType']);
		$backwardDeliveryData->setCargoType($formNovaPochta['BackwardDeliveryData']['CargoType']);
		$backwardDeliveryData->setRedeliveryString($formNovaPochta['BackwardDeliveryData']['RedeliveryString']);
		
		return $backwardDeliveryData ;
		
	}
	
	
	
}#END CLASS

























