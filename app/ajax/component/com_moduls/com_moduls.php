<?php

namespace xzlib\app\ajax\component\com_moduls;

use JLayoutFile;
use JLoader;
use Joomla\Registry\Registry as Registry;
use JPath;


defined('_JEXEC') or die;

/**
 * @package     xzlib\app\ajax\component\com_moduls
 *
 * @since       version
 */
class com_moduls extends \xzlib\app\ajax\ajax_app
{

	
	public $app ;
	
	public static $instance;
	/**
	 * Instance method
	 * @param   array $options
	 * @return  com_moduls
	 * @since   version 1
	 * @throws \Exception
	 */
	public static function instance ( $options = array() )
	{
		if (self::$instance === null)
		{
			self::$instance = new self($options);
		}

		return self::$instance;
	}#END FN

	/**
	 * Construct method
	 * @param   array $options
	 * @since   version 1
	 * @throws \Exception
	 */
	public function __construct ( $options = array() )
	{
		parent::__construct($options);
		self::$instance = $this;
	}#END FN

	/**
	 * Перезагрузка модулей страницы
	 *
	 * @since version1
	 */
	public function modulBlock_Load ()
	{
		$jinput      = $this->app->input;
		$elementsArr = $jinput->get('elements', array(), 'array');

		foreach ($elementsArr as $i => $elem)
		{
			$elementsArr[$i] = $this->_getViewModilBlock($elem['view']);
		}

		return array('elements' => $elementsArr);
	}

	/**
	 * Перезагрузка модулей страницы
	 *
	 * @param   string $view layout name
	 * @return array
	 * @since   version 1
	 */
	private function _getViewModilBlock ( $view )
	{

		$template = $this->app->getTemplate();
		$data     = array();
		$layout   = new \JLayoutFile( $view, /*JPATH_LIBRARIES . '/xzlib/app/components/com_moduls/layouts'*/ null ,  array('debug' => false));
		$layout->addIncludePath(JPATH_THEMES . '/' . $template . '/html/lib_xzlib/modules/blocks/');
		// $layout->addIncludePath(JPATH_THEMES . '/' . $template . '/html/lib_xzlib/modules/blocks/'.$view);

		$returnData = ['html' => $layout->render($data)]; // $displayData

		if ($this->get('debug', false))
		{
			$returnData['debug'] = $this->layoutFile_getDebugMessage($layout);
		}

		return $returnData;
	}#END FN
	






}#END CLASS