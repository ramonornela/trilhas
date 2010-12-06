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
 * @category   SelectionProcess
 * @package    SelectionProcess_Controller
 * @copyright  Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 * @license    http://www.gnu.org/licenses/  GNU GPL
 */
class SelectionProcess_IndexController extends Tri_Controller_Action
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
        $this->view->title = "Selection process";
    }
	
	/**
	 * Action index.
	 *
	 * @return void
	 */
    public function indexAction()
    {
		$user = Zend_Auth::getInstance()->getIdentity();
		if ('student' == $user->role) {
			$this->_redirect('selection-process/index/view/user/' . $user->id);
		}
        $page  = Zend_Filter::filterStatic($this->_getParam('page'), 'int');
        $query = Zend_Filter::filterStatic($this->_getParam('query'), 'alnum');
        $table = new Zend_Db_Table('selection_process');
        $select = $table->select()->order('end DESC');

        if ($query) {
            $select->where('name LIKE (?)', "%$query%");
        }

        $paginator = new Tri_Paginator($select, $page);
        $this->view->data = $paginator->getResult();
    }

	/**
	 * Action view.
	 *
	 * @return void
	 */
    public function viewAction()
    {
		$user_id = Zend_Filter::filterStatic($this->_getParam('user'), 'int');
		$page  = Zend_Filter::filterStatic($this->_getParam('page'), 'int');
        $query = Zend_Filter::filterStatic($this->_getParam('query'), 'alnum');
		$select = SelectionProcess_Model_SelectionProcess::listPreRegistration();
        if (!$user_id) {
            $this->_helper->_flashMessenger->addMessage('Error');
            $this->_redirect('selection-process/index/');
        }
		$select->where('u.id = ?', $user_id);
        $paginator = new Tri_Paginator($select, $page);
        $this->view->data = $paginator->getResult();
    }

	/**
	 * Action form.
	 *
	 * @return void
	 */
    public function formAction()
    {
		$id = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $form = new SelectionProcess_Form_SelectionProcess();
		if (isset($id) && $id) {
			$table = new Tri_Db_Table('selection_process');
			$data = $table->find($id)->current()->toArray();
		}
		$data['user_id'] = Zend_Auth::getInstance()->getIdentity()->id;
		$form->populate($data);
		$this->view->form = $form;
    }

	/**
	 * Action save
	 *
	 * @return void
	 */
    public function saveAction()
    {
        $form  = new SelectionProcess_Form_SelectionProcess();
        $table = new Tri_Db_Table('selection_process');
        $data  = $this->_getAllParams();
        if ($form->isValid($data)) {
            $data = $form->getValues();
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
            $this->_redirect('selection-process/index/');
        }

        $this->_helper->_flashMessenger->addMessage('Error');
        $this->view->form = $form;
        $this->render('form');
    }

	/**
	 * Action list classroom
	 *
	 * @return void
	 */
	public function listClassroomAction()
	{
		$id = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
		if (empty($id)) {
			$this->_helper->_flashMessenger->addMessage('select a selective process');
            $this->_redirect('selection-process/index/');
		}
		$page  = Zend_Filter::filterStatic($this->_getParam('page'), 'int');
        $query = Zend_Filter::filterStatic($this->_getParam('query'), 'alnum');
		$select = Application_Model_Classroom::getAvailable($id);
		if ($query) {
            $select->where('name LIKE (?)', "%$query%");
        }
        $paginator = new Tri_Paginator($select, $page);
        $this->view->data = $paginator->getResult();
		$this->view->selection_process_id = $id;
		$this->view->coursesAdd = SelectionProcess_Model_SelectionProcess::getAvailableClass($id);
	}
	
	/**
	 * Action add course
	 *
	 * @return void
	 */
	public function addCourseAction()
	{
		$selection_process_id = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
		$classroom_id = Zend_Filter::filterStatic($this->_getParam('classroom'), 'int');
		$table = new Tri_Db_Table('selection_process_classroom');
		if (!empty($selection_process_id) && !empty($classroom_id)) {
			$data = array('selection_process_id' => $selection_process_id, 'classroom_id' => $classroom_id);
			$row = $table->createRow($data);
            $id = $row->save();
			if ($id) {
				$this->_helper->_flashMessenger->addMessage('Success');
	            $this->_redirect('selection-process/index/list-classroom/id/' . $selection_process_id);
			}
		} else 	if (empty($selection_process_id)) {
			$this->_helper->_flashMessenger->addMessage('select a selective process');
            $this->_redirect('selection-process/index/');
		}
		$this->_helper->_flashMessenger->addMessage('Error');
        $this->_redirect('selection-process/index/list-classroom/id/' . $selection_process_id);
	}
	
	/**
	 * Action remove course
	 *
	 * @return void
	 */
	public function removeCourseAction()
	{
		$selection_process_id = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
		$classroom_id = Zend_Filter::filterStatic($this->_getParam('classroom'), 'int');
		$table = new Tri_Db_Table('selection_process_classroom');
		if (!empty($selection_process_id) && !empty($classroom_id)) {
			$data = array('selection_process_id =?' => $selection_process_id, 'classroom_id =?' => $classroom_id);
			if ($table->delete($data)) {
				$this->_helper->_flashMessenger->addMessage('Success');
	            $this->_redirect('selection-process/index/list-classroom/id/' . $selection_process_id);
			}
		} else 	if (empty($selection_process_id)) {
			$this->_helper->_flashMessenger->addMessage('select a selective process');
            $this->_redirect('selection-process/index/');
		}
		$this->_helper->_flashMessenger->addMessage('Error');
        $this->_redirect('selection-process/index/list-classroom/id/' . $selection_process_id);
	}
	
	/**
	 * Action list students
	 *
	 * @return void
	 */
	public function listPreRegistrationAction()
	{
		$id = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
		$page  = Zend_Filter::filterStatic($this->_getParam('page'), 'int');
        $query = Zend_Filter::filterStatic($this->_getParam('query'), 'alnum');
		$course = Zend_Filter::filterStatic($this->_getParam('course'), 'int');
		$select = SelectionProcess_Model_SelectionProcess::listPreRegistration($id, $course);
        if ($query) {
            $select->where('u.name LIKE (?)', "%$query%");
        }
		$table = new Tri_Db_Table('selection_process');
		$this->view->selection_process = $table->find($id)->current();
		$this->view->courses = $this->toSelect(SelectionProcess_Model_SelectionProcess::getCourses($id)->toArray());
		$this->view->course_id = $course;
        $paginator = new Tri_Paginator($select, $page);
        $this->view->data = $paginator->getResult();
	}
	
	/**
	 * Action pre register
	 *
	 * @return void
	 */
	public function preRegisterAction()
	{
		$user_id = Zend_Auth::getInstance()->getIdentity()->id;
		$selectionProcess = Zend_Filter::filterStatic($this->_getParam('selection_process_id'), 'int');
		$result = SelectionProcess_Model_SelectionProcess::verifyUserPermission($user_id, $selectionProcess);
		if (false === $result) {
			$this->_helper->_flashMessenger->addMessage('Error pre-register');
			$this->_redirect('index');
		}
		$course = Zend_Filter::filterStatic($this->_getParam('course'), 'int');
		$form  = new SelectionProcess_Form_PreRegister();
        $table = new Tri_Db_Table('selection_process_user');
		$courseTable = new Tri_Db_Table('course');
        $data  = $this->_getAllParams();
		$data['user_id'] = Zend_Auth::getInstance()->getIdentity()->id;
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($data)) {
	            $data = $form->getValues();
                $row = $table->createRow($data);
				if ($row->save()) {
				    $this->_helper->_flashMessenger->addMessage('Success');
		            $this->_redirect('dashboard/');
				}
	        }
		}
		$this->view->course = $courseTable->find($course)->current();
		$form->populate($data);
        $this->view->form = $form;
	}
	
	/**
	 * Action matriculate
	 *
	 * @return void
	 */
	public function matriculateAction()
	{
		$post = $this->_getAllParams();
		if (count($post['interested'])) {
			$table = new Tri_Db_Table('selection_process_user');
			foreach ($post['interested'] as $interested) {
				$i = explode('-', $interested);
				//Alter status of the interested to ACCEPTS
				$where['selection_process_id = ?'] = $i[0];
				$where['classroom_id = ?'] = $i[1];
				$where['user_id = ?'] = $i[2];
				$row = $table->fetchRow($where);
				$data['status'] = SelectionProcess_Model_SelectionProcess::ACCEPTS;
                $row->setFromArray($data);
                $id = $row->save();
				if (!$id) {
					$this->_helper->_flashMessenger->addMessage('Error');
			        $this->_redirect('selection-process/index/list-pre-registration/id/' . $post['id']);
				}
				//Save new student in classroom_user
				$tableClassRoom = new Tri_Db_Table('classroom_user');
				$classroom['classroom_id'] = $i[1];
				$classroom['user_id'] = $i[2];
				$classroom['status'] = Application_Model_Classroom::REGISTERED;
				$rowClass = $tableClassRoom->createRow($classroom);
                $result = $rowClass->save();
				if ($result) {
					$this->_helper->_flashMessenger->addMessage('Success');
			        $this->_redirect('selection-process/index/list-pre-registration/id/' . $post['id']);
				}
			}
		} 
		$this->_helper->_flashMessenger->addMessage('Error');
        $this->_redirect('selection-process/index/list-pre-registration/id/' . $post['id']);
	}
	
	/**
	 * Action matriculate
	 *
	 * @return void
	 */
	public function rejectAction()
	{
		$post = $this->_getAllParams();
		if (count($post['interested'])) {
			$table = new Tri_Db_Table('selection_process_user');
			foreach ($post['interested'] as $interested) {
				$i = explode('-', $interested);
				//Alter status of the interested to ACCEPTS
				$where['selection_process_id = ?'] = $i[0];
				$where['classroom_id = ?'] = $i[1];
				$where['user_id = ?'] = $i[2];
				$row = $table->fetchRow($where);
				$data['status'] = SelectionProcess_Model_SelectionProcess::REJECTED;
                $row->setFromArray($data);
                $id = $row->save();
				if ($id) {
					$this->_helper->_flashMessenger->addMessage('Success');
			        $this->_redirect('selection-process/index/list-pre-registration/id/' . $post['id']);
				}
			}
		} 
		$this->_helper->_flashMessenger->addMessage('Error');
        $this->_redirect('selection-process/index/list-pre-registration/id/' . $post['id']);
	}
	
	/**
	 * Arranges data to select tag
	 *
	 * @param array $datas	
	 * @return array
	 */
	public function toSelect($datas)
	{
		$result = array('' => $this->view->translate('[select]'));
		foreach ($datas as $data) {
			$result[$data['id']] = $data['name'];
		}
		return $result;
	}
}