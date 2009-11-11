<?php

require_once 'Zend/View/Helper/FormElement.php';

class Preceptor_View_Helper_RecursiveSelect extends Zend_View_Helper_FormElement
{
	public function recursiveSelect($name, $value = null, $attribs = null, $options = null, $listsep = "<br />\n" )
    {
		$multiple = null;
		
        $info = $this->_getInfo( $name, $value, $attribs, $options, $listsep );
        extract( $info );

        $value = (array) $value;

        $disabled = '';
        if (true === $disable) {
            $disabled = ' disabled="disabled"';
        }

        // Build the surrounding select element first.
        $xhtml = '<select'
                . ' name="' . $this->view->escape($name) . '"'
                . ' id="' . $this->view->escape($id) . '"'
                . $multiple
                . $disabled
                . $this->_htmlAttribs($attribs)
                . ">\n    ";

        $xhtml .= $this->toRecursiveSelect( $options , $value , $attribs );
        
        $xhtml .= "\n</select>";

        return $xhtml;
    }

    protected function _build($value, $label, $selected, $disable)
    {
        if (is_bool($disable)) {
            $disable = array();
        }

        $opt = '<option'
             . ' value="' . $this->view->escape($value) . '"'
             . ' label="' . $this->view->escape($label) . '"';

        // selected?
        if (in_array($value, $selected, 0 === $value)) {
            $opt .= ' selected="selected"';
        }

        // disabled?
        if (in_array($value, $disable)) {
            $opt .= ' disabled="disabled"';
        }

        $opt .= '>' . $this->view->escape($label) . "</option>";

        return $opt;
    }
    
	public function toRecursiveSelect( $options , $value , $attribs , $select = null , $line = null )
	{
		$xhtml = null;
		foreach( $options as $key => $val )
		{
			if( !isset($val['child']) ){
                if( !$line || !isset($attribs['disableOptions']) ){
                    $xhtml .= $this->_build(  $key , $line . ". " . $val['value']['title'] , $value , false );
                }
			} else {
				$xhtml .= $this->_build( $key , $line . ". " . $val['value']['title'] , $value , array() );
				$xhtml .= $this->toRecursiveSelect( $val['child'] , $value , $attribs , $select , $line . " . " );
			}
		}
		
		return $xhtml;
	}

}
	