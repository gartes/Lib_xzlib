<?php
/**
 * Created by PhpStorm.
 * User: oleg
 * Date: 30.10.18
 * Time: 15:11
 */

$app = \JFactory::getApplication() ; 

$prodId = $app->input->getArray([
        'opt'=>[
                'prodId'=>'INT'
        ]
]);

$form = $displayData['formHtml'];

?>


<div id="oneClickPriceHtml">
	<form onsubmit="OneClick.oneClick_form_gCallBack(event)"  method="post" class="form-validate form-oneClick">
		<h4>Предварительный заказ</h4>
		<div id="headLt">Заполните форму, и наши менеджеры свяжутся с вами в течение часа.</div>
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


		<input type="hidden" name="option" value="com_ajax">
		<input type="hidden" name="group" value="system">
		<input type="hidden" name="plugin" value="Xlib">
  


		<input type="hidden" name="component" value="com_virtuemart">


		<input type="hidden" name="task" value="addPreOrder">
        <input type="hidden" name="virtuemart_product_id[]" value="<?= $prodId['opt']['prodId'] ?>">
        <input type="hidden" name="quantity[]" value="1">


		<?php echo JHtml::_( 'form.token' ); ?>

	</form>
</div>

































