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
        $select = $table->select()->order('status');

        if ($query) {
            $select->where('name LIKE (?)', "%$query%");
        }

        $paginator = new Tri_Paginator($select, $page);
        $this->view->data = $paginator->getResult();
    }

    public function viewAction()
    {
        $id = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $classroom = new Zend_Db_Table('classroom');
        $rowset = $classroom->find($id);

        if (!count($rowset)) {
            $this->_redirect('/dashboard');
        }
        $row = $rowset->current();
        $content = new Zend_Db_Table('content');
        $session = new Zend_Session_Namespace('data');
        $session->classroom_id = $row->id;
        $this->view->data = $content->fetchRow(array('course_id = ?' => $row->course_id));
        $this->_helper->layout->setLayout('layout');
    }
}