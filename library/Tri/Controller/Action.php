<?php
class Tri_Controller_Action extends Zend_Controller_Action
{
    public function init()
    {
        $this->_security();
        $this->_locale();

        if (!isset($theme)) {
            $this->view->theme = 'cupertino';
        }

        $this->_helper->layout->disableLayout();
        if (!$this->_request->isXmlHttpRequest()) {
            $this->_helper->layout->enableLayout();
        }

        $this->view->messages = $this->_helper->flashMessenger->getMessages();
    }

    protected function _locale()
    {
        $locale = Zend_Registry::get('Zend_Locale');

        $this->view->locale = key($locale->getDefault());
        $this->view->date_format = Zend_Locale_Data::getContent($this->view->locale, 'date');
    }

    protected function _security()
    {
        // apply access control list
        $container = $this->getInvokeArg('bootstrap')->getContainer();
        $identity  = Zend_Auth::getInstance()->getIdentity();
        if (isset($container->acl)) {
            if ($identity) {
                $role = $identity['role'];
            } else {
                $role = 'all';
            }
            $acl       = $container->acl;
            $resource  = $this->_getParam('module');
            $privilege = $this->_getParam('controller')
                       . Tri_Application_Resource_Acl::RESOURCE_SEPARATOR
                       . $this->_getParam('action');
            if ($acl->isAllowed($role, $resource, $privilege)) {
                return;
            }
            //throw new Exception('Acesso restrito.');
        }
    }
}
