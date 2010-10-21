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
class ClassroomController extends Tri_Controller_Action
{
    public function init()
    {
        parent::init();
        $this->view->title = "Classroom";
    }

    public function indexAction()
    {
        $page  = Zend_Filter::filterStatic($this->_getParam('page'), 'int');
        $query = Zend_Filter::filterStatic($this->_getParam('query'), 'alnum');
        $table = new Zend_Db_Table('classroom');
        $select = $table->select(true)
                        ->setIntegrityCheck(false)
                        ->join('course', 'course.id = classroom.course_id', 'course.name as cname')
                        ->order('status');

        if ($query) {
            $select->where('classroom.name LIKE (?)', "%$query%");
        }

        $paginator = new Tri_Paginator($select, $page);
        $this->view->data = $paginator->getResult();
    }


    public function viewAction()
    {
        $classroom = new Zend_Db_Table('classroom');
        $id        = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $rowset    = $classroom->find($id);

        if (!count($rowset)) {
            $this->_redirect('/dashboard');
        }

        $row     = $rowset->current();
        $session = new Zend_Session_Namespace('data');
        $session->classroom_id = $row->id;
        $session->course_id = $row->course_id;
        $data = Application_Model_Content::fetchAllOrganize($row->course_id);

        if (!$data) {
            Application_Model_Content::createInitialContent($row->course_id);
            $data = Application_Model_Content::fetchAllOrganize($row->course_id);
        }
        $this->view->data = Zend_Json::encode($data);

        $session->contents = $this->view->data;
        
        $this->_helper->layout->setLayout('layout');
    }

    public function formAction()
    {
        $id   = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $form = new Application_Form_Classroom();

        if ($id) {
            $table = new Tri_Db_Table('classroom');
            $row   = $table->find($id)->current();

            if ($row) {
                $form->populate($row->toArray());
            }
        }

        $this->view->form = $form;
    }

    public function saveAction()
    {
        $form  = new Application_Form_Classroom();
        $table = new Tri_Db_Table('classroom');
        $data  = $this->_getAllParams();

        if ($form->isValid($data)) {
            $data = $form->getValues();
            $data['user_id'] = Zend_Auth::getInstance()->getIdentity()->id;

            if (!$data['amount']) {
                unset($data['amount']);
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
            $this->_redirect('classroom/form/id/'.$id);
        }

        $this->view->messages = array('Error');
        $this->view->form = $form;
        $this->render('form');
    }

    public function signAction()
    {
        $data = array();
        if ($this->_hasParam('id')) {
            $id = Zend_filter::filterStatic($this->_getParam('id'), 'int');
            if (Application_Model_Classroom::isAvailable($id)) {
                $session = new Zend_Session_Namespace('data');
                $session->classroom_id = $id;
                $classroom = new Zend_Db_Table('classroom');
                $row= $classroom->fetchRow(array('id = ?' => $id));
                if (PAYMENT && $row->amount && $row->amount > 0) {
                    $this->_redirect('/classroom/pay');
                } else {
                    $this->_redirect('/classroom/register');
                }
            }
        }
        $this->view->messages = array('Unavailable');
    }

    public function payAction()
    {
        $classroom = new Tri_Db_Table('classroom');
        $session   = new Zend_Session_Namespace('data');
        $select    = $classroom->select(true)
                               ->setIntegrityCheck(false)
                               ->join('course', 'course.id = classroom.course_id', 'course.name as cname')
                               ->where('classroom.id = ?', $session->classroom_id)
                               ->order('status');
        
        $this->view->data = $classroom->fetchRow($select);
    }

    public function registerAction()
    {
        $session = new Zend_Session_Namespace('data');
        $classroomUser = new Tri_Db_Table('classroom_user');

        $data['user_id'] = Zend_Auth::getInstance()->getIdentity()->id;
        $data['classroom_id'] = $session->classroom_id;

        $classroomUser->createRow($data)->save();

        $this->_helper->_flashMessenger->addMessage('Success');
        $this->_redirect('/dashboard');
    }


    public function listUserAction()
    {
        $id = Zend_filter::filterStatic($this->_getParam('id'), 'int');
        $classroom     = new Tri_Db_Table('classroom');
        $classroomUser = new Tri_Db_Table('classroom_user');

        $select = $classroomUser->select(true)
                                ->setIntegrityCheck(false)
                                ->join('user', 'classroom_user.user_id = user.id')
                                ->where('classroom_user.classroom_id = ?', $id)
                                ->order('name');
        $this->view->data = $classroomUser->fetchAll($select);

        $select = $classroom->select(true)
                            ->setIntegrityCheck(false)
                            ->join('course', 'course.id = classroom.course_id', 'course.name as cname')
                            ->where('classroom.id = ?', $id)
                            ->order('status');
        
        $this->view->classroom = $classroom->fetchRow($select);
        $this->view->id = $id;
    }

    public function matriculateAction()
    {
        $id = Zend_filter::filterStatic($this->_getParam('id'), 'int');
        if ($this->_hasParam('userId')) {
            $classroomUser = new Tri_Db_Table('classroom_user');
            
            $data['user_id'] = $this->_getParam('userId');
            $data['classroom_id'] = $id;

            $classroomUser->createRow($data)->save();

            $this->_helper->_flashMessenger->addMessage('Success');
            $this->_redirect('/classroom/list-user/id/'.$id);
        }
        $table = new Zend_Db_Table('user');
        $select = $table->select()->order('id DESC');

        $paginator = new Tri_Paginator($select, 1);
        $this->view->data = $paginator->getResult();
        $this->view->id = $id;
    }

    public function searchUserAction()
    {
        $id    = Zend_filter::filterStatic($this->_getParam('id'), 'int');
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
        $this->view->id = $id;
        $this->render('matriculate');
    }

    public function deleteAction()
    {
        $id     = Zend_filter::filterStatic($this->_getParam('id'), 'int');
        $userId = Zend_filter::filterStatic($this->_getParam('userId'), 'int');
        $classroomUser = new Tri_Db_Table('classroom_user');
        $classroomUser->delete(array('user_id = ?' => $userId, 'classroom_id = ?' => $id));

        $this->_helper->_flashMessenger->addMessage('Success');
        $this->_redirect('/classroom/list-user/id/'.$id);
    }
}