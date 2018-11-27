<?php 
namespace xzlib\app\components\com_virtuemart\models;
use xzlib\app\components\com_virtuemart\helprers as Helprers;
use \stdClass ;

defined('_JEXEC') or die;  

class user {
    
    public $JUser_keys = array (
            'email'     => 'string',
            'username'  => 'string',
            'password'  => 'string',
            'password1' => 'string',
            'name'      => 'string',
            'phone'     => 'text',
            'phone_1'     => 'text',
            'agreed'    => 'bool' , 
            'last_name' => 'text',
    ); 
    static private $passwordLength = 6 ;
    /**
      * Регистрация пользователя Joomla
      * 
      * 
      */ 
    public function joomlaRegUser ()
    {
        
        // Load the language file for com_users
		$lang = \JFactory::getLanguage();
		$lang->load('com_users', JPATH_SITE);
        
        $jinput = \JFactory::getApplication()->input;
        
        $formDataArr = $jinput->getArray( $this->JUser_keys );
        
        // Коректировка полей для регистрации пользователя.
        $formDataArr = Helprers\helperUser::corectUserFields( $formDataArr );
        
        // Проверить на пользователя Joomla
        $uid = Helprers\helperUser::checkUserJoomla( $formDataArr );
        
        // Если пользователь существует 
        if( $uid ){
            $user = \JFactory::getUser($uid); 
            return $user ; 
        }#END IF
        
        if (!class_exists('UsersModelRegistration'))
            require(JPATH_SITE . DS . 'components' . DS . 'com_users' . DS . 'models' . DS . 'registration.php'); #END IF 
		$model = new \UsersModelRegistration(); 
        
        $uid = $model->register( $formDataArr );
        
        if ($uid){
            $user = \JFactory::getUser($uid) ; 
            $user->password_clear =  $formDataArr['password'] ; 
            return $user   ;
        } #END IF     
    
    }#END FN 
    
    
    /**
     *  Регистрация пользователя в virtuemart
     * 
     * 
     *  @param $jUser - Joomla User Obj
     */ 
    public function virtuemartRegUser ($jUser){
        
        $jinput = \JFactory::getApplication()->input;
        $db = \JFactory::getDbo(); 
        
        // Проверить наличие пользователя
        $vmUid = Helprers\helperUser::checkUserVirtuemart ( $jUser  ) ; 
        if( $vmUid ){
            return array ( 
                'userType' => 'old' ,
                'uid' => $vmUid ,  
            ) ;  
        }#END IF
        
        $this->_requiredFields ();
        
        $formDataArr = $jinput->getArray(
            $this->JUser_keys
        );
        
        foreach ($formDataArr as $key => $val )
        {
            if( empty ( $val ) ){
                $formDataArr[$key] = '*no value*' ; 
            }#END IF
        }#END FOREACH  
        
        $userObject = new \stdClass();
        $userObject->virtuemart_user_id = $jUser ->id;
        $userObject->address_type = 'BT';
        $userObject->name = $jUser->get('name');
        
        $userObject->last_name = $formDataArr['last_name'];
        
        $userObject->phone_1 = $formDataArr['phone_1']; 
        $userObject->city = ( isset($formDataArr['city'])?$formDataArr['city']:'' );
        
        
        $userObject->agreed =  $jinput->get( 'agreed' , 0 , 'BOOL' )  ;
        
        $userObject->created_on = date('Y-m-d h:i:s');
        $userObject->created_by = $jUser->id;
        $userObject->modified_on = date('Y-m-d h:i:s');
        $userObject->modified_by = $jUser->id;
        $userObject->locked_on = '0000-00-00 00:00:00';
        $userObject->locked_by = '0'; 
        
        
        
         
         
        if( $db->insertObject('#__virtuemart_userinfos', $userObject ) )
        {
            $userGroupObject = new stdClass();
            $userGroupObject->virtuemart_user_id = $jUser->id;
			$userGroupObject->virtuemart_shoppergroup_id =  2 ;
			$db->insertObject('#__virtuemart_vmuser_shoppergroups', $userGroupObject);	
				
				
			$username = $jUser->get('username');
				
			$vmuser = new stdClass();
			$vmuser->virtuemart_user_id = $jUser->id;
			$vmuser->virtuemart_vendor_id = 1;
            $vmuser->user_is_vendor = 0;
			$vmuser->customer_number = strtoupper(substr( md5( $jUser->get('name') ) , 0 , 2 )).substr(md5( $username ) , 0 , 9 );
			$vmuser->virtuemart_paymentmethod_id = 0;
			$vmuser->virtuemart_shipmentmethod_id = 0;
			
            $vmuser->agreed =  $jinput->get( 'agreed' , 0 , 'BOOL' )    ;
            
			$vmuser->created_on = date('Y-m-d h:i:s');
			$vmuser->created_by = $jUser->id;
			$vmuser->modified_on = date('Y-m-d h:i:s');
			$vmuser->modified_by = $jUser->id;
			$vmuser->locked_on = '0000-00-00 00:00:00';
			$vmuser->locked_by = 0;
            
            
            
			if($db->insertObject('#__virtuemart_vmusers', $vmuser))
            {
				return array ( 
                    'userType' => 'new' ,
                    'uid' => $jUser->id ,  
                ) ;  	
            }
            else
            {
					$error = 'Error function createUser ' . __FILE__ ;
					$app = JFactory::getApplication();
					$app->enqueueMessage($error, 'error');	
					echo new JResponseJson(false , 'Главное сообщение ответа');
					 return false  ;
					 
				}// end if
        } 
        else
        {
		  $error = 'Error function createUser ' . __FILE__ ;
		  $app = JFactory::getApplication();
		  $app->enqueueMessage($error, 'error');
		  echo new JResponseJson(false , 'Главное сообщение ответа');
		  return false  ;
				 
		}#END IF   
    }#END FN 
    
    /**
     * Получить список полей для регистрации
     * 
     * 
     * 
     */ 
    private function _requiredFields (){
        
        if (!class_exists('VirtueMartModelUserfields')) 
            require(VMPATH_ADMIN . DS . 'models' . DS . 'userfields.php');
		
        $userFieldsModel = \VmModel::getModel('userfields');
        
        $fieldtypes = array ( 'BT' => 'account' , 'cartfields' => 'cart' , 'ST' => 'shipment' );
        
        $this->JUser_keys = array ();
        
        foreach( $fieldtypes as $cartFieldType => $fieldtype ){ 
             
            // Получить список обязательных полей для текущего типа вида 
            $neededFields = $userFieldsModel->getUserFields(
                $fieldtype 
                , array('required' => true, 'delimiters' => true, 'captcha' => true, 'system' => false  )
                , array('delimiter_userinfo', 'name','username', 'password', 'password2', 'address_type_name', 'address_type', 'user_is_vendor', 'agreed')
            ); 
            
           if ( count ( $neededFields) == 0 ) continue ; 
            
            foreach( $neededFields as $neededField ){
                switch ($neededField->name){
                    case 'email' :
                        $neededField->type = 'text' ;
                    break ; 
                }#END SWITCH
                $this -> JUser_keys[$neededField->name] = $neededField->type ; 
            }#END FOREACH
         }#END FOREACH
     }#END FN
    
    /**
     * получить длину автоматически содоваемоего пароля 
     * 
     * 
     */ 
    public static function getPasswordLength(){
        return self::$passwordLength ; 
    }#END FN

}#END CLASS