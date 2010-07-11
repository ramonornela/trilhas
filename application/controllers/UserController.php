<?php
class UserController extends Tri_Controller_Action {
    public function loginAction() {
        $auth = Zend_Auth::getInstance();
        $form = new Application_Form_Login();

        if ($auth->hasIdentity()) {
            $this->_redirect('/dashboard');
        }

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getPost())) {
                $username = $form->getValue('username');
                $password = $form->getValue('password');
                $db = Zend_Db_Table::getDefaultAdapter();
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

    public function logoutAction() {
		Zend_Auth::getInstance()->clearIdentity();
		$this->_redirect( "/user/login/" );
    }

    public function resetAction() {

    }
}
