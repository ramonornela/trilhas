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
class Panel_Model_Certificate
{
    /**
     * Emit certificate
     *
     * @param integer $userId
     * @param integer $classroomId
     */
    public static function emit($userId, $classroomId)
    {
        $classroom = new Tri_Db_Table('classroom');
        $row = $classroom->fetchRow(array('id = ?' => $classroomId));

        if ($row) {
            $classroomUser = new Tri_Db_Table('classroom_user');
            $certificate   = new Tri_Db_Table('certificate');
            $uniqueId      = uniqid();

            $where = array('classroom_id = ?' => $classroomId, 'user_id = ?' => $userId);
            $update = $classroomUser->fetchRow($where);
            $update->status = 'approved';
            $update->save();

            $data = array('classroom_id' => $classroomId,
                          'user_id' => $userId,
                          'unique_id' => $uniqueId,
                          'begin' => $row->begin,
                          'end' => date('Y-m-d'));
            
            $certificate->createRow($data)->save();
        }
    }
}