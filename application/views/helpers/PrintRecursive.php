<?php
/**
 * @author Preceptor Educação a Distância <contato@preceptoread.com.br>
 * @license http://www.preceptoread.com.br
 * @package View
 * @subpackage Helper
 * @category Views
 * @final 
 * @version 4.0
 */
class Preceptor_View_Helper_PrintRecursive extends Zend_View_Helper_Abstract
{
	/**
	 * @param array $recursivation => data
	 * @access public
	 * @return string $xhtml 
	 */
	public function printRecursive( $recursivation )
	{
		$xhtml = "<ul>";
		 
		foreach( $recursivation as $key => $val )
		{
			$xhtml .= "<li>";
			
			if( $val['child'] )
			{
				$xhtml .= "<a target='_blank' href='{$this->view->url}/composer/print/view/id/{$val['value']['id']}'>{$val['value']['title']}</a>";
				$xhtml .= $this->printRecursive( $val['child'] );
			}
			else
				$xhtml .= "<a target='_blank' href='{$this->view->url}/composer/print/view/id/{$val['value']['id']}' target='_blank'>{$val['value']['title']}</a>";
			$xhtml .= "</li>";
		}
		$xhtml .= "</ul>";
		return $xhtml;
	}
}