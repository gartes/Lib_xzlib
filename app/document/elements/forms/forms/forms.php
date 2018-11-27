<?php

namespace xzlib\app\document\elements\forms\forms;

use Joomla\Registry\Registry as Registry;
use \xzlib\app\document\document as xzlibDoc;


class forms extends xzlibDoc
{
	
	
	public static $instance;
	
	public static function instance ( $options = array() )
	{
		if( self::$instance === NULL )
		{
			self::$instance = new self($options);
		}
		
		return self::$instance;
	}#END FN 
	
	public function __construct ( $options = NULL )
	{
		$p = parent::instance();
		$this->loadString($p);
		self::$instance = $this;
	}#END FN
	
	
	/**
	 * cтек элементов формы
	 *
	 */
	private $_formElement = array();
	
	/**
	 * cтек полей формы
	 *
	 */
	private $_formField = array();
	
	/**
	 * Имя формы
	 *
	 */
	private $_form = NULL;
	
	/**
	 *
	 *
	 */
	public function render ()
	{
		
		
		$src = 'libraries/xzlib/app/document/elements/forms/elements/assets/js/couponCodeForm.js';
		$this->addScript($src, [ 'defer' => true, ]);
		
		$formBody              = array();
		$formBody[ 'Field' ]   = $this->_renderField();
		$formBody[ 'Element' ] = $this->_renderformElement();
		
		$f = $this->getForm()->renderForm($formBody);
		
		
		return $f; ///  implode("", $formBody) ;
	}#END FN
	
	/**
	 * рендер Елементов формы
	 *
	 */
	private function _renderformElement ()
	{
		
		$returnHtml = '';
		foreach ( $this->_formElement as $formElement )
		{
			$returnHtml .= $this->getFormElement($formElement[ 'element' ], $formElement[ 'options' ])->render();
		}
		
		return $returnHtml;
	}#END FN
	
	/**
	 * рендер полей формы
	 *
	 */
	private function _renderField ()
	{
		$returnHtml = '';
		foreach ( $this->_formField as $formField )
		{
			
			foreach ( $formField[ 'fields' ] as $field )
			{
				$field[ 'formcode' ] = str_replace("<input ", '<input placeholder="' . $field[ 'title' ] . '" ', $field[ 'formcode' ]);
				$returnHtml          .= $field[ 'formcode' ];
			}#END FOREACH
			
		}#END FOREACH
		
		return $returnHtml;
	}#END FN
	
	/**
	 * Установить Имя формы
	 *
	 *
	 */
	public function setForm ( $formName )
	{
		$this->_form = $formName;
		
		return self::$instance;
	}#END FN
	
	/**
	 * Добавить в cтек форму элемент
	 *
	 *
	 */
	public function addFormField ( $fields, $options = array() )
	{
		$this->_formField[] = array( 'fields' => $fields[ 'fields' ], 'options' => $options );
		
		return self::$instance;
	}#END FN
	
	
	/**
	 * Добавить в cтек форму элемент
	 *
	 *
	 */
	public function addFormElement ( $Element, $options = array() )
	{
		$this->_formElement[] = array( 'element' => $Element, 'options' => $options );
		
		return self::$instance;
	}#END FN
	
	
	/**
	 *  getFormElement - Получить элемент формы
	 *
	 *
	 *
	 *
	 *
	 */
	public function getFormElement ( $element, $options = array() )
	{
		$obj = 'xzlib\\app\\document\\elements\\forms\\elements\\' . $element;
		
		return $obj::instance($options);
	}#END FN
	
	/**
	 * Получение пустой формы
	 *
	 *
	 */
	public function getForm ( $options = array() )
	{
		$obj = 'xzlib\\app\\document\\elements\\forms\\forms\\' . $this->_form;
		
		return $obj::instance($options);
	}#END FN
	
}