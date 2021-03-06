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
class Admin_UserController extends Tri_Controller_Action
{
	/**
	 * Init
 	 *
	 * Call parent init and set title box.
	 *
	 * @return void
	 */
    public function init()
    {
        parent::init();
        $this->view->title = "User";
        $this->_helper->layout->setLayout('admin');

    }
	
	/**
	 * Action index.
	 *
	 * @return void
	 */
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

    public function formAction()
    {
        $id   = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $form = new Admin_Form_User();

        if ($id) {
            $table = new Tri_Db_Table('user');
            $row   = $table->find($id)->current();

            if ($row) {
                $form->populate($row->toArray());
            }
        } else {
            $form->getElement('password')->setAllowEmpty(false);
        }
        
        $this->view->form = $form;
    }

    public function saveAction()
    {
        $messages     = array();
        $isValidEmail = true;
        $session      = new Zend_Session_Namespace('data');
        $form         = new Admin_Form_User();
        $table        = new Tri_Db_Table('user');
        $data         = $this->_getAllParams();

        if ($data['email'] && (!isset($data['id']) || !$data['id'])) {
            $row = $table->fetchRow(array('email = ?' => $data['email']));
            if ($row) {
                $isValidEmail = false;
                $messages[] = 'Email existing';
            }
        }

        if (!isset($data['id']) || !$data['id']) {
            $form->getElement('password')->setAllowEmpty(false);
        }

        if ($form->isValid($data) && $isValidEmail) {
            if (!$form->image->receive()) {
                $messages[] = 'Image fail';
            }

            $data = $form->getValues();
            if (!$form->image->getValue()) {
                unset($data['image']);
            }

            if (!$data['password']) {
                unset($data['password']);
            }
            
            if (isset($data['id']) && $data['id'] && Zend_Auth::getInstance()->hasIdentity()) {
                $row = $table->find($data['id'])->current();
                $row->setFromArray($data);
                $id = $row->save();
            } else {
                unset($data['id']);
                $row = $table->createRow($data);
                $id = $row->save();

                $session->attempt = 0;
                $data['password'] = $this->_getParam('password');
                $this->view->data = $data;
                
                $mail = new Zend_Mail(APP_CHARSET);
                $mail->setBodyHtml($this->view->render('user/welcome.phtml'));
                $mail->setSubject($this->view->translate('Welcome'));
                $mail->addTo($data['email'], $data['name']);
                $mail->send();

                $result = $this->login($data['email'], $data['password']);
                if ($result->isValid()) {
                    if ($session->url) {
                        $this->_helper->_flashMessenger->addMessage('Success');
                        $url = $session->url;
                        $session->url = null;
                        $this->_redirect($url);
                    }
                }
            }

            $this->_helper->_flashMessenger->addMessage('Success');

            $identity = Zend_Auth::getInstance()->getIdentity();
            if ($identity->id == $id) {
                $this->_redirect('user/edit');
            }

            if ($identity->role == 'institution') {
                $this->_redirect('user');
            }
            
            $this->_redirect('dashboard');
        }

        $messages[] = 'Error';
        $this->view->messages = $messages;
        $this->view->form = $form;
        $this->render('form');
    }
    
	/**
	 * Action logout.
	 *
	 * @return void
	 */
    public function logoutAction()
    {
		Zend_Auth::getInstance()->clearIdentity();
		$this->_redirect( "/index" );
    }
	
	
	/**
	 * Action reset.
	 *
	 * @todo implement functionality. 
	 * @return void
	 */
    public function resetAction()
    {

    }
	
	private function generateCaptcha()
	{
		$captcha = new Zend_Captcha_Figlet(array(
		    'name' => 'captch_verify',
		    'wordLen' => 3,
		    'timeout' => 200,
		));
		
		$captcha->generate();
		return $captcha->render();
	}
}
