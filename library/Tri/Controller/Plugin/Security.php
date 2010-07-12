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
 * @see Zend_Controller_Action_HelperBroker
 */
require_once 'Zend/Controller/Plugin/Abstract.php';

/**
 * @category   Tri
 * @package    Tri_Controller
 * @copyright  Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 * @license    http://www.gnu.org/licenses/  GNU GPL
 */
class Tri_Controller_Plugin_Security extends Zend_Controller_Plugin_Abstract
{
    public function  preDispatch(Zend_Controller_Request_Abstract $request) {
        $acl = Zend_Registry::get('acl');
        $identity  = Zend_Auth::getInstance()->getIdentity();
        if (isset($acl)) {
            if ($identity) {
                $role = $identity->role;
            } else {
                $role = 'all';
            }
            $resource  = $request->getParam('module');
            $privilege = $request->getParam('controller')
                       . Tri_Application_Resource_Acl::RESOURCE_SEPARATOR
                       . $request->getParam('action');
            if ($acl->isAllowed($role, $resource, $privilege)) {
                return;
            }
            throw new Tri_Controller_Plugin_Exception('Acesso restrito.');
        }
    }
}