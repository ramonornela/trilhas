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
 * @category   chat
 * @package    chat_Plugin
 * @copyright  Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 * @license    http://www.gnu.org/licenses/  GNU GPL
 */
class chat_Plugin extends Tri_Plugin_Abstract
{
    protected $_name = "chat";
    
    protected function _createDb()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `classroom` (
                  `id` bigint(20) NOT NULL AUTO_INCREMENT,
                  `course_id` bigint(20) NOT NULL,
                  `responsible` bigint(20) DEFAULT NULL,
                  `name` varchar(255) NOT NULL,
                  `begin` date NOT NULL,
                  `end` date DEFAULT NULL,
                  `max_student` int(10) DEFAULT NULL,
                  `amount` decimal(20,2) DEFAULT NULL,
                  `status` enum('active','open','inactive') NOT NULL DEFAULT 'active',
                  PRIMARY KEY (`id`),
                  KEY `course_id` (`course_id`),
                  KEY `responsible` (`responsible`)
                );

                CREATE TABLE IF NOT EXISTS `classroom_user` (
                  `user_id` bigint(20) NOT NULL,
                  `classroom_id` bigint(20) NOT NULL,
                  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                  `status` enum('registered','approved','disapproved','justified','not-justified') NOT NULL DEFAULT 'registered',
                  PRIMARY KEY (`user_id`,`classroom_id`),
                  KEY `classroom_id` (`classroom_id`)
                );";
        
        $this->_getDb()->query($sql);
    }

    public function install()
    {
        $this->_createDb();
    }

    public function activate()
    {
        $this->_addClassroomMenuItem('communication','message','chat/message/index');
        $this->_addClassroomMenuItem('communication','chat','chat/room/index');
        $this->_addAclItem('chat/chat/index', 'identified');
        $this->_addAclItem('chat/chat/form', 'identified');
        $this->_addAclItem('chat/chat/save', 'identified');
        $this->_addAclItem('chat/chat/find', 'identified');
        $this->_addAclItem('chat/room/index', 'identified');
        $this->_addAclItem('chat/room/form', 'teacher, coordinator, institution');
        $this->_addAclItem('chat/room/save', 'teacher, coordinator, institution');
        $this->_addAclItem('chat/room/delete', 'teacher, coordinator, institution');
        $this->_addAclItem('chat/room/live', 'identified');
        $this->_addAclItem('chat/room/live-save', 'identified');
        $this->_addAclItem('chat/room/view', 'identified');
        $this->_addAclItem('chat/message/index', 'identified');
        $this->_addAclItem('chat/message/view', 'identified');
        $this->_addAclItem('chat/message/reply', 'identified');
        $this->_addAclItem('chat/message/save', 'identified');
        $this->_addAclItem('chat/message/delete', 'identified');
    }

    public function desactivate()
    {
        $this->_removeClassroomMenuItem('communication','message');
        $this->_removeClassroomMenuItem('communication','chat');
        $this->_removeAclItem('chat/chat/index');
        $this->_removeAclItem('chat/chat/form');
        $this->_removeAclItem('chat/chat/save');
        $this->_removeAclItem('chat/chat/find');
        $this->_removeAclItem('chat/room/index');
        $this->_removeAclItem('chat/room/form');
        $this->_removeAclItem('chat/room/save');
        $this->_removeAclItem('chat/room/delete');
        $this->_removeAclItem('chat/room/live');
        $this->_removeAclItem('chat/room/live-save');
        $this->_removeAclItem('chat/room/view');
        $this->_removeAclItem('chat/message/index');
        $this->_removeAclItem('chat/message/view');
        $this->_removeAclItem('chat/message/reply');
        $this->_removeAclItem('chat/message/save');
        $this->_removeAclItem('chat/message/delete');
    }
}

