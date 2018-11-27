<?php 
namespace xzlib\app\components\com_virtuemart\models;
use VirtueMartModelCoupon;

defined('_JEXEC') or die;  
 
if(!class_exists('VirtueMartModelCoupon'))
    require(VMPATH_ADMIN.DS.'models'.DS.'coupon.php');

class vmCoupon extends VirtueMartModelCoupon 
{
    
    /**
     *  
     * 
     */ 
    function __construct() 
    {
		parent::__construct();
    }#END FN
    
    /**
     * Создание  Купона.
     * 
     * 
     * 
     */ 
    function store(&$data) 
    {
        $table = $this->getTable('coupons');
               
		// Convert selected dates to MySQL format for storing.
		if ($data['coupon_start_date']) {
		    $startDate = \JFactory::getDate($data['coupon_start_date']);
		    $data['coupon_start_date'] = $startDate->toSQL();
		}
		if ($data['coupon_expiry_date']) {
		    $expireDate = \JFactory::getDate($data['coupon_expiry_date']);
		    $data['coupon_expiry_date'] = $expireDate->toSQL();
		}
        
        
        
		$table->bindChecknStore($data);
		$data['virtuemart_coupon_id'] = $table->virtuemart_coupon_id;

        return $table->virtuemart_coupon_id;
    }#END FN
}#END CLASS