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
 * @category   Content
 * @package    Content_Controller
 * @copyright  Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 * @license    http://www.gnu.org/licenses/  GNU GPL
 */
class Content_IndexController extends Tri_Controller_Action
{
    public function viewAction()
    {
        $id     = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $string = $this->_getParam('string');

        $restriction = Content_Model_Restriction::verify($id);
		
        if(empty($restriction['has'])) {
            $table = new Tri_Db_Table('content');
            $contentAccess = new Tri_Db_Table('content_access');
            $this->view->data = $table->find($id)->current();

            $data['content_id'] = $id;
            $data['user_id'] = Zend_Auth::getInstance()->getIdentity()->id;

            $contentAccess->createRow($data)->save();
        } else {
            $this->view->restriction = $this->view->translate($restriction['content']) . " " . $restriction['value'];
        }
    }
}