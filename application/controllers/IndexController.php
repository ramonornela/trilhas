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
class IndexController extends Tri_Controller_Action
{
	/**
	 * Action index.
	 *
	 * @return void
	 */
    public function indexAction()
    {
        $course   = new Tri_Db_Table('course');
        $calendar = new Tri_Db_Table('calendar');
        $form     = new Application_Form_Login();
        $this->view->courses  = $course->fetchAll(array('status = ?' => 'Active'),
                                                  array('name', 'category'));
        $where = array('classroom_id IS NULL', 'end IS NULL OR end > ?' => date('Y-m-d'));
        $this->view->calendar = $calendar->fetchAll($where, 'begin', 10);
        $this->view->form = $form;
        $this->view->user = Zend_Auth::getInstance()->getIdentity();
        $this->_helper->layout->setLayout('layout');
    }
}
