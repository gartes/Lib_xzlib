<?php

namespace xzlib\app\document\elements\forms\elements;

use Joomla\Registry\Registry as Registry;
use \xzlib\app\document\document as xzlibDoc;
use \xzlib\app\document\elements\forms\forms\forms as Forms;


class tos extends Forms
{
	public $registry;
	public static $instance;

	public static function instance ( $options = array() )
	{
		if (self::$instance === null)
		{
			self::$instance = new self($options);
		}

		return self::$instance;
	}#END FN 

	public function __construct ( $options = null )
	{
		$p = parent::instance();
		$this->loadString($p);
		self::$instance = $this;
	}#END FN

	public function render ()
	{
		$doc = \JFactory::getDocument();

		// Получить из параметров плагина plg_system_xlib ID Статьи для TOS
		$plg_xlib        = \JPluginHelper:: getPlugin($type = "system", $plugin = 'xlib');
		$plg_xlib_params = json_decode($plg_xlib->params);
		$tos_article_id  = (isset($plg_xlib_params->tos_article_id)) ? $plg_xlib_params->tos_article_id : 0;




		$option = array('defer' => true,);
		$src    = 'libraries/xzlib/app/document/elements/forms/elements/assets/tos.js';
		$this->addScript($src, $option);


		$doc->addScriptOptions('xzlib_app_document_elements_forms_elements_tos', array('article_id' => $tos_article_id));

		$data = array(
			'mediaVersion' => $this->get('ver', '00001x'),
		);

		/*// Подключить js библиотеки
		$xzlibDocument = new xzlibDoc();
		$option = array( 'defer' => true ,);
		$url = 'libraries/xzlib/app/document/assets/js/couponCodeForm.js';
		parent :: addScript ( $url , $option );*/

		$layout = new \JLayoutFile('tos', JPATH_LIBRARIES . '/xzlib/app/document/elements/forms/elements/layouts');
		$html   = $layout->render($data); // $displayData

		return $html;
	}#END FN
}