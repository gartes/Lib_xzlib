<?php

namespace xzlib\app\document\elements\forms\forms;

use Joomla\Registry\Registry as Registry;
use \xzlib\app\document\document as xzlibDoc;
use \xzlib\app\document\elements\forms\forms\forms as Forms;


class couponCodeForm extends Forms
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

	function renderForm ( $data )
	{
		$layout = new \JLayoutFile('couponCodeForm', JPATH_LIBRARIES . '/xzlib/app/document/elements/forms/forms/layouts');
		$html   = $layout->render($data); // $displayData


		return $html;
	}#END FN

}#END CLASS 