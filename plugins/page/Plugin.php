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
 * @category   Page
 * @package    Page_Plugin
 * @copyright  Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 * @license    http://www.gnu.org/licenses/  GNU GPL
 */
class Page_Plugin extends Tri_Plugin_Abstract
{
    protected $_name = "page";
    
    protected function _createDb()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `page` (
                  `id` bigint(20) NOT NULL AUTO_INCREMENT,
                  `title` varchar(255) NOT NULL,
                  `description` text NOT NULL,
                  `position` tinyint(4) NOT NULL,
                  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
                  PRIMARY KEY (`id`)
                )";
        
        $this->_getDb()->query($sql);
    }

    public function install()
    {
        $this->_createDb();
    }

    public function activate()
    {
        $this->_addAdminMenuItem('page','page/index/index');
        $this->_addAclItem('page/index/index','teacher, coordinator, institution');
        $this->_addAclItem('page/index/view','all');
        $this->_addAclItem('page/index/form','teacher, coordinator, institution');
        $this->_addAclItem('page/index/save','teacher, coordinator, institution');
        $this->_addAclItem('page/index/delete','teacher, coordinator, institution');
    }

    public function desactivate()
    {
        $this->_removeAdminMenuItem('page');
        $this->_removeAclItem('page/index/index');
        $this->_removeAclItem('page/index/view');
        $this->_removeAclItem('page/index/form');
        $this->_removeAclItem('page/index/save');
        $this->_removeAclItem('page/index/delete');
    }
}
