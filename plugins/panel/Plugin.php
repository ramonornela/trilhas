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
 * @category   Panel
 * @package    Panel_Plugin
 * @copyright  Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 * @license    http://www.gnu.org/licenses/  GNU GPL
 */
class Panel_Plugin extends Tri_Plugin_Abstract
{
    protected $_name = "panel";
    
    protected function _createDb()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `panel` (
                  `id` bigint(20) NOT NULL AUTO_INCREMENT,
                  `classroom_id` bigint(20) NOT NULL,
                  `type` enum('exercise','forum','activity') NOT NULL,
                  `item_id` bigint(20) NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `classroom_id` (`classroom_id`),
                  KEY `item_id` (`item_id`)
                );

                CREATE TABLE IF NOT EXISTS `panel_note` (
                  `id` bigint(20) NOT NULL AUTO_INCREMENT,
                  `user_id` bigint(20) DEFAULT NULL,
                  `panel_id` bigint(20) DEFAULT NULL,
                  `note` tinyint(3) DEFAULT NULL,
                  PRIMARY KEY (`id`),
                  KEY `user_id` (`user_id`),
                  KEY `panel_id` (`panel_id`)
                )";
        
        $this->_getDb()->query($sql);
    }

    public function install()
    {
        $this->_createDb();
    }

    public function activate()
    {
        $this->_addClassroomMenuItem('evaluation','panel','panel/index/index');
        $this->_addAclItem('panel/index/index','identified');
        $this->_addAclItem('panel/index/form','teacher, coordinator, institution');
        $this->_addAclItem('panel/index/save','teacher, coordinator, institution');
        $this->_addAclItem('panel/index/delete','teacher, coordinator, institution');
        $this->_addAclItem('panel/index/find','teacher, coordinator, institution');
        $this->_addAclItem('panel/index/save-note','teacher, coordinator, institution');
        $this->_addAclItem('panel/index/change','teacher, coordinator, institution');
        $this->_addAclItem('panel/certificate/save','teacher, coordinator, institution');
        $this->_addAclItem('panel/certificate/emit','teacher, coordinator, institution');
        $this->_addAclItem('panel/certificate/validate','all');
    }

    public function desactivate()
    {
        $this->_removeClassroomMenuItem('evaluation','panel');
        $this->_removeAclItem('panel/index/index');
        $this->_removeAclItem('panel/index/form');
        $this->_removeAclItem('panel/index/save');
        $this->_removeAclItem('panel/index/delete');
        $this->_removeAclItem('panel/index/find');
        $this->_removeAclItem('panel/index/save-note');
        $this->_removeAclItem('panel/index/change');
        $this->_removeAclItem('panel/certificate/save');
        $this->_removeAclItem('panel/certificate/emit');
        $this->_removeAclItem('panel/certificate/validate');
    }
}
