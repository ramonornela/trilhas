<?php
class DashboardController extends Tri_Controller_Action
{
    public function indexAction()
    {
        var_dump(Zend_Auth::getInstance()->getIdentity());exit;
    }
}
