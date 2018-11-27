<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */
$JUser = $displayData['JUser'];

#echo'<pre>';print_r(  $JUser );echo'</pre>'.__FILE__.' '.__LINE__;
$emailTo = $JUser->email;
?>
<div class="registration_completed">
	<div class="head">
		<h4>
			<?= \JText::_('LIB_XZLIB_COM_VIRTUEMART_REGISTRATION_COMPLETED'  ) ?>
		</h4>
	</div>
	<div class="body">
		<div class="messege">
			<?= \JText::sprintf(  'LIB_XZLIB_COM_VIRTUEMART_REGISTRATION_COMPLETED_BODY' , $emailTo   ) ?>
		</div>
		<div class="notify">
			<?= \JText::_(  'LIB_XZLIB_COM_VIRTUEMART_REGISTRATION_COMPLETED_BODY_NOTIFY' ) ?>
		</div>
	</div><!-- /.registration_completed -->
</div>
