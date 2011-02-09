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
        
        if (in_array('content', Tri_Config::get('tri_plugins', true))) {
            $data = Application_Model_Content::fetchAllOrganize($row->course_id);

            if (!$data) {
                Application_Model_Content::createInitialContent($row->course_id);
                $data = Application_Model_Content::fetchAllOrganize($row->course_id);
            }

            $this->view->current = Application_Model_Content::getLastAccess($id, $data);
            $this->view->data = Zend_Json::encode($data);

            $session->contents = $this->view->data;
        }
        
        $this->_helper->layout->setLayout('layout');
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
                $row = $classroom->fetchRow(array('id = ?' => $id));
                
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

        try {
            $classroomUser->createRow($data)->save();
            $this->_helper->_flashMessenger->addMessage('Success');
        } catch (Exception $e) {
            $this->_helper->_flashMessenger->addMessage('Student already registered in this class');
        }

        $this->_redirect('/dashboard');
    }
}