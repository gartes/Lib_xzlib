<?php defined('_JEXEC');

/*$mediaVersion = $displayData['mediaVersion']*/

?>
<div class="zxlib tos">
    <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label tos-element">
        <label for="tos" class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect">
            <!-- id="tos" -->
            <input name="agreed" data-error=".errorTxt_tos" type="checkbox"  class="mdl-checkbox__input" checked required>
            <span class="mdl-checkbox__label">Я согласен на обработку <a id="agreed_txt" href="javascript:;" onclick="Xzlib.tos.OnClickRel()">персональных данных.</a></span>
            <div class="errorTxt_tos txt_err_field"></div>


            <span class="mdl-checkbox__focus-helper"></span>
            <span class="mdl-checkbox__box-outline">
                <span class="mdl-checkbox__tick-outline"></span>
            </span>
            <span class="mdl-checkbox__ripple-container mdl-js-ripple-effect mdl-ripple--center" data-upgraded=",MaterialRipple">
                <span class="mdl-ripple"></span>
            </span>
        </label>
    </div>
    <input type="hidden" name="Article" value="" />
</div> 