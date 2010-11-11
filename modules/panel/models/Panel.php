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
 * @package    Panel_Model
 * @copyright  Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 * @license    http://www.gnu.org/licenses/  GNU GPL
 */
class Panel_Model_Panel
{
    /**
     *
     * @param string $type
     * @param integer $id
     * @param integer $note
     */
    public static function addNote($userId, $type, $id, $note)
    {
        $session = new Zend_Session_Namespace('data');
        $panel = new Tri_Db_Table('panel');
        $row = $panel->fetchRow(array('type = ?' => $type,
                                      'item_id = ?' => $id,
                                      'classroom_id = ?' => $session->classroom_id));

        if ($row) {
            $panelNote = new Tri_Db_Table('panel_note');
            $panelNote->delete(array('panel_id = ?' => $row->id,
                                     'user_id = ?' => $userId));

            $panelNote->createRow(array('panel_id' => $row->id,
                                        'user_id' => $userId,
                                        'note' => $note))->save();
        }
    }
}