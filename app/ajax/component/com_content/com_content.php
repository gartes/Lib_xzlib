<?php 
namespace xzlib\app\ajax\component\com_content;
use Joomla\Registry\Registry as Registry;
 
class com_content extends \xzlib\app\ajax\apl {
    public $registry; 
    public static $instance ;
    
   /**
     * 
     * 
     */ 
    public static function instance( $options =  array () ) 
    {
        if (self::$instance === null) 
        {
			self::$instance = new self( $options );
        }
        return self::$instance;
	}#END FN 
    
    /**
     * 
     * 
     */ 
    public function __construct	($options = null)
    {
        parent::__construct($options );
        self::$instance = $this;
    }#END FN
    
    /**
     * 
     * 
     */ 
    public function getArticle (){
        $returnArr = array();
        $app = \JFactory::getApplication() ; 
        $jinput = $app->input;
        
        $Articleid = $jinput->get( 'Article' , false , 'int' );
         
        $article =  \JTable::getInstance("content");
        $article->load( $Articleid );
            
        try
        {
            if(!$article->get("title"))
            {
               throw new \Exception( \JText::_('LIB_XZLIB_GET_COM_CONTENT_ERR_NO_ARTICLE') ); 
            }//
        }catch (Exception $e){
            $app->enqueueMessage( $e->getMessage() , 'error');
        }
            
        $returnArr['title'] = $article->get("title") ; 
        $returnArr['content']  = $article->get("introtext") ;
         
        return $returnArr ; 
     
    }#END FN
    
    
    
    
    
    
    
}#END CLASS