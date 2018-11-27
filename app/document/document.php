<?php 
namespace xzlib\app\document;
use JFactory;
use Joomla\Registry\Registry as Registry;

use JURI;
use xzlib\app\document\elements\off_canvas ;
use xzlib\app\box2   ;


 defined('_JEXEC') or die;

class document extends \xzlib\xzlib_app
{
    
    public static  $_script_Xzlib = array() ; 
    private $_mediaVersion;
    // public $registry; 
    
	
	/**
	 * @param array $options
	 *
	 * @return document
	 *
	 * @since version
	 * @throws \Exception
	 */
	public static $instance ;
    public static function instance( $options =  array () ) 
    {
		if (self::$instance === null) 
        {
			self::$instance = new self( $options );
	  
			
        }
        return self::$instance;
	}#END FN 


    public function __construct	($options = null)
    {
        parent::__construct($options );
        self::$instance = $this;
		
         $doc = JFactory::getDocument();
		
	    if ( JVERSION <= 3.7 ){
		    $doc->addScript( JURI::root() . 'libraries/xzlib/app/document/assets/js/'.'xzlib.js', "text/javascript" ,false, false );
		   
	    }else{
		    $doc->addScript( JURI::root().'libraries/xzlib/app/document/assets/js/'.'xzlib.js' , ['version' => 'auto'] , [/*'async' => 'async' ,*/ 'defer'=>1] );
	    }#END IF
     
        
	}#END FN
	
	

	/**
	 * getElement - Получить элемент страницы
	 *
	 * @param       $element
	 * @param array $options
	 *
	 * @return mixed
	 *
	 * @since version
	 */
    public function getElement ($element,$options = array () )
    {
        $obj = 'xzlib\\app\\document\\elements\\'.$element.'\\'.$element ; 
        return  $obj::instance($options);
    }#END FN
	
	
	
	/**
	 * Получить Объект форм Form
	 *
	 * @param array $options
	 *
	 * @return mixed
	 *
	 * @since version
	 *
	 */
	public function getForm ($options = array () )
	{
		$obj = 'xzlib\\app\\document\\form'  ;
		return  $obj::instance($options);
	}#END FN
	
	
	
	/**
	 * Получить Объект форм Form
	 *
	 * @param array $options
	 *
	 * @return mixed
	 *
	 * @since version
	 *
	 */
    public function getForms ($options = array () )
    {
        $obj = 'xzlib\\app\\document\\elements\\forms\\forms\\forms'  ; 
        return  $obj::instance($options);
    }#END FN

	/**
	 * @param       $src
	 * @param array $option
	 *
	 * @return bool
	 *
	 * @since version
	 *
	 * Добвавить скрипт к загрузке
	 *
	 */
    public function addScript ( $src , $option = array()  )
    {
        $doc = JFactory::getDocument();
        $doc->getMediaVersion();
        $_defOption = array (
            'version' => $doc->getMediaVersion() , 
            'async' => false ,
            'defer' => false ,
            'type'  => 'text/javascript' ,
        );
        
        $option = array_merge ($_defOption, $option);
        if ( array_key_exists( $src , self::$_script_Xzlib )) 
        {
            return true ; 
        }#END IF
        self::$_script_Xzlib[$src]['options'] = $option ; 
    }#END FN
	
	/**
	 * Установить загрущики файлов js
	 *
	 * @since version
	 * @throws \Exception
	 *
	 *
	 *
	 */
	public function addBodyScripts ()
	{
		$doc  = JFactory::getDocument();
		$app  = JFactory::getApplication();
		$body = $app->getBody();
		
		if( count(self::$_script_Xzlib) )
		{
			$this->_mediaVersion = '?' . $doc->getMediaVersion();
		}#END IF
		
		// echo'<pre>';print_r( self::$_script_Xzlib );echo'</pre>'.__FILE__.' '.__LINE__;
		
		foreach ( self::$_script_Xzlib as $src => $attribs )
		{
			$body = str_replace('</body>', $this->_renderScript($src, $attribs[ 'options' ]) . "</body>", $body);
		}#END IF

		$this->_addJsRecapcha();
		
		
		
		// $body = str_replace('</body>',$script . "</body>", $body);


		$app->setBody($body);
	}#END FN


	/**
	 * Добавить JS Загрузка google.com - Recapcha
	 * @return string
	 * @author    Gartes
	 * @since     3.8
	 * @copyright 26.10.18
	 */
	private function _addJsRecapcha (){
		$xzlibApi = \xzlib::get( 'api', [] );
		$xzlibApi->getApi( 'grecapcha' )->loadScript();
	}#END FN
	
	
	/**
	 * @param bool $src
	 * @param      $attribs
	 *
	 * @return string
	 *
	 * @since version
	 *
	 * Создание елемента DOM
	 *
	 *
	 */
	private function _renderScript( $src = false, $attribs) {
		
        $doc = JFactory::getDocument();
        $defaultJsMimes = array('text/javascript', 'application/javascript', 'text/x-javascript', 'application/x-javascript');

        
        
        $mediaVersion = (isset($attribs['options']['version']) && $attribs['options']['version'] && strpos($src, '?') === false && ($this->_mediaVersion || $attribs['options']['version'] !== 'auto')) ? $this->_mediaVersion : '';
		
                
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $script = $dom->createElement('script');

            // src attribute
        if ($src)
        {
	
	        if(strpos($src , "http://") !== false){
		        $this->_addAttribute($dom, $script, 'src', Juri::base() . $src . $mediaVersion);
	        }else{
		        $this->_addAttribute($dom, $script, 'src',  $src );
	        }
	        
		       
        }#END IF
        
        // type attribute
		if (array_intersect(array_keys($attribs), array('type', 'mime')) && !$doc->isHtml5() && in_array((isset($attribs['type']) ? $attribs['type'] : $attribs['mime']), $defaultJsMimes))
		{
			$this->_addAttribute($dom, $script, 'type', isset($attribs['type'])?$attribs['type']:$attribs['mime']);
		}#END IF
  
        //echo'<pre>';print_r($attribs  );echo'</pre>'.__FILE__.' '.__LINE__;
        //$attribs['defer'] = true ;
  
        // defer attribute
		if (isset($attribs['defer']) && $attribs['defer'] === true)
		{
			$this->_addAttribute($dom, $script, 'defer');
		}#END IF
        
        
		// async attribute
		if (isset($attribs['async']) && $attribs['async'] === true)
		{
			$this->_addAttribute($dom, $script, 'asnyc');
		}#END IF
		
        // charset attribute
		if (isset($attribs['charset']))
		{
			$this->_addAttribute($dom, $script, 'charset', $attribs['charset']);
		}#END IF
        
        $dom->appendChild($script);
        
        
        if (isset($attribs['options']) && isset($attribs['options']['conditional']))
		{
			$tag = $dom->saveHTML();
			return implode("\n", array('<!--[if ' . $attribs['options']['conditional'] . ']>', $tag . '<![endif]-->', ''));
		}#END IF
        return $dom->saveHTML();
	}#END FN

	/**
	 * @param      $dom
	 * @param      $element
	 * @param      $name
	 * @param bool $value
	 *
	 *
	 * @since version
	 *
	 * Добавить атребъют к тегу
	 *
	 */
    private function _addAttribute($dom, &$element, $name, $value = false) {
		$attr = $dom->createAttribute($name);
		if ($value)
		{
			$attr->value = $value;
		}
		$element->appendChild($attr);
	}#END FN
    
    
    
     
}# END CLASS