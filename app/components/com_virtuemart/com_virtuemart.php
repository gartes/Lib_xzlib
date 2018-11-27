<?php

namespace xzlib\app\components\com_virtuemart;


use JConfig;
use JDispatcher;
use JLayoutFile;
use JLog;
use JPluginHelper;
use JResponseJson;
use JRoute;
use JSession;
use JText;
use VmModel;
use xzlib\app\components\com_virtuemart\helprers\helperUser;
use xzlib\xzlib_app;

defined('_JEXEC') or die;

/**
 * @package     xzlib\app\components\com_virtuemart
 *
 * @since       version 2
 */
class com_virtuemart extends xzlib_app
{
	
	
	/**
	 * cтек элементов формы
	 *
	 * @var array
	 * @since version
	 */
	private $_formElement = array();
	
	/**
	 * cтек полей формы
	 *
	 * @var array
	 * @since version
	 */
	private $_formField = array();
	
	
	/**
	 * Стек полей заполняемых пользователем
	 *
	 * @var array
	 * @since version
	 */
	private $_formFieldUser = array();

	
	public static $instance;
	var $app ;
	/**
	 * Instance method
	 *
	 * @param   array $options
	 *
	 * @return  com_virtuemart
	 * @since   version 2
	 * @throws \Exception
	 */
	public static function instance ( $options = array() )
	{
		if( self::$instance === NULL )
		{
			self::$instance = new self($options);
		}
		
		return self::$instance;
	}#END FN
	
	
	/**
	 * Список товаров в корзине
	 * При обновлении модуля корзины...
	 *
	 * разробатывася для protect-sc.ru
	 *
	 * @return array
	 *
	 * @since version
	 */
	public function getCartHtml(){
		
		if (!class_exists('VirtueMartCart'))
			require(VMPATH_SITE . DS . 'helpers' . DS . 'cart.php');
		
		$cart = \VirtueMartCart::getCart();
		
		$cart->updateProductCart();
		$data = $cart->prepareAjaxData();
		
		$template = $this->app->getTemplate();
		$layout = new JLayoutFile('topAccount.cartProductsContainer', $basePath);
		$layout->addIncludePath(JPATH_THEMES . '/' . $template . '/html/lib_xzlib/modules/blocks/');
		
		// echo'<pre>';print_r( $data->billTotal_discounted_net );echo'</pre>'.__FILE__.' '.__LINE__;
		
		$html = [
			'vmcontainer' => $layout->render(['data'=>$data]), // $displayData
			'cartProductsContainer_footer' =>  $layout->setLayoutId('topAccount.cartProductsContainer_footer'  )->render(['data'=>$data]),
			'total_products' => $data->totalProduct ,
			'billTotal_net' => $data->billTotal_net ,
			'billTotal'=>$data->billTotal_discounted_net,
		];
		
		return $html ;
	}#END FN
	
	
	/**
	 * Проверка пользователя по емайлу и телефону
	 *
	 * @param $data
	 *
	 * @return bool
	 *
	 * @since version
	 * @throws \Exception
	 */
	public function checkVmUser($data){
		$_RETUN = false ;
		
		// Проверка Телефона
		$res = helperUser::checkUserByPhone( $data['phone_1'] );
		if( $res && !$_RETUN ){
			$this->app->enqueueMessage( JText::_('LIB_XZLIB_COM_VIRTUEMART_WARN_PHONE') , 'Message');
			$_RETUN = true ;
		}#END IF
		
		// Проверка пользователля в базе Joomla
		$formDataArr['email'] = $data['email'] ;
		$formDataArr['username'] = $data['email'] ;
		$res = helperUser::checkUserJoomla( $formDataArr );
		if( $res && !$_RETUN ){
			$this->app->enqueueMessage( JText::_('LIB_XZLIB_COM_VIRTUEMART_WARN_EMAIL') , 'Message');
			$_RETUN = true ;
		}#END IF
		
		return $_RETUN ;
	}#END FN
	
	
	/**
	 * Возвращает поля формы для регистрации адрес TYPE BT
	 * с FORM CODE
	 *
	 * @return mixed
	 *
	 * @since version 2
	 */
	public function get_vmFirldsUser ()
	{
		$this->_formFieldUser = helperUser::get_vmUserFields();
		return $this->_formFieldUser ;
	}#END FN

	/**
	 * Обработка формы "Нашли дешевле"
	 *
	 * @author    Gartes
	 * @since     3.8
	 * @copyright 25.10.18
	 *
	 */
	public function sendDownPrice(){
		JSession::checkToken() or die( 'Invalid Token' );

		$recaptcha_result = $this->app->input->get( 'g-recaptcha_result' , false , 'ARRAY') ;
		if ($recaptcha_result['success'] !=  1 )
		{
			echo new \JResponseJson( false , \JText::_('ERR_G_RECAPHCA'), true );
			jexit();
		}


		$jinput = $this->app->input ;
		$template = $this->app->getTemplate();

		
		$dVar = new JConfig();




		$post = $jinput->getArray(
			array(
				'downPrice' => array(
					'last_name' => 'STRING',
					'email' => 'RAW',
					'url' => 'RAW',
					'phone_1' => 'RAW',
					'text_comment' => 'RAW',
					'virtuemart_product_id' => 'INT',
				)
			)
		);



		
		$productModel = VmModel::getModel ('product');
		$product = $productModel->getProduct ($post['downPrice']['virtuemart_product_id']);
		
		
		$url = JRoute::_ (
			'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id
			. '&virtuemart_category_id=' .$product->virtuemart_category_id );
		
		$post['productLink'] = '<a href="'.$url.'">'.$product->product_name.'</a>';
		
		
		
		$post['from'] = $dVar->mailfrom ;
		$post['to'] = $dVar->mailfrom ;
		// $post['to'] = 'sad.net79@gmail.com';
		$post['subject'] = 'Нашли этот товар дешевле';
		
		$layout = new JLayoutFile('downPrice.downPrice_maile', null , array('debug' => false) );
		$layout->addIncludePath(JPATH_THEMES . '/' . $template . '/html/lib_xzlib/modules/blocks/');
		$post['body'] = $layout->render($post);// $displayData

		# отправить письмо с данными
		$this->sendMaile ( $post );


		$postStr = json_encode( $_POST ) ;
		// echo'<pre>';print_r( $postStr );echo'</pre>'.__FILE__.' '.__LINE__;


		JLog::add( $postStr , JLog::NOTICE, 'my-debug-category');

		$this->app->enqueueMessage( JText::_('LIB_XZLIB_COM_VIRTUEMART_SEND_DOWN_OK') , 'Message');
		return ;

		/*echo'<pre>';print_r( $post );echo'</pre>'.__FILE__.' '.__LINE__;
		echo'<pre>';print_r( $resultsArr );echo'</pre>'.__FILE__.' '.__LINE__;*/
		
	}#END FN
	
	
	/**
	 * Добавление коментария к товару...
	 *
	 * @since version
	 */
	public function addComment (){
		JSession::checkToken() or die( 'Invalid Token' );
		$user = \JFactory::getUser();
		
		
		$jinput = $this->app->input ;
		$_prefix = $jinput->get('prefixField' , '' , 'WORD');
		
		
		
		$jinput->set('option' , 'com_jcomments');
		$jinput->set('jtxf' , 'JCommentsAddComment');
		

		
		
		$virtuemart_product_id = $jinput->get('virtuemart_product_id' , false , 'INT') ;
		if($virtuemart_product_id ){
			$object_group = 'com_virtuemart' ;
			$jinput->set('object_id' , $virtuemart_product_id  ) ;
			$jinput->set('object_group' , $object_group  ) ;
			
		}




		$content_id = $jinput->get('content_id' , false , 'INT') ;
		if($content_id){
			$object_group = 'com_content' ;
			$jinput->set('object_id' , $content_id  ) ;
			$jinput->set('object_group' , $object_group  ) ;
			$virtuemart_product_id = $content_id ;
		}
		
		$name = $jinput->get( $_prefix.'last_name' , '' , 'RAW');
		$jinput->set('name' , $name );
		
		
		
		
		$jinput->set('captcha_refid' , 'XXXX'  ) ;
		
		
		$mark = $jinput->get( 'mark' , false , 'INT');
		
		$db = \JFactory::getDBO();
		
		$user_rating = \JRequest::getInt('mark');
		//$cid = $_POST ['object_id'];
		
		
		
		if ( $user_rating >= 1 && $user_rating <= 5) {
			$currip = $_SERVER['REMOTE_ADDR'];
			$query = "SELECT * FROM #__content_vrvote WHERE content_id=".$virtuemart_product_id;
			$db->setQuery( $query );
			$votesdb = $db->loadObject();
			
			
			
			
			if ( !$votesdb ) {
				$query = "INSERT INTO #__content_vrvote  (content_id , extra_id , lastip , rating_count , rating_sum , object_group)"
					. "\n VALUES (".$virtuemart_product_id.",".$virtuemart_product_id.",".$db->Quote($currip).",1,".$mark .',"' . $object_group . '")';
				
				
				
				$db->setQuery( $query );
				$db->query() or die( $db->getErrorMsg() );
				
				
			} else {
				
				
				$query = "UPDATE #__content_vrvote"
					. "\n SET rating_count = rating_count + 1, rating_sum = rating_sum + " .  $mark . ", lastip = " . $db->Quote( $currip )
					. "\n WHERE content_id=".$virtuemart_product_id;
				$db->setQuery( $query );
				$db->query() or die( $db->getErrorMsg() );
				
				
				
				
			}
		}
		
		
		


		/*

		$user_rating = $jinput->get('mark' , 0 , 'INT' );
		if( $comment && $user_rating >= 1 && $user_rating <= 5 ){
			$comment['mark'] = $user_rating ;
		}

		if($user->id > 0 && $user->name !=  $name   ) {
			$comment['alias_name'] = $name ;
		}
		// alias
		*/



		// $jinput->set('comment' , $comment );
		// $_POST ['comment'] = $comment ;

		/*
		$email = $jinput->get('comments_email' , '' , 'RAW');
		$jinput->set('email' , $email );
		$_POST ['email'] = $email ;






		if($user->id > 0 ){
			$_POST ['userid'] = $user->id ;
		}#END IF



		 */
		
		$coreFile = JPATH_ROOT . '/components/com_jcomments/jcomments.php';
		include_once($coreFile);
		
		$JComments = new \JComments();
		
		echo'<pre>';print_r( $JComments );echo'</pre>'.__FILE__.' '.__LINE__;
		
		
		/*
		define('JCOMMENTS_SITE', JPATH_ROOT . '/components/com_jcomments');
		require_once(JCOMMENTS_SITE . '/jcomments.ajax.php');*/
		
		
		// $comment = \JTable::getInstance('Comment', 'JCommentsTable');
		
		echo'<pre>';print_r( $comment );echo'</pre>'.__FILE__.' '.__LINE__;
		echo'<pre>';print_r( $jinput );echo'</pre>'.__FILE__.' '.__LINE__;
	}#END FN
	
	
	
