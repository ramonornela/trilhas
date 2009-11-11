<?php
class Preceptor_View_Helper_Status extends Zend_View_Helper_Abstract
{	
	public function status( $status , $valeus )
	{
		foreach( $valeus as $key => $val )
		{
			if( $key == $status )
				return $this->view->translate( $val );
		}
	}
}