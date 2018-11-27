<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace xzlib\app\document\form\models ;



use JFactory;
use vmAccess;
use vmText;

class fields {
	
	private $_defailt_array_fild = array(
		'description'   => '' ,
		'maxlength'     => 255 ,
		'size'          => 30 ,
		'required'      => 0 ,
		'cols'          => 32 ,
		'rows'          => 5 ,
		'value'         => '',
		'default'       => '',
		'readonly'      => 0 ,
		'published'     => 1 ,
	);
	
	public function createFild( $array_fild_new ){
		
		
		$array_fild = array_merge ($this->_defailt_array_fild , $array_fild_new );
		
		
		$cls =  new \stdClass();
		$cls->name = $array_fild['name'];
		$cls->title = $array_fild['title'];
		$cls->type = $array_fild['type'];
		
		$cls->description = $array_fild['description'];
		
		$cls->maxlength = $array_fild['maxlength'];
		$cls->size = $array_fild['size'];
		$cls->required =   $array_fild['required'] ;
		$cls->cols = $array_fild['cols'];
		$cls->rows = $array_fild['rows'];
		$cls->value =  $array_fild['value']  ;
		$cls->default = $array_fild['default'];
		$cls->readonly =  $array_fild['readonly']  ;
		
		
		return $cls ;
		
	}#END FN
	
