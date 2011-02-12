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
 * @category   Notepad
 * @package    Notepad_Plugin
 * @copyright  Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 * @license    http://www.gnu.org/licenses/  GNU GPL
 */
class Notepad_Plugin extends Tri_Plugin_Abstract
{
    protected $_name = "notepad";
    
    protected function _createDb()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `notepad` (
                  `id` bigint(20) NOT NULL AUTO_INCREMENT,
                  `classroom_id` bigint(20) NOT NULL,
                  `user_id` bigint(20) NOT NULL,
                  `description` text NOT NULL,
                  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  KEY `classroom_id` (`classroom_id`),
                  KEY `user_id` (`user_id`)
                )";
        
        $this->_getDb()->query($sql);
    }

    public function install()
    {
        $this->_createDb();
    }

    public function activate()
    {
        $this->_addClassroomMenuItem('support','notepad','notepad/index/index');
        $this->_addAclItem('notepad/index/index','identified');
        $this->_addAclItem('notepad/index/form','teacher, coordinator, institution');
        $this->_addAclItem('notepad/index/save','teacher, coordinator, institution');
        $this->_addAclItem('notepad/index/delete','teacher, coordinator, institution');
    }

    public function desactivate()
    {
        $this->_removeClassroomMenuItem('support','notepad');
        $this->_removeAclItem('notepad/index/index');
        $this->_removeAclItem('notepad/index/form');
        $this->_removeAclItem('notepad/index/save');
        $this->_removeAclItem('notepad/index/delete');
    }
}
