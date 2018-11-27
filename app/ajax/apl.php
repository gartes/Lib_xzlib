<?php 
namespace xzlib\app\ajax; 
use Joomla\Registry\Registry as Registry;

defined('_JEXEC') or die;

class apl extends Registry {
    /**
     *  __construct ()
     * 
     */ 
    public function __construct	($options = null)
    {
        $this->_setDefault ();
        $opt = new Registry( $options ) ;
        $this->registry->merge( $opt );
        $this->_setDebug (); 
    }
    
    /**
     * Установить значения по умолчанию
     * 
     */ 
    protected function _setDefault  ()
    {
        $optionsDEF = array (
            'ver' => false   , 
            'debug'         => false    , 
            'comment'       =>  false   ,
            'cache'         =>  true    ,
            'cacheReload'   =>  false   ,
            'fancybox'=>array(
                'ver'=>'1.3.4',
                'template'=>false,
            ),
             
        );
        $this->registry   = new Registry( $optionsDEF );
    }#END FN
    
    /**
     * Установка DEBUG Xlib
     * 
     */ 
    protected function _setDebug ()
    {
       if( $this->registry->get('debug' , false)){
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL); 
        } 
    }#END FN
     
        
}
