<?php
	
	namespace xzlib\app\api;
	/**
	 * Created by PhpStorm.
	 * User: oleg
	 * Date: 04.11.18
	 * Time: 14:08
	 */
	class api extends \xzlib\xzlib_app
	{
		
		public static $instance;
		
		/**
		 * @param array $options
		 *
		 * @return api
		 * @throws \Exception
		 * @author    Gartes
		 * @since     3.8
		 * @copyright 06.11.18
		 */
		public static function instance ( $options = [] )
		{
			if ( self::$instance === null )
			{
				self::$instance = new self( $options );
			}#END IF
			return self::$instance;
		}#END FN
		
		/**
		 * api constructor.
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
		 * @param $name
		 * @param $arguments
		 *
		 * @throws apiException
		 * @author    Gartes
		 * @since     3.8
		 * @copyright 06.11.18
		 */
		public function __call ( $name, $arguments )
		{
			if ( !method_exists( $this, $name ) )
			{
				throw new apiException( $name . ' method does not exist' );
			}#END IF
		}#END FN
		
		
		/**
		 * getApi - Запуск API
		 *
		 * @param       $element
		 * @param array $options
		 *
		 * @return mixed
		 *
		 * @since version
		 */
		public function getApi ( $element, $options = [] )
		{
			$obj = 'xzlib\\app\\api\\' . $element . '\\' . $element;
			
			
			
			return $obj::instance( $options );
		}#END FN
		
		
	}#END CLASS

































