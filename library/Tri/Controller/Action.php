<?php
class Tri_Controller_Action extends Zend_Controller_Action{
    public function init() {
        $this->view->locale = key(Zend_Registry::get('Zend_Locale')->getDefault());
        $this->view->date_format = Zend_Locale_Data::getContent($this->view->locale, 'date');
        
        if (!isset($theme)) {
            $this->view->theme = 'cupertino';
        }
    }
}