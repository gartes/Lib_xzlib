<?php

namespace xzlib\app\components\com_users;

use JFactory;
use JText;
use JUser;
use JUserHelper;

/**
 * @package     Joomla.Libraries
 * @subpackage
 *
 * @since       version 2
 * @author      Gartes.
 * @copyright   Copyright (C) 2014 - 2018 All rights reserved.
 * @license     A "Slug" license name e.g. GPL2
 */

defined('_JEXEC') or die;

class com_users extends \xzlib\xzlib_app
{
	
	/**
	 * @var
	 * @since version
	 */
	public static $instance;
	/**
	 * @var   Object   JFactory::getApplication
	 * @since version  2
	 */
	var $app ;

	/**
	 * Путь к папке с языковыми файлами
	 * @var   string
	 * @since version 2
	 */
	const LANG_PACH = '/xzlib/app/components/com_users' ;
	
	/**
	 * файл локали
	 * @var   string
	 * @since version 2
	 */
	const LANG_FILE = 'lib_xzlib_app_components_com_users' ;
	
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
			
			$language =  \JFactory::getLanguage();
			$language->load( self::LANG_FILE, JPATH_LIBRARIES . self::LANG_PACH , $language->getTag(), true);
			
		}
		
		return self::$instance;
	}#END FN
	
	/**
	 * логиним пользователя
	 *
	 * @param array $credentials ['username' , 'password']
	 *
	 *
	 * @since version 2
	 * @return bool|\JUser
	 */
	public function loginUser ( $credentials , $redirect = false  )
	{
		$app =  $this->app;
		
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('id, password')
			->from('#__users')
			->where('username=' . $db->quote($credentials[ 'username' ]));
		
		$db->setQuery($query);
		$result = $db->loadObject();
		
		if( $result )
		{
			$match = JUserHelper::verifyPassword($credentials[ 'password' ], $result->password, $result->id);
			if( $match === true )
			{
				// Bring this in line with the rest of the system
				$user = JUser::getInstance($result->id);
				
				
				$error = $app->login($credentials);
				$logged_user = JFactory::getUser();
				
				// var_dump($logged_user );
				
				if( $redirect ){
					#redirect logged in user
					$app->redirect('index.php');
				}
				return $logged_user;
				
				// echo 'Joomla! Authentication was successful!';
			}
			else
			{
				
				$app->enqueueMessage( JText::_('LIB_XZLIB_APP_COMPONENTS_COM_USERS_ERR_PASS_OR_LOGIN') , 'Message');
				return false ;
				// Invalid password
				// Prmitive error handling
				// die('Invalid password');
			}
		}
		else
		{
			
			$app->enqueueMessage( JText::_('LIB_XZLIB_APP_COMPONENTS_COM_USERS_ERR_PASS_OR_LOGIN') , 'Message');
			return false ;
			// Invalid user
			// Prmitive error handling
			// die('Cound not find user in the database');
		}
		
		
	}#END FN
	
	
}#END CLASS