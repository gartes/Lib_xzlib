<?php 
 
defined('_JEXEC') or die;
JHtml::_('behavior.formvalidator');
$url = vmURI::getCurrentUrlBy('request');
$Field = $displayData['Field'];
$Element = $displayData['Element'];
 
  
  
?>

<div id="getCouponCode">
    <form id="getCouponCodeForm" method="post"  name="userForm" action="<?php echo $url ?>" class="form-validate">
        <?= $Field ?>
        <?= $Element ?>
             
        
        <div class="buttonBar">
            <button id="sendFormGetCoupon"  class="button"  type="submit">Получить купон</button>
            <button class="button" type="reset" onclick="closeNav()" ><?= \vmText::_('COM_VIRTUEMART_CANCEL'); ?></button>
            <?php /**                       
                <button 
                class="button g-recaptcha" 
                data-sitekey="<?= $reCAPTCHA->cnf['public_key'] ?>" 
                data-callback="offCanvas_captchaValid"
                type="submit"  >
                Получить купон 
            </button> 
                <!--<script src='https://www.google.com/recaptcha/api.js' defer ></script>-->
                */?>
                    
        </div>
        
        <input type="hidden" name="option" value="com_ajax" />
        <input type="hidden" name="group" value="system" />
        <input type="hidden" name="plugin" value="Xlib" />
        <input type="hidden" name="component" value="" />
        <input type="hidden" name="view" value="copon_user" />
        <input type="hidden" name="task" value="add_coupon_code_nUser" />
        <input type="hidden" name="address_type" value="BT">
       <!-- <input type="hidden" name="g-recaptcha-response">-->
        
        <?= \JHtml::_( 'form.token' ); ?>
    </form>
</div>




























 