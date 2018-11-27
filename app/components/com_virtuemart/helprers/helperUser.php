<?php

namespace xzlib\app\components\com_virtuemart\helprers;

use xzlib\app\components\com_virtuemart\models as Models;

defined('_JEXEC') or die;

class helperUser
{
	
	public $passwordLength = 6;
	
	/**
	 * Получить массив с опубликованными полями для пользователя
	 *
	 * @return Array - Масссив объектов полей.
	 *
	 * @since version
	 */
	public static function get_vmUserFields ()
	{
		if( !class_exists('VmConfig') )
			require( JPATH_ROOT . '/administrator/components/com_virtuemart/helpers/config.php' );
		\VmConfig::loadConfig();
		\vmLanguage::loadJLang('com_virtuemart');
		
		// vmJsApi::vmValidator();


		
		
		$userFieldsModel = \VmModel::getModel('userfields');
		
		
		$userFieldsCart = $userFieldsModel->getUserFields(
			'account',
			array( 'captcha' => true, 'delimiters' => true ),  // Ignore these types
			array( 'delimiter_userinfo', 'user_is_vendor', 'username', 'password', 'password2', 'agreed', 'address_type' ) // Skips
		);

		/*echo'<pre>';print_r( $userFieldsCart );echo'</pre>'.__FILE__.' '.__LINE__;
		die(__FILE__ .' Lines '. __LINE__ );*/
		return $userFieldsCart ;
		
	}#END FN
	
	
	
	/**
	 * Получить обьект с полями для регистрации пользователя VM
	 *
	 * @since version
	 */
	public static function get_vmFirldsRegUser ()
	{
		$userFieldsCart  = self::get_vmUserFields();
		
		if( !class_exists('VirtueMartCart') ) require( VMPATH_SITE . DS . 'helpers' . DS . 'cart.php' );
		$cart = \VirtueMartCart::getCart();
		
		$userFieldsModel = \VmModel::getModel('userfields');
		
		$userFieldsCart  = $userFieldsModel->getUserFieldsFilled($userFieldsCart, $cart->cartfields);
		
		return $userFieldsCart;
		
	}#END FN
	
	
	/**
	 * Проверка пользователля в базе Joomla
	 *
	 * @param  {Type} $formDataArr
	 *
	 * @return bool|mixed
	 * @throws \Exception
	 * @since  version 1
	 *
	 * @author Gartes
	 */
	public static function checkUserJoomla ( $formDataArr )
	{
		$app = \JFactory::getApplication();
		$db  = \JFactory::getDbo();
		try
		{
			$query = $db->getQuery(true);
			
			$query->select('id')
				->from($db->quoteName('#__users'))
				->where($db->quoteName('email') . ' = ' . $db->quote($formDataArr[ 'email' ]), 'OR')
				->where($db->quoteName('username') . ' = ' . $db->quote($formDataArr[ 'username' ]));
			//echo $query->dump();
			$db->setQuery($query, 0, 1);
			
			$id = $db->loadResult();
		}
		catch (\Exception $e)
		{
			
			$mes = \JText::sprintf('UKCPU_BD_ERROR_' . $e->getCode(), $e->getCode());
			echo new \JResponseJson(NULL, $mes, true);
			$app->close();
			
		}
		
		return $id = ( !empty($id) ) ? $id : false;
	}#END FN
	
	
	/**
	 * Проверить покупателя Virtuemart
	 *
	 * @param  object $jUser
	 *
	 * @return bool|mixed
	 *
	 * @since  version 1
	 *
	 * @author Gartes
	 */
	public static function checkUserVirtuemart ( $jUser )
	{
		
		$db    = \JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select('virtuemart_user_id')
			->from($db->quoteName('#__virtuemart_userinfos'))
			->where($db->quoteName('virtuemart_user_id') . ' = ' . $db->quote($jUser->id));
		$db->setQuery($query, 0, 1);
		$id = $db->loadResult();
		
		return $id = ( $id > 0 ) ? $id : false;
		
		
	}#END FN
	
	/**
	 * Поиск пользователя по номеру телефона в таблицах #_virtuemart_userinfos и #__users
	 *
	 * @param $phone
	 *
	 * @return bool|mixed   INT User ID - If user exists TRUE
	 *
	 * @since version
	 */
	public static function checkUserByPhone ( $phone )
	{
		$db    = \JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id')
			->from($db->quoteName('#__users'))
			->where($db->quoteName('username') . ' = ' . $db->quote($phone));
		$db->setQuery($query, 0, 1);
		if( $id = $db->loadResult() )
		{
			return $id;
		}
		$query->clear();
		$query->select('virtuemart_user_id')
			->from($db->quoteName('#__virtuemart_userinfos'))
			->where(
				array(
					$db->quoteName('phone_1') . ' = ' . $db->quote($phone),
					$db->quoteName('phone_2') . ' = ' . $db->quote($phone),
				), 'OR'
			);
		$db->setQuery($query, 0, 1);
		if( $id = $db->loadResult() )
		{
			return $id;
		}
		
		return false;
	}#END FN
	
	/**
	 * Коректировка недостающих полей для регистрации пользователя.
	 *
	 * @param $formDataArr
	 *
	 * @return mixed
	 *
	 * @since version 1
	 * @throws \Exception
	 */
	public static function corectUserFields ( $formDataArr )
	{
		
		$jinput = \JFactory::getApplication()->input;
		
		if( empty($formDataArr[ 'username' ]) )
		{
			if( !empty($formDataArr[ 'phone_1' ]) )
			{
				
				$formDataArr[ 'username' ] = preg_replace("/[^0-9]/", '', $formDataArr[ 'phone_1' ]);
			}
			else if( !empty($formDataArr[ 'phone' ]) )
			{
				$formDataArr[ 'username' ] = preg_replace("/[^0-9]/", '', $formDataArr[ 'phone' ]);
			}
			else if( !empty($formDataArr[ 'email' ]) )
			{
				$formDataArr[ 'username' ] = $formDataArr[ 'email' ];
			}
		}#END IF
		
		if( empty($formDataArr[ 'password' ]) )
		{
			$passwordLength = Models\user::getPasswordLength();
			// $2y$10$ViVVpO9Nvv0QtDNaBjsnKOuhlm06fHg38Cfs30goY3YUPgPejyLJO  === ABCDEF
			$formDataArr[ 'password' ] = /*'ABCDEF'; //*/
				\JUserHelper::genRandomPassword($passwordLength);
		}#END IF
		
		if( empty($formDataArr[ 'password1' ]) )
		{
			$formDataArr[ 'password1' ] = $formDataArr[ 'password' ];
			$jinput->set('password', $formDataArr[ 'password' ]);
			$jinput->set('password1', $formDataArr[ 'password1' ]);
		}#END IF
		
		if( empty($formDataArr[ 'name' ]) )
		{
			$formDataArr[ 'name' ] = $formDataArr[ 'last_name' ];
			$jinput->set('name', $formDataArr[ 'name' ]);
		}#END IF
		if( empty($formDataArr[ 'email' ]) )
		{
			$formDataArr[ 'email' ] = $formDataArr[ 'username' ] . '__@email.email';
			$jinput->set('email', $formDataArr[ 'email' ]);
		}#END IF
		
		if( empty($formDataArr[ 'email1' ]) )
		{
			$formDataArr[ 'email1' ] = $formDataArr[ 'email' ];
			$jinput->set('email1', $formDataArr[ 'email1' ]);
		}#END IF
		
		return $formDataArr;
		
	}#END FN
	
}#END CLASS