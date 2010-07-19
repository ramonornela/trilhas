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
 * @see Zend_Controller_Action
 */
require_once 'Zend/Controller/Action.php';

/**
 * @category   Tri
 * @package    Tri_Controller
 * @copyright  Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 * @license    http://www.gnu.org/licenses/  GNU GPL
 */
class Tri_Controller_Action extends Zend_Controller_Action
{
    /**
     * (non-PHPdoc)
     * @see Zend_Controller_Action#init()
     */
    public function init()
    {
        $this->_locale();

        if (!isset($theme)) {
            $this->view->theme = 'cupertino';
        }

        $this->_helper->layout->disableLayout();
        if (!$this->_request->isXmlHttpRequest()) {
            $this->_helper->layout->enableLayout();
        }

        $this->view->messages = $this->_helper->flashMessenger->getMessages();
    }

    protected function _locale()
    {
        $locale = Zend_Registry::get('Zend_Locale');

        $this->view->locale = key($locale->getDefault());
        $this->view->date_format = Zend_Locale_Data::getContent($this->view->locale, 'date');
    }

}
