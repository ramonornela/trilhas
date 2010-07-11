<?php
class Tri_View_Helper_Date {
    public function date($value) {
        return Zend_Filter::filterStatic($value, 'date', array(), 'Tri_Filter');
    }
}