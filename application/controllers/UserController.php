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
    public function init()
    {
        parent::init();
        $this->view->title = "User";
    }

    public function indexAction()
    {
        $page  = Zend_Filter::filterStatic($this->_getParam('page'), 'int');
        $query = $this->_getParam('query');
        $table = new Zend_Db_Table('user');
        $select = $table->select()->order('name');

        if ($query) {
            $parts = explode(' ', $query);
            foreach($parts as $part){
                $select->where('name LIKE ?', "%$part%");
            }
            $select->orWhere('email LIKE ?', "%$query%");
        }

        $paginator = new Tri_Paginator($select, $page);
        $this->view->data = $paginator->getResult();
    }

    public function loginAction()
    {
        $this->view->title = "Login";
        $session = new Zend_Session_Namespace('data');
        $auth    = Zend_Auth::getInstance();
        $form    = new Application_Form_Login();

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
                    if ($session->url) {
                        $url = $session->url;
                        $session->url = null;
                        $this->_redirect($url);
                    }

                    $this->_redirect('/dashboard');
                }
                $this->_helper->flashMessenger->addMessage('Login failed');
            }
        }

        if ($this->_hasParam('url')) {
            $path = str_replace('index.php','', $_SERVER['SCRIPT_NAME']);
            $url = base64_decode($this->_getParam('url'));
            $url = str_replace($path, '', $url);
            $session->url = $url;
        }
        $this->view->form = $form;
    }

    public function formAction()
    {
        $userId   = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $form     = new Application_Form_User();
        $identity = Zend_Auth::getInstance()->getIdentity();

        if ($identity) {
            $id = $identity->id;

            if ($identity->role == 'institution') {
                $id = 0;
            }

            if ($userId && $identity->role == 'institution') {
                $id = $userId;
            }

            $table = new Tri_Db_Table('user');
            $row   = $table->find($id)->current();

            if ($row) {
                $form->populate($row->toArray());
            }
        }
        $this->view->form = $form;
    }

    public function saveAction()
    {
        $form  = new Application_Form_User();
        $table = new Tri_Db_Table('user');
        $data  = $this->_getAllParams();

        if ($form->isValid($data)) {
            if (!$form->image->receive()) {
                $this->_helper->_flashMessenger->addMessage('Image fail');
            }

            $data = $form->getValues();
            if (!$form->image->getValue()) {
                unset($data['image']);
            }

            if (!$data['password']) {
                unset($data['password']);
            }
            
            if (isset($data['id']) && $data['id']) {
                $row = $table->find($data['id'])->current();
                $row->setFromArray($data);
                $id = $row->save();
            } else {
                unset($data['id']);
                $row = $table->createRow($data);
                $id = $row->save();
            }

            $this->_helper->_flashMessenger->addMessage('Success');
            $this->_redirect('user/form/id/'.$id);
        }

        $this->view->messages = array('Error');
        $this->view->form = $form;
        $this->render('form');
    }
    
    public function logoutAction()
    {
		Zend_Auth::getInstance()->clearIdentity();
		$this->_redirect( "/index" );
    }

    public function resetAction()
    {

    }
}