	/**
	 * Возвращает поля формы для регистрации адрес TYPE BT
	 * с FORM CODE
	 *
	 * @return mixed
	 *
	 * @since version 2
	 */
	public function get_vmFirldsRegUser ()
	{
		$this->_formField[] = helperUser::get_vmFirldsRegUser();
		
		return self::$instance;
	}#END FN
	
	
	
	
	/**
	 * html - рендер полей формы регистрации
	 *
	 * @return string
	 *
	 * @since version
	 */
	public function renderField ()
	{
		$res = $this->_renderField();
		
		return $res;
	}#END FN
	
	/**
	 * Рендер полей формы для регистрации
	 *
	 * @return string
	 *
	 * @since version
	 */
	private function _renderField ()
	{
		
		$min_rows = 5 ;
		
		$returnHtml = '';
		foreach ( $this->_formField as $formField )
		{
			
			
			foreach ( $formField[ 'fields' ] as $field )
			{
				$returnHtml .= '<div class="control-group mdl-textfield">';
				
				switch ($field['type']){
					
					case 'textarea' :
						$returnHtml .= '<label class="control-label" for="addopinionform-plus">Преимущества</label>';
						$returnHtml .= '<textarea ';
							$returnHtml .= 'class="'.($field['required']?'required':'').'" ';
							$returnHtml .= 'name="' . $field[ 'name' ] . '" ';
							$returnHtml .= 'value="'.(!empty($field['value'])?$field['value']:'').'" ';
							$returnHtml .= 'data-role="autosized-textarea" ';
							$returnHtml .= 'data-min-rows="'.$min_rows.'" ';
							$returnHtml .= '>';
						$returnHtml .= '</textarea>';
					break;
					
					default:
						$returnHtml .= '<input ';
						$returnHtml .= 'placeholder="' . $field[ 'title' ] . '" ';
						
						$returnHtml .= 'class="'.($field['required']?'required':'').'" ';
						
						$returnHtml .= 'name="' . $field[ 'name' ] . '" ';
						$returnHtml .= 'value="'.(!empty($field['value'])?$field['value']:'').'" ';
						
						if(!$field['hidden']){
							$returnHtml .= 'type="text" ';
						}else{
							$returnHtml .= 'type="hidden" ';
						}
						
						$returnHtml .= ' />';
				}
				 // $returnHtml .= str_replace("<input ", '<input placeholder="' . $field[ 'title' ] . '" ', $field[ 'formcode' ]);
				$returnHtml .= '</div>';
			}#END FOREACH
			
		}#END FOREACH
		
		return $returnHtml;
	}#END FN
	
	
	/**
	 * Отправка письма пользователю
	 *
	 * @param $jmailDat
	 *
	 *
	 * @since version
	 */
	public function sendMaile ( $jmailDat )
	{

		$bcc = 'sad.net79@gmail.com';

		# создаем объект JMail класса
		$mailer = \JFactory::getMailer();
		
		# присваиваем отправителя
		$mailer->setSender($jmailDat[ 'from' ]);

		# определяем получателя, тему и тело письма
		$mailer->addRecipient($jmailDat[ 'to' ]);
		$mailer->addBCC ($bcc);
		$mailer->setSubject($jmailDat[ 'subject' ]);
		$mailer->setBody($jmailDat[ 'body' ]);
		
		# если хотите отправить письмо как HTML
		$mailer->isHTML(true);
		
		# отправляем письмо
		$mailer->send();
		
	}#END FN
	
	
	
}#END CLASS







































