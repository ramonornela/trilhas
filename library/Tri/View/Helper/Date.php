<?php
class Tri_View_Helper_Date extends Zend_View_Helper_Abstract
{
    public function date($value)
    {
        return Zend_Filter::filterStatic($value, 'date', array(), 'Tri_Filter');
    }
}
