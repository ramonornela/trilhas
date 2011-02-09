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
 * @category   Activity
 * @package    Activity_Plugin
 * @copyright  Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 * @license    http://www.gnu.org/licenses/  GNU GPL
 */
class Activity_Plugin extends Tri_Plugin_Abstract
{
    protected $_name = "activity";
    
    protected function _createDb()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `activity` (
                  `id` bigint(20) NOT NULL AUTO_INCREMENT,
                  `user_id` bigint(20) NOT NULL,
                  `classroom_id` bigint(20) NOT NULL,
                  `title` varchar(255) NOT NULL,
                  `description` text,
                  `begin` date NOT NULL,
                  `end` date DEFAULT NULL,
                  `status` enum('active','inactive','final') NOT NULL DEFAULT 'active',
                  PRIMARY KEY (`id`),
                  KEY `user_id` (`user_id`),
                  KEY `classroom_id` (`classroom_id`)
                );

                CREATE TABLE IF NOT EXISTS `activity_text` (
                  `id` bigint(20) NOT NULL AUTO_INCREMENT,
                  `user_id` bigint(20) NOT NULL,
                  `sender` bigint(20) NOT NULL,
                  `activity_id` bigint(20) NOT NULL,
                  `description` text NOT NULL,
                  `status` enum('open','final','close') DEFAULT 'open',
                  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY (`id`),
                  KEY `user_id` (`user_id`),
                  KEY `activity_id` (`activity_id`),
                  KEY `sender` (`sender`)
                )";
        
        $this->_getDb()->query($sql);
    }

    public function install()
    {
        $this->_createDb();
    }

    public function activate()
    {
        $this->_addClassroomMenuItem('evaluation','activity','activity/index/index');
        $this->_addAclItem('activity/index/index','identified');
        $this->_addAclItem('activity/index/form','teacher, coordinator, institution');
        $this->_addAclItem('activity/index/save','teacher, coordinator, institution');
        $this->_addAclItem('activity/index/view','identified');
        $this->_addAclItem('activity/text/save','identified');
        $this->_addAclItem('activity/text/index','identified');
        $this->_addAclItem('activity/text/view','identified');
        $this->_addAclItem('activity/correct/index','teacher, coordinator, institution');
        $this->_addAclItem('activity/correct/save','teacher, coordinator, institution');
    }

    public function desactivate()
    {
        $this->_removeClassroomMenuItem('evaluation','activity');
        $this->_removeAclItem('activity/index/index');
        $this->_removeAclItem('activity/index/form');
        $this->_removeAclItem('activity/index/save');
        $this->_removeAclItem('activity/index/view');
        $this->_removeAclItem('activity/text/save');
        $this->_removeAclItem('activity/text/index');
        $this->_removeAclItem('activity/text/view');
        $this->_removeAclItem('activity/correct/index');
        $this->_removeAclItem('activity/correct/save');
    }
}
