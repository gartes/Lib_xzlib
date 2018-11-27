<?php 
// namespace xzlib ;
	
	
	use xzlib\app ;
use xzlib\app\document ; 
use xzlib\app\ajax\component\com_content ; 
use xzlib\app\ajax\component\com_virtuemart ;  
     /*
     
     ini_set('display_errors', 1);
     ini_set('display_startup_errors', 1);
     error_reporting(E_ALL);
     
     */
    
defined('_JEXEC') or die;  
defined ('DS') or define('DS', DIRECTORY_SEPARATOR);
require_once JPATH_LIBRARIES . '/xzlib/vendor/autoload.php' ;


/**
 * @package     xzlib
 *
 * @since       version 3,7
 */
final class xzlib 
{


	/**
	 * xzlib constructor.
	 *
	 * @param null $options
	 *
	 * @since       version 3.7
	 */
    public function __construct	($options = null)
    {
      
    }#END FN

	/**
	 * @param       $element
	 * @param array $options
	 *
	 * @return mixed
	 *
	 * @since version 3.7
	 */
    static function get( $element  , $options = array() )
    {
	    
        self::loadLangLib();
        $obj = 'xzlib\\app\\'.$element.'\\'.$element; 
        return  $obj::instance($options);
    }#END FN
	
	/**
	 * @param       $component
	 * @param array $options
	 *
	 * @return mixed
	 *
	 * @since version
	 */
	static public function getComponent ( $component , $options = array() ){
		self::loadLangLib();
    	$obj = 'xzlib\\app\\components\\'.$component.'\\' . $component;
		return  $obj::instance($options);
    
    }#END FN
	
	/**
	 * Ajax - точка входа в XZLIB Библиотеку
	 *
	 * @since version 3.7
	 * @throws Exception
	 */
    static public function Ajax(){
        $app = JFactory::getApplication() ;
        $jinput = $app->input;
        self::loadLangLib();
        
        $options = array();
        $component = $jinput->get( 'component' , false , 'word' );
        if( !$component ){
           return ;  
        }
        $task = $jinput->get( 'task' , false , 'word' );

        try {

            $obj = 'xzlib\\app\\ajax\\component\\' . $component.'\\'.$component;
            $compObj = $obj::instance( $options );
            $data = $compObj->{$task}();

        } catch ( \Error $e ) {
/*
            $err = \Error::getMessage() ."\n";

            $err .= \Error::getFile() . " in Line: " . \Error::getLine() ;*/


           //  echo'<pre>';print_r(  $err );echo'</pre>'.__FILE__.' '.__LINE__;
            
            echo new \JResponseJson( $e );
            jExit();

        }
        echo new \JResponseJson( $data );
        jExit();
    }#END FN

	/**
	 * Загрзка локализации библиотеки
	 *
	 * @since version 3.7
	 */
    static public function loadLangLib(){

	    if (!class_exists( 'VmConfig' )) require(JPATH_ROOT .'/administrator/components/com_virtuemart/helpers/config.php');
	    VmConfig::loadConfig();
		
	    
	    
	    $app = \JFactory::getApplication() ;
	    
	    
	    $language =  JFactory::getLanguage();
        $language->load('lib_xzlib' , JPATH_LIBRARIES . '/xzlib' , $language->getTag(), true);


	    $menu = JFactory::getApplication()->getMenu();
	    $active = $menu->getActive();
        $doc = JFactory::getDocument();


	    $doc->addScriptOptions(
		    'XzLib', array(
			    'SiteUrl'   => JURI::root(),
			    'XzLibLang' => "&lang=" . VmConfig::$vmlangSef ,
			    'itemId'    => (!empty($active) ? $active->id : false),
			    'isAdmin' => $app->isAdmin() ,
		    )
	    );

    }#END FN
    
}#END CLASS





 

 

?>