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
class CourseController extends Tri_Controller_Action
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
        $this->view->title = "Course";
    }
	
	/**
	 * Action index.
	 *
	 * @return void
	 */
    public function indexAction()
    {
        $page  = Zend_Filter::filterStatic($this->_getParam('page'), 'int');
        $query = Zend_Filter::filterStatic($this->_getParam('query'), 'alnum');
        $table = new Zend_Db_Table('course');
        $select = $table->select()->order('status');

        if ($query) {
            $select->where('name LIKE (?)', "%$query%");
        }

        $paginator = new Tri_Paginator($select, $page);
        $this->view->data = $paginator->getResult();
    }
	
	/**
	 * Action view
	 *
	 * @return void
	 */
    public function viewAction()
    {
        $id = Zend_Filter::filterStatic($this->_getParam('id'), 'int');

        if ($id) {
            $course    = new Tri_Db_Table('course');
            $classroom = new Tri_Db_Table('classroom');

            $this->view->data = $course->find($id)->current();
            $where = array('course_id = ?' => $id, 
                           'status = ?' => 'open',
                           'end >= ? OR end IS NULL' => date('Y-m-d'));
            $this->view->classroom = $classroom->fetchAll($where, 'begin');
			$this->view->selectionProcess = SelectionProcess_Model_SelectionProcess::getAvailableProcessByCourse($id);
        }
    }
	
	/**
	 * Action form
	 *
	 * @return void
	 */
    public function formAction()
    {
        $id   = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $form = new Application_Form_Course();

        if ($id) {
            $table = new Tri_Db_Table('course');
            $row   = $table->find($id)->current();

            if ($row) {
                $form->populate($row->toArray());
            }
        }

        $this->view->form = $form;
    }
	
	/**
	 * Action save
	 *
	 * @return void
	 */
    public function saveAction()
    {
        $form  = new Application_Form_Course();
        $table = new Tri_Db_Table('course');
        $data  = $this->_getAllParams();

        if ($form->isValid($data)) {
            if (!$form->image->receive()) {
                $this->_helper->_flashMessenger->addMessage('Image fail');
            }

            $data = $form->getValues();
            if (!$form->image->getValue()) {
                unset($data['image']);
            }

            if (!$data['responsible']) {
                unset($data['responsible']);
            }

            $data['user_id'] = Zend_Auth::getInstance()->getIdentity()->id;

            if (isset($data['id']) && $data['id']) {
                $row = $table->find($data['id'])->current();
                $row->setFromArray($data);
                $id = $row->save();
            } else {
                unset($data['id']);
                $classroom = new Zend_Db_Table('classroom');
                $row = $table->createRow($data);
                $id = $row->save();

                $responsible = null;
                if (isset($data['responsible'])) {
                    $responsible = $data['responsible'];
                }

                $data = array('course_id'   => $id,
                              'responsible' => $responsible,
                              'name'        => 'Open ' . $data['name'],
                              'begin'       => date('Y-m-d'));

                $row = $classroom->createRow($data);
                $row->save();
            }

            $this->_helper->_flashMessenger->addMessage('Success');
            $this->_redirect('course/form/id/'.$id);
        }

        $this->_helper->_flashMessenger->addMessage('Error');
        $this->view->form = $form;
        $this->render('form');
    }
}
