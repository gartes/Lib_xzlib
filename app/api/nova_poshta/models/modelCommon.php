<?php
	
	namespace xzlib\app\api\nova_poshta\models;
	/**
	 * Created by PhpStorm.
	 * User: oleg
	 * Date: 07.11.18
	 * Time: 16:07
	 */
	
	use NovaPoshta\ApiModels\Common;
	use NovaPoshta\MethodParameters\Common_getCargoDescriptionList;
	use NovaPoshta\MethodParameters\Common_getTimeIntervals;
	
	class modelCommon extends \xzlib\app\api\nova_poshta\nova_poshta {
		/**
		 * @param array $options
		 *
		 * @return \xzlib\app\api\api|modelCommon|nova_poshta|\xzlib\app\api\nova_poshta\nova_poshta
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
		 * Тип груза
		 *
		 * @return \NovaPoshta\Models\DataContainerResponse
		 * @author    Gartes
		 * @since     3.8
		 * @copyright 07.11.18
		 */
		public static function getCargoTypes()
		{
			return Common::getCargoTypes();
		}
		
		/**
		 * Технология доставки
		 *
		 * @return \NovaPoshta\Models\DataContainerResponse
		 * @author    Gartes
		 * @since     3.8
		 * @copyright 07.11.18
		 */
		public static function getServiceTypes()
		{
			return Common::getServiceTypes();
		}#END FN
		
		/**
		 * Виды плательщиков услуги доставки
		 *
		 * @return \NovaPoshta\Models\DataContainerResponse
		 * @author    Gartes
		 * @since     3.8
		 * @copyright 07.11.18
		 */
		public static function getTypesOfPayers()
		{
			return Common::getTypesOfPayers();
		}
		
		
		
		
		
		
		
		
		
		
	}#END CLASS