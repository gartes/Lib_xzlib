<?php
namespace xzlib\app\document\elements\forms;

use Exception;
use Throwable;
use \xzlib\app\document\document as xzlibDoc;

class forms extends xzlibDoc{
	
	
	public function getFormByName( $formName ){


		$fileForm = JPATH_LIBRARIES.DS.'xzlib'.DS.'app'.DS.'components'.DS.'com_virtuemart'.DS.'forms'.DS. $formName.'.xml';

		if (!is_file($fileForm ))
		{
			throw new \Error('XML файл не найден. "'.$formName.'.xml" ');
		}#END IF

		$form = \JForm::getInstance( 'adminControl', JPATH_LIBRARIES.DS.'xzlib'.DS.'app'.DS.'components'.DS.'com_virtuemart'.DS.'forms'.DS. $formName.'.xml');





		$A = (object) array( 'virtuemart_product_id'=>'99999' );
		$form->bind( array ( $formName => $A ) );
		$formHtml = $form->renderFieldset( $formName );



		$data = [
			'formHtml' => $formHtml ,
		];

		$layout = new \JLayoutFile( $formName , JPATH_LIBRARIES . '/xzlib/app/document/elements/forms/forms/layouts');
		

		
		$html   = $layout->render($data); // $displayData
		return $html ;
	}#END FN
	
}#END CLASS





































