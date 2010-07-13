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
 * @category   Application
 * @package    Application_Controller
 * @copyright  Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 * @license    http://www.gnu.org/licenses/  GNU GPL
 */
class UserController extends Tri_Controller_Action
{
    public function loginAction()
    {
        $auth = Zend_Auth::getInstance();
        $form = new Application_Form_Login();

        if ($auth->hasIdentity()) {
            $this->_redirect('/dashboard');
        }

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $username = $form->getValue('email');
                $password = $form->getValue('password');
                $db      = Zend_Db_Table::getDefaultAdapter();
                $adapter = new Tri_Auth_Adapter_DbTable($db, 'user', 'email', 'password');

                $adapter->setIdentity($username)
                        ->setCredential($password)
                        ->setCredentialTreatment('MD5(?)');

                $result = $auth->authenticate($adapter);

                if ($result->isValid()) {
                    $this->_redirect('/dashboard');
                }
                $this->_helper->flashMessenger->addMessage('Login failed');
            }
        }
        $this->view->form = $form;
    }

    public function logoutAction()
    {
		Zend_Auth::getInstance()->clearIdentity();
		$this->_redirect( "/user/login/" );
    }

    public function resetAction()
    {

    }
}