	/**
	 * Добавление атрибута к полю
	 *
	 * @param $_formField - Массив обектов полей
	 * @param $fieldName  - Название поля
	 * @param $attrName   - Имя аребута
	 * @param $value      - Значение
	 *
	 *
	 * @since version
	 * @return array      - Массив обектов полей
	 */
	public function addAttrField( $_formField ,  $fieldName , $attrName , $value  ){
		
		$retArr = array ();
		foreach ( $_formField as $i => $Field)
		{
			if($Field->name == $fieldName  ){
				$Field->{$attrName} = $value ;
			}#END IF
			$retArr[$i] = $Field ;
		}#END FOREACH
		
		
		return $retArr ;
	}#END FN
	
	
	/**
	 *
	 *
	 *
	 * @param array  $formField
	 * @param null   $_userDataIn
	 * @param string $_prefix
	 *
	 * @return array
	 * @throws \Exception
	 * @since version
	 */
	public function renderFields( $formField = [] , $_userDataIn = null , $_prefix = '' , $wraperStyle = 'mdl' ){
		
		
		$admin = vmAccess::manager();
		
		//We copy the input data to prevent that objects become arrays
		if(empty($_userDataIn)){
			$_userData = array();
		} else {
			$_userData = $_userDataIn;
			$_userData=(array)($_userData);
		}
		
		
		$_return = array(
			'fields' => array()
		,'functions' => array()
		,'scripts' => array()
		,'links' => array()
		);
		
		
		
		if (is_array( $formField )) {
			foreach ( $formField as $_fld) {
				
				$yOffset = 0;
				
				if(!empty($_userDataIn) and isset($_fld->default) and $_fld->default!=''){
					if(is_array($_userDataIn)){
						if(!isset($_userDataIn[$_fld->name]))
							$_userDataIn[$_fld->name] = $_fld->default;
					} else {
						if(!isset($_userDataIn->{$_fld->name}))
							$_userDataIn->{$_fld->name} = $_fld->default;
					}
				}
				
				$valueO = $valueN = (($_userData == null || !array_key_exists($_fld->name, $_userData))
					? vmText::_($_fld->default)
					: $_userData[$_fld->name]);
				
				//TODO htmlentites creates problems with non-ascii chars, which exists as htmlentity, for example äöü
				
				if ((!empty($valueN)) && (is_string($valueN))) /** @var TYPE_NAME $valueN */
					$valueN = htmlspecialchars($valueN,ENT_COMPAT, 'UTF-8', false);	//was htmlentities
				
				
				
				$_return['fields'][$_fld->name] = array(
					'name' => $_prefix . $_fld->name
					,'value' => $valueN // htmlspecialchars (was htmlentities) encoded value for all except editorarea and plugins
					,'unescapedvalue'=> $valueO
					,'title' => vmText::_($_fld->title)
					,'type' => $_fld->type
					,'required' => $_fld->required
					,'hidden' => false
					,'formcode' => ''
					,'description' => vmText::_($_fld->description)
					,'register' => (isset($_fld->register)? $_fld->register:0)
					,'htmlentities' => true  // to provide version check agains previous versions
				);
				
				$readonly = '';
				if(!$admin){
					if($_fld->readonly ){
						$readonly = ' readonly="readonly" ';
					}
				}
				
				
				
				switch( $_fld->name ) {
					
					
					case 'password':
					case 'password2':
					
					break;
					
					
					default:
						switch( $_fld->type ) {
							case 'hidden':
								
								$_return['fields'][$_fld->name]['formcode'] = '<input type="hidden" id="'
									. $_prefix.$_fld->name . '_field" name="' . $_prefix.$_fld->name.'" size="' . $_fld->size
									. '" value="' . $_return['fields'][$_fld->name]['value'] .'" '
									. ($_fld->required ? ' class="required"' : '')
									. ($_fld->maxlength ? ' maxlength="' . $_fld->maxlength . '"' : '')
									. $readonly . ' /> ';
								$_return['fields'][$_fld->name]['hidden'] = true;
								
							break;
							
							case 'button':
								
								$_return['fields'][$_fld->name]['formcode'] =
									'<button onclick="Joomla.submitbutton(\'apply\');" class="btn btn-small button-apply btn-success">
							<span class="icon-apply icon-white" aria-hidden="true"></span>
							' . $_fld->title . '</button>';
							
							break;
							
							
							case 'emailaddress':
								if( \JFactory::getApplication()->isSite()) {
									if(empty($_return['fields'][$_fld->name]['value']) && $_fld->required) {
										
										$_return['fields'][$_fld->name]['value'] = \JFactory::getUser()->email;
										
									}
									
									$formcode = '<input type="email" id="'
										. $_prefix.$_fld->name . '_field" name="' . $_prefix.$_fld->name.'" size="' . $_fld->size
										
										. '" value="' . $_return['fields'][$_fld->name]['value'] .'" '
										
										
										. ($_fld->required ? ' class="required validate-email"' : '');
										
										if(isset ($_fld->disabled ) ){
											$formcode  .=   ( $_fld->disabled ? '  disabled="disabled"' : '') ;
										}
										
										
										
										$formcode  .= ($_fld->maxlength ? ' maxlength="' . $_fld->maxlength . '"' : '35')
										. $readonly . '  /> ';
									$_return['fields'][$_fld->name]['formcode'] = $formcode ;
									$_return['fields'][$_fld->name]['formcode'] = $this->_render_addWrapp_input($_return['fields'][$_fld->name] ,  $wraperStyle) ;
									break;
								}
							
							case 'text':
							case 'webaddress':
								
								if(empty(  $_fld->value  ) && $_fld->required) {
									$_fld->value = \JFactory::getUser()->name;
									
								}
								
								$formcode  = '<input type="text" id="'
									. $_prefix.$_fld->name . '_field" name="' . $_prefix.$_fld->name
									.'" size="' . $_fld->size
									. '" value="' . $_fld->value .'" ' ;
								
								if(isset ($_fld->disabled ) ){
									$formcode  .=   ( $_fld->disabled ? '  disabled="disabled"' : '') ;
								}
								
									
								$formcode  .= ($_fld->required ? ' class="required"' : '')
									. ($_fld->maxlength ? ' maxlength="' . $_fld->maxlength . '"' : '')
									. $readonly . ' /> ';
								$_return['fields'][$_fld->name]['formcode'] = $formcode ;
								$_return['fields'][$_fld->name]['formcode'] = $this->_render_addWrapp_input($_return['fields'][$_fld->name], $wraperStyle) ;
							
							break;
							
							case 'textarea':
								
								//echo'<pre>';print_r( $_fld );echo'</pre>'.__FILE__.' '.__LINE__;
								
								$_return['fields'][$_fld->name]['name'] = $_prefix.$_fld->name ;
								
								$formcode  = '<textarea id="'
									. $_prefix.$_fld->name . '_field" name="' . $_prefix.$_fld->name
									
									.'" rows="' . $_fld->rows
									.'" cols="' . ($_fld->cols ? $_fld->cols : 32) . '" '
									
									
									. ($_fld->required ? ' class="required"' : '')
									. ($_fld->maxlength ? ' maxlength="' . $_fld->maxlength . '"' : '').'" '
									. 'data-role="autosized-textarea" '
									. 'data-min-rows="'.($_fld->rows - 1).'" '
									. '>'
									. $_return['fields'][$_fld->name]['value'] . ''
									. '</textarea>'
								;
								$_return['fields'][$_fld->name]['formcode'] = $formcode ;
								$_return['fields'][$_fld->name]['formcode'] = $this->_render_addWrapp_textarea($_return['fields'][$_fld->name] , $wraperStyle) ;
							
							break ;
							
							
							
						}#END switch
				}#END switch
				
			}#END FOREACH
		}#END IF
		
		
		return $_return ;
	
	}#END FN
	
	
	/**
	 * обвертка кода для поля textarea
	 *
	 * @since version
	 *
	 */
	private function _render_addWrapp_textarea ( $formCode, $style = '' )
	{
		
		// echo'<pre>';print_r(  $formCode  );echo'</pre>'.__FILE__.' '.__LINE__;
		
		switch ( $style )
		{
			case 'mdl':
				$_return = '<div class="control-group mdl-textfield mdl-textarea">';
				$_return .= '<label class="control-label" for="' . $formCode[ 'name' ] . '_field">' . $formCode[ 'title' ] . '</label>';
				$_return .= $formCode[ 'formcode' ];
				$_return .= '<p class="help-block help-block-error"></p>';
				$_return .= '</div>';
				break;
			
			default:
				return $formCode[ 'formcode' ];
		}
		
		return $_return;
	}#END FN
	
	/**
	 * Создать обвертку для поля INPUT
	 *
	 * @param $formCode
	 *
	 * @return string
	 *
	 * @since version
	 */
	private function _render_addWrapp_input( $formCode , $style = '' ){
		
		
		switch( $style ) {
			case 'mdl':
				$_return = '<div class="control-group mdl-textfield">' ;
				$_return .= '<label class="control-label" for="' . $formCode[ 'name' ] . '_field">' . $formCode[ 'title' ] . '</label>';
				$_return .=  $formCode['formcode'] ;
				$_return .= '<p class="help-block help-block-error"></p>';
				$_return .= '</div>' ;
			break;
			
			default:
				$_return =  $formCode['formcode'];
				
		}#END switch
		

		
		
		return $_return ;
	}
	

}#END CLASS
























