<?php

namespace xzlib\app\document;


use JSession;
use xzlib\app\document\document;
use xzlib\app\document\form\models\fields;

/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

defined('_JEXEC') or die;

/**
 * @package     xzlib\app\document
 *
 * @since       version 4
 */
class form extends document
{
	
	
	/**
	 * Префикс для добавления к имени поля
	 *
	 * @var bool
	 * @since version
	 */
	private $_prefixField = false;
	/**
	 * Cтек полей формы
	 *
	 * @var array
	 * @since version
	 */
	private $_formField = array();
	/**
	 * @var bool
	 * @since version
	 */
	private $_Token = false;
	
	/**
	 * Массив полей с созданным FORMCODE
	 *
	 * @var array
	 * @since version
	 */
	private $_arrayRenderField = array();
	
	
	public static $instance;
	var $app;
	
	/**
	 * Instance method
	 *
	 * @param   array $options
	 *
	 * @return \xzlib\app\document\form
	 * @since   version 4
	 * @throws \Exception
	 */
	public static function instance ( $options = array() )
	{
		self::$instance = NULL;
		if( self::$instance === NULL )
		{
			self::$instance = new self($options);
		}
		
		return self::$instance;
	}#END FN
	
	
	/**
	 * form constructor.
	 *
	 * @param null $options
	 *
	 * @since version 4
	 *
	 * @throws \Exception
	 */
	public function __construct ( $options = NULL )
	{
		parent::__construct($options);
		
		$this->_formField        = array();
		$this->_Token            = false;
		$this->_arrayRenderField = array();

        $plg_xlib        = \JPluginHelper:: getPlugin($type = "system", $plugin = 'xlib');
        $plg_xlib_params = json_decode($plg_xlib->params);
        $recapcha = $plg_xlib_params->recapcha ;



		self::$instance = $this;
		
		$option = array( 'defer' => true, );
		$src    = 'libraries/xzlib/app/document/form/assets/js/form.js';
		$this->addScript($src, $option);



		
	}#END FN
	
	private function initForm ()
	{
	
	}#END FN
	
	
	/**
	 * Добавить в стек полей формы массив полей
	 *
	 * @param array $arrayField
	 *
	 * @return \xzlib\app\document\form
	 *
	 * @since version 4
	 */
	public function addField ( $arrayField = array() )
	{
		foreach ( $arrayField as $Field )
		{
			array_push($this->_formField, $Field);
		}#END FOREACH
		
		
		return self::$instance;
	}#END FN
	
	
	private function _addToken ()
	{
		$token_name = JSession::getFormToken();
		
		// $cls =  new \stdClass();
		$cls[ 'name' ]     = $token_name;
		$cls[ 'value' ]    = 1;
		$cls[ 'type' ]     = 'hidden';
		$cls[ 'hidden' ]   = 1;
		$cls[ 'formcode' ] = '<input type="hidden" name="' . $token_name . '" value="1">';
		
		array_push($this->_arrayRenderField, $cls);
		
		
	}#END FN
	
	/**
	 * @param $ArrFild
	 *
	 * @return \xzlib\app\document\form
	 *
	 * @since version
	 */
	public function addNewField ( $ArrFild )
	{
		
		$fieldsModel = new fields();
		$Field       = $fieldsModel->createFild($ArrFild);
		array_push($this->_formField, $Field);
		
		
		return self::$instance;
	}
	
	public function removeField ( $nameField )
	{
		
		foreach ( $this->_formField as $i => $field )
		{
			if( $field->name == $nameField )
			{
				unset($this->_formField[ $i ]);
				sort($this->_formField);
				
			}
		}
		
		return self::$instance;
	}#END FN
	
	
	/**
	 *
	 *
	 * @since version
	 * @throws \Exception
	 */
	public function renderFormcode ()
	{
		
		$html = '';
		foreach ( $this->_arrayRenderField as $field )
		{
			$html .= $field[ 'formcode' ];
		}
		
		if($this->_prefixField){
			$html .= '<input type="hidden" name="prefixField" value="'.$this->_prefixField.'">';
		}
		
		
		
		
		return $html;
	}#END FN
	
	/**
	 * Добавление атрибута к полю
	 *
	 * @param $fieldName
	 * @param $attrName
	 * @param $value
	 *
	 * @return \xzlib\app\document\form
	 *
	 * @since version
	 */
	public function addAttrField ( $fieldName, $attrName, $value )
	{
		
		$fieldsModel      = new fields();
		$this->_formField = $fieldsModel->addAttrField($this->_formField, $fieldName, $attrName, $value);
		
		return self::$instance;
	}
	
	/**
	 * Рендер полей пользователя в FORMCODE
	 *
	 * @param array  $arrayField
	 *
	 *
	 * @param null   $_userDataIn
	 * @param string $_prefix
	 *
	 * @return \xzlib\app\document\form
	 * @throws \Exception
	 * @since version
	 */
	public function renderFields ( $arrayField = array(), $_userDataIn = NULL, $_prefix = false )
	{
		
		if( !$_prefix && !$this->_prefixField )
		{
			$_prefix            = '';
			
		}
		else if( $this->_prefixField )
		{
			$_prefix = $this->_prefixField;
		}
		else if($_prefix)
		{
			$this->_prefixField = $_prefix;
		}#EN IF
		
		
		
		if( count($arrayField) )
		{
			$this->addField($arrayField);
		}
		$fieldsModel = new fields();
		$Filds       = $fieldsModel->renderFields($this->_formField, $_userDataIn, $_prefix, $wrapperStyle = 'mdl');
		
		
		foreach ( $Filds[ 'fields' ] as $Field )
		{
			array_push($this->_arrayRenderField, $Field);
		}#END FOREACH
		
		if( !$this->_Token )
		{
			$this->_Token = true;
			$this->_addToken();
		}
		
		return self::$instance;
		
	}#END FN
	
	
}#END CLASS