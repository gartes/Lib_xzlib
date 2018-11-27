<?php 
$emailTo = $displayData['to'];
$coupon_expiry_date = $displayData['coupon_expiry_date'];
?>
<div class="auto_coupon">
    <div class="head">
        <h4>
            <?= \JText::sprintf('LIB_XZLIB_COM_VIRTUEMART_AUTO_COUPON_HTML_HEAD_SEND_MAILE' , $emailTo ) ?>
        </h4>
    </div>
    <div class="body_auto_coupon">
         
        <div class="inf_auto_coupon">
            <div class="coupon_expiry_date">
                <?= \JText::sprintf(  'LIB_XZLIB_COM_VIRTUEMART_AUTO_COUPON_HTML_EXPIRY_DATE' , $coupon_expiry_date   ) ?>
            </div>
        </div>
        
    </div><!-- /.body_auto_coupon -->
</div>