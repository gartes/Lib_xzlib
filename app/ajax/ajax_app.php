<?php

namespace xzlib\app\ajax;

use JLoader;
use Joomla\Registry\Registry as Registry;
use JPath;

defined('_JEXEC') or die;

/**
 * @package     xzlib\app\ajax
 *
 * @since       version 1
 */
class ajax_app extends Registry
{

	/**
	 * ajax_app constructor.
	 *
	 * @param null $options
	 *
	 * @throws \Exception
	 *
	 * @since version 1
	 */
	public function __construct ( $options = null )
	{
		$DefaultParams = $this->_setDefault();
		parent::__construct($DefaultParams);
		$this->loadArray($options);
		$this->_setDebug();
		$this->app = \JFactory::getApplication();

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
			'ver'         => '001.b',
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
	 * Установка DEBUG Xlib
	 *
	 * @since version 1
	 */
	protected function _setDebug ()
	{
		if ($this->get('debug', false))
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
