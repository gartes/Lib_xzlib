<?php defined('_JEXEC') or die;    

$coupon_code = $displayData['coupon_code']; // => CU-ZMRL6C
$coupon_value = $displayData['coupon_value']; //  => 10
$coupon_start_date = $displayData['coupon_start_date']; //  => 2018-08-24 12:51:51
$coupon_expiry_date = $displayData['coupon_expiry_date']; //  => 2018-11-22 12:51:51
$coupon_type = $displayData['coupon_type']; //  => gift
$percent_or_total = $displayData['percent_or_total']; //  => percent
$coupon_value_valid = $displayData['coupon_value_valid']; //  => 100
$virtuemart_coupon_id = $displayData['virtuemart_coupon_id']; //  => 89


$HTML_VALUE_TEXT = 'LIB_XZLIB_COM_VIRTUEMART_AUTO_COUPON_HTML_VALUE_'.strtoupper ($percent_or_total) ; 
 


?>
<div class="auto_coupon modal">
    <div class="head"><h4><?= JText::_('LIB_XZLIB_COM_VIRTUEMART_AUTO_COUPON_HTML_HEAD') ?></h4></div>
    <div class="body_auto_coupon">
        <div class="inp_auto_coupon">
            <input value="<?= $coupon_code ?>" />
        </div><!--/.inp_auto_coupon -->
        <div class="inf_auto_coupon">
            <div class="coupon_value"><?= JText::sprintf( $HTML_VALUE_TEXT , $coupon_value   ) ?></div>
            <?php 
            if( $coupon_value_valid > 0 )
            {?>
                <div class="coupon_value_valid">
                    <?= JText::sprintf(  'LIB_XZLIB_COM_VIRTUEMART_AUTO_COUPON_HTML_VALUE_VALID' , $coupon_value_valid   ) ?>
                </div>    
            <?php 
            }
            ?>
            <div class="coupon_expiry_date"><?= JText::sprintf(  'LIB_XZLIB_COM_VIRTUEMART_AUTO_COUPON_HTML_EXPIRY_DATE' , $coupon_expiry_date   ) ?></div>
        </div>
        
    </div><!-- /.body_auto_coupon -->
</div>