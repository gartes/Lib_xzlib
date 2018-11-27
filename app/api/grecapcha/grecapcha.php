<?php namespace xzlib\app\api\grecapcha;

use JFactory;
use JPluginHelper;
use JRegistry;

class grecapcha extends \xzlib\app\api\api {
	
	
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
	 * Загрузка файла API g-Recapcha
	 * @author    Gartes
	 * @since     3.8
	 * @copyright 05.11.18
	 */
	public function loadScript ()
	{
		
		if ( $this->app->isAdmin() )
		{
			return;
		}#END IF
		
		$xlibPLG  = JPluginHelper::getPlugin( 'system', 'xlib' );
		$params   = new JRegistry( $xlibPLG->params );
		$recapcha = $params->get( 'recapcha', false );
		
		$doc = JFactory::getDocument();
		$doc->addScript( 'https://www.google.com/recaptcha/api.js?render=' . $recapcha->public_key, [], [ 'async' => 'async', 'defer' => 1 ] );
	}#END FN
	
	/**
	 * Проверка настроек G-Recapcha в плагине system.xlib
	 * 
	 * @author    Gartes
	 * @since     3.8
	 * @copyright 04.11.18
	 *            
	 */
	public function controlGrecapcha ()
	{
		
		$xlibPLG = JPluginHelper::getPlugin( 'system', 'xlib' );
		$params  = new JRegistry( $xlibPLG->params );
		$recapcha = $params->get( 'recapcha', false );
		
		#Не установлены ключи
		if ( !$recapcha || empty( $recapcha->public_key ) || empty( $recapcha->privat_key ) )
		{
			#Сообщение для установки ключей
			$this->createMesNidleKey();
		}#END IF
	}#END FN
	
	/**
	 * Создать сообщене для добавлени клбючей рекапчи
	 * @author    Gartes
	 * @since     3.8
	 * @copyright 04.11.18
	 */
	private function createMesNidleKey (){
		
		$doc = JFactory::getDocument();
		
		$xlibPLG = JPluginHelper::getPlugin('system','xlib');
		
		$option = $this->app->input->get( 'option' , false , 'WORD' );
		$view = $this->app->input->get( 'view' , false , 'WORD' );
		$layout = $this->app->input->get( 'layout' , false , 'WORD' );
		$extension_id = $this->app->input->get( 'extension_id' , false , 'INT' );
		
		if ( $option == 'com_plugins' && $view == 'plugin' && $layout == 'edit' && $extension_id == $xlibPLG->id )
		{
			$this->app->enqueueMessage( 'Зарегистрируйте сайт и установите сюда ключи ' , 'INFO' );
			$doc->addScriptDeclaration(
				'document.addEventListener("DOMContentLoaded", function () {
					setTimeout(()=>{
						jQuery(\'[href="#attrib-PLG_XLIB_RECAPCHA"]\').trigger(\'click\');
					}, 500)
					
				});'
			);
		}
		else
		{
			$aRef = \JURI::root() . 'administrator/index.php?option=com_plugins&view=plugin&layout=edit&extension_id=' . $xlibPLG->id;
			$a    = '<a href="' . $aRef . '">Перейти к настройкам</a>';
			$this->app->enqueueMessage( 'Необходимо установить ключи g-recapcha  ' . $a, 'warning' );
		}
		
		
	}#END FN
	
}#END CLASS