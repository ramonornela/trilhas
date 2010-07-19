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
 * @category   Application
 * @package    Application_Model
 * @copyright  Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 * @license    http://www.gnu.org/licenses/  GNU GPL
 */
class Application_Model_Classroom
{
    /**
     * Get all possible classroom
     *
     * @param int $user_id
     * @return array
     */
    public static function getAllByUser($user_id)
    {
        $db   = Zend_Db_Table::getDefaultAdapter();
        $cols = array('cr.*', 'c.*', 'c.id as id', 'cr.id as classroom_id');
        $course = array('c' => 'course');
        $classroom = array('cr' => 'classroom');
        $classUser = array('cu' => 'classroom_user');
        
        //by course or classroom responsible
        $select = $db->select()
                     ->from($classroom, $cols)
                     ->join($course, 'c.id = cr.course_id', array())
                     ->where('c.responsible = ? OR cr.responsible = ?', $user_id);
        $rsResponsible = $db->fetchAll($select);

        //by registration
        $select = $db->select()
                     ->from($classroom, $cols)
                     ->join($course, 'c.id = cr.course_id', array())
                     ->join($classUser, 'cr.id = cu.classroom_id', array())
                     ->where('cu.user_id = ?', $user_id);
        $rsRegistry = $db->fetchAll($select);

        foreach ($rsResponsible as $responsible) {
            foreach ($rsRegistry as $key => $registry) {
                if ($responsible === $registry) {
                    continue;
                }
                $response[] = $registry;
            }
            $response[] = $responsible;
        }
        return $response;
    }
}
