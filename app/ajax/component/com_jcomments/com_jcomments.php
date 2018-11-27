<?php
	namespace xzlib\app\ajax\component\com_jcomments;
	
	use JFactory;
	use JLayoutFile;
	use JResponseJson;
	use JSession;
	use JText;
	
	/**
	 * Created by PhpStorm.
	 * User: oleg
	 * Date: 02.11.18
	 * Time: 20:54
	 */
	defined('_JEXEC') or die;
	
	class com_jcomments extends \xzlib\app\ajax\apl{
		
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
			self::$instance = $this;
		}#END FN
		
		/**
		 * Получить форму комментария для товара
		 *
		 * @throws \Exception
		 * @author    Gartes
		 * @since     3.8
		 * @copyright 02.11.18
		 */
		public function getFormComments ()
		{
			
			JSession::checkToken() or die( 'Invalid Token' );
			$app = JFactory::getApplication();
			$template = $app->getTemplate();
			
			$data = $app->input->getArray(array('opt' => ['prodId'=>'INT ']));
			
			$layout = new JLayoutFile( 'reviews' );
			$layout->addIncludePath( JPATH_THEMES . '/' . $template . '/html/lib_xzlib/com_virtuemart/productdetails/' );
			$html =  $layout->render( ['opt'=>$data['opt']] );
			
			echo new JResponseJson( [ 'html' => $html , ] , JText::_('ок!'));
			$app->close();
			
		}#END FN
		
	}#END CLASS






































