<?php namespace xzlib;

use Error;
use Exception;
use JResponseJson;
use Throwable;
use xzlib\app ;
use JFactory;
use JLoader;
use JLog;
use Joomla\Registry\Registry as Registry;
use JPath;

defined('_JEXEC') or die;
defined ('DS') or define('DS', DIRECTORY_SEPARATOR);
require_once JPATH_LIBRARIES . '/xzlib/vendor/autoload.php' ;




/**
 * @property \Joomla\CMS\Application\CMSApplication app
 * @package     xzlib\app\ajax
 *
 * @since       version 1
 */
class xzlib_app extends Registry
{

	var $app ;


	
	
	/**
	 * ajax_app constructor.
	 *
	 * @param null $options
	 *
	 * @throws Exception
	 *
	 * @since version 1
	 */
	public function __construct ( $options = null )
	{
		$DefaultParams = $this->_setDefault();
		parent::__construct($DefaultParams);

		$this->loadArray($options);

		$this->_setDebug();
		$this->xzLog($this);
		$this->app = JFactory::getApplication();

	}#END FN
	
	/**
	 * @param       $element
	 * @param array $options
	 *
	 * @return mixed
	 *
	 * @since version 3.7
	 */
	static function _get( $element  , $options = array() )
	{
		// self::loadLangLib();
		$obj = 'xzlib\\app\\'.$element.'\\'.$element;
		
		
		
		return  $obj::instance($options );
	}#END FN
	
	
	/**
	 * Ajax - точка входа в XZLIB Библиотеку
	 * 
	 * @author    Gartes
	 * @since     3.8
	 * @copyright 05.11.18
	 */
	static public function Ajax()
	{
		
		$app    = JFactory::getApplication();
		
		$data = array();
		
		$options = array();
		
		$component = $app->input->get( 'component' , false , 'word' );
		$task = $app->input->get( 'task' , false , 'word' );
		
		if( !$component ){
			echo new JResponseJson( '' , 'Не передан параметр "component"' , true );
			jExit();
			return ;
		}
		
		
		
		if ($component == 'api'){
			$apiName = $app->input->get( 'api' , false , 'word' );
			$task = $app->input->get('task' , false, 'WORD' ) ;
			
			
			
			try {
				// Code that may throw an Exception or Error.
				$obj = 'xzlib\\app\\api\\' . $apiName . '\\' . $apiName;
				$api = $obj::instance( $options = array() ) ;
				
			 
				return $api->$task();
				
				
			} catch ( Throwable $t) {
				// Executed only in PHP 7, will not match in PHP 5.x
				echo new JResponseJson( $t );
				jExit();
			} catch ( Exception $e) {
				
				
				// Executed only in PHP 5.x, will not be reached in PHP 7
				echo new JResponseJson( $e );
				jExit();
			}
			echo new JResponseJson( $data );
			jExit();
		}
		
		
		
	}#END FN
	
	/**
	 *
	 * @return array
	 *
	 * @since version 1
	 */
	protected function _setDefault ()
	{
		$params = $this->getXlibPlgParams();

		$optionsDEF = [
			'ver'         => '001.c',
			'debug'       => $params->debag_on,
			'comment'     => false,
			'cache'       => true,
			'cacheReload' => false,
		];

		return $optionsDEF;
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

		if ($fields)
		{
			return $plg_xlib_params->{$fields};
		}

		return $plg_xlib_params;
	}#END FN

	/**
	 * @author    Gartes
	 * @since     3.8
	 * @copyright 26.10.18
	 */
	public function xzLog ( $obj ){

		$log_path = JFactory::getConfig()->get('log_path');
		$cl = get_class( $obj );
		$cl = str_replace("xzlib\\app\\", "", $cl);
		$cl = str_replace("\\", ".", $cl);

		// echo'<pre>';print_r( $cl );echo'</pre>'.__FILE__.' '.__LINE__;

		JLog::addLogger(
			array(
				// Sets file name
				'text_file' => $cl.'.php' ,
				// Sets the format of each line
				'text_entry_format' => '{DATETIME} {PRIORITY} {CLIENTIP} {MESSAGE}' ,

				'text_file_path'  => $log_path.'/xzlib'
			),
			// Sets all but DEBUG log level messages to be sent to the file
			JLog::ALL & ~JLog::DEBUG,
			// The log category which should be recorded in this file
			array('my-debug-category')
		);

	}#END FN


	/**
	 * Установка DEBUG Xlib
	 *
	 * @since version 1
	 */
	protected function _setDebug ()
	{
		if ($this->get('debug', false) )
		{
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(E_ALL);
		}
	}#END FN





	/**
	 * Get debug message JLayoutFile object
	 *
	 * @param object $layout JLayoutFile
	 *
	 * @since version 3.7
	 *
	 * @return array Debug Message
	 */
	public function layoutFile_getDebugMessage ( $layout)
	{
		JLoader::import('joomla.filesystem.path');


		/** @var string $layoutId */
		$layoutId     = $layout->getLayoutId();

		/** @var array $includePaths */
		$includePaths =  $layout->getIncludePaths();


		$suffixes     = $layout->getSuffixes();
		$layout->addDebugMessage('<strong>Layout:</strong> ' . $layoutId );
		$layout->addDebugMessage('<strong>Include Paths:</strong> ' . print_r($includePaths, true));
		if ($suffixes)
		{
			$layout->addDebugMessage('<strong>Suffixes:</strong> ' . print_r($suffixes, true));
		}
		$rawPath  = str_replace('.', '/', $layoutId) . '.php';
		$layout->addDebugMessage('<strong>Searching layout for:</strong> ' . $rawPath);
		$foundLayout = JPath::find( $includePaths , $rawPath);
		$layout->addDebugMessage('<strong>Found layout:</strong> ' . $foundLayout);
		$message = $layout->renderDebugMessages();
		// $message = $layout->getDebugMessages();
		return $message;

	}#END FN

}#END  CLASS
