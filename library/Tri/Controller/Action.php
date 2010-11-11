<?php
/**
 * Trilhas - Learning Management System
 * Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * @see Zend_Controller_Action
 */
require_once 'Zend/Controller/Action.php';

/**
 * @category   Tri
 * @package    Tri_Controller
 * @copyright  Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 * @license    http://www.gnu.org/licenses/  GNU GPL
 */
class Tri_Controller_Action extends Zend_Controller_Action
{
    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action#init()
     */
    public function init()
    {
        $this->_security();
        $this->_locale();

        $this->view->theme = THEME;

        $this->_helper->layout->disableLayout();
        if (!$this->_request->isXmlHttpRequest()) {
            $this->_helper->layout->enableLayout();
            $this->_helper->layout->setLayout('solo');
        } elseif ($this->_hasParam('layout')) {
            $this->_helper->layout->setLayout($this->_getParam('layout'));
        }

        $messages = $this->_helper->flashMessenger->getMessages();

        if (count($messages)) {
            $this->view->messages = $messages;
            $this->getResponse()->prepend('messages', $this->view->render('message.phtml'));
        }
    }

    protected function _security()
    {
        $acl      = Zend_Registry::get('acl');
        $identity = Zend_Auth::getInstance()->getIdentity();

        $role = 'all';
        if ($identity) {
            $role = $identity->role;
        }

        $resource  = $this->_getParam('module');
        $privilege = $this->_getParam('controller')
                   . Tri_Application_Resource_Acl::RESOURCE_SEPARATOR
                   . $this->_getParam('action');

        if (!$acl->isAllowed($role, $resource, $privilege)) {
            $url = base64_encode($_SERVER['REQUEST_URI']);
            $this->_redirect('/user/login/url/'. $url);
        }
    }

    protected function _locale()
    {
        $locale = Zend_Registry::get('Zend_Locale');

        $this->view->locale = key($locale->getDefault());
        $this->view->date_format = Zend_Locale_Data::getContent($this->view->locale, 'date');
    }

}
