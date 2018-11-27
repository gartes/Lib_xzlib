<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 29.10.18
 * Time: 6:47
 */

$form = $displayData['formHtml'];

?>



<div id="formDownPriceHtml">
	<form onsubmit="downPrice_form_gCallBack(event)"  method="post" class="form-validate form-downPrice" data-re_id="downPrice_form">
		<h4>Нашли этот товар дешевле?</h4>
		<div id="headLt">Пришлите нам ссылку на этот товар в другом магазине, и мы свяжемся с Вами в течении 24 часов.</div>
		<?= $form ?>

		<label for="tos" class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect">
			<!-- id="tos" -->
			<input name="downPrice_agreed" data-error=".errorTxt_tos" type="checkbox" class="mdl-checkbox__input" checked="" required="">
			<span class="mdl-checkbox__label">Я согласен на обработку <a  href="javascript:;" onclick="Xzlib.tos.OnClickRel()">персональных данных.</a></span>

		</label>

		<button id="form-downPrice-submit" type="submit" onclick="" class="btn btn-small button-apply btn-success">
			<span class="icon-apply icon-white" aria-hidden="true"></span>
			Отправить
		</button>

		<input type="hidden" name="XzlibRequest" value="1">
		<input type="hidden" name="option" value="com_ajax">
		<input type="hidden" name="group" value="system">
		<input type="hidden" name="plugin" value="Xlib">
		<input type="hidden" name="component" value="com_virtuemart">
		<input type="hidden" name="view" value="copon_user">

		<input type="hidden" name="nosef" value="1">
		<input type="hidden" name="task" value="send_downPriceForm">

		<input type="hidden" name="g-recaptcha-response">
		<?php echo JHtml::_( 'form.token' ); ?>

		<div class="g-recaptcha"
		     data-sitekey="6LeCYHcUAAAAAAJALr7MI0IkN3ueCXv6DgNnHjCE"
		     data-callback="onReCaptchaSubmit"
		     data-size="invisible">
		</div>
	</form>

</div>
