<?php 
namespace xzlib\app\document\elements\off_canvas ;

use \xzlib\app\document\document as xzlibDoc;

defined('_JEXEC') or die;


class off_canvas extends xzlibDoc {
    public $registry; 
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
       //  parent::__construct($options );
        self::$instance = $this;
    }#END FN
    public function render ($formHtml){
        
        $data = array('html' => $formHtml);
        $layout =  new \JLayoutFile('right' ,  JPATH_LIBRARIES .'/xzlib/app/document/elements/off_canvas/layouts' );
        $html =  $layout->render($data); // $displayData
        
        
        // Подключить js библиотеки
        $xzlibDocument = new xzlibDoc();
        $option = array( 'defer' => true ,);
        
        // Основаня Xzlib.Helper{}
        
        
        
         
        $option = array( 'defer' => true ,);
        $src = 'libraries/xzlib/app/document/elements/off_canvas/assets/js/c-offcanvas.js';
        $this->addScript( $src , $option ); 
        
       
        
        
        return $html;
    }#END FN
}