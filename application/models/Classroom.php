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
     * Status type: waiting
     */
	const REGISTERED = 'registered';
		
    /**
     * Get all possible classroom
     *
     * @param int $userId
     * @return array
     */
    public static function getAllByUser($userId)
    {
        $session   = new Zend_Session_Namespace('data');
        $db        = Zend_Db_Table::getDefaultAdapter();
        $cols      = array('cr.*', 'c.*', 'c.id as id', 'cr.id as classroom_id');
        $course    = array('c' => 'course');
        $classroom = array('cr' => 'classroom');
        $classUser = array('cu' => 'classroom_user');
        $data      = array();
        
        //by course or classroom responsible
        $select = $db->select()
                     ->from($classroom, $cols)
                     ->join($course, 'c.id = cr.course_id', array())
                     ->where('c.responsible = ? OR cr.responsible = ?', $userId)
                     ->where('cr.status = ?', 'active');
        $responsibles = $db->fetchAll($select);

        //by registration
        $select = $db->select()
                     ->from($classroom, $cols)
                     ->join($course, 'c.id = cr.course_id', array())
                     ->join($classUser, 'cr.id = cu.classroom_id', array())
                     ->where('cu.user_id = ?', $userId)
                     ->where('cu.status = ?', 'registered')
                     ->where('cr.begin <= ?', date('Y-m-d'))
                     ->where('cr.end >= ? OR end IS NULL', date('Y-m-d'))
                     ->where('cr.status = ?', 'active');
        $registries = $db->fetchAll($select);

        foreach ($responsibles as $responsible) {
            $data[] = $responsible;
            $session->classrooms[] = $responsible['classroom_id'];
        }

        foreach ($registries as $registry) {
            if (in_array($registry, $data)) {
                continue;
            }
            $data[] = $registry;
            $session->classrooms[] = $registry['classroom_id'];
        }
        
        return $data;
    }

    /**
     * Get all possible classroom
     *
     * @param int $userId
     * @return array
     */
    public static function getFinalizedByUser($userId)
    {
        $certificate = new Tri_Db_Table('certificate');
        $select = $certificate->select(true)->setIntegrityCheck(false)
                              ->join('classroom', 'classroom.id = certificate.classroom_id', array())
                              ->join('course', 'course.id = classroom.course_id')
                              ->where('certificate.user_id = ?', $userId);

        return $certificate->fetchAll($select);
    }
	
	/**
	 * Verify if class it's available
	 *
	 * @param int $id 
	 * @return boolean
	 */
    public static function isAvailable($id)
    {
        $classroom     = new Tri_Db_Table('classroom');
        $classroomUser = new Tri_Db_Table('classroom_user');

        $row = $classroom->fetchRow(array('id = ?' => $id));

        if (!$row) {
            return false;
        }

        $select = $classroomUser->select(true)
                                ->columns(array('COUNT(0) as total'))
                                ->where('classroom_id = ?', $id);

        $total = $classroom->fetchRow($select)->total;

        if ($row->max_student > 0 && $row->max_student <= $total) {
            return false;
        }

        return true;
    }

	/**
	 * Get all class it's available
	 *
	 * @param int $id
	 * @return object select
	 */
    public static function getAvailable($id)
    {
        $classroom  = new Tri_Db_Table('classroom');
		$selectionProcessClassroom = new Tri_Db_Table('selection_process_classroom');
		$selectIn = $selectionProcessClassroom->select()->setIntegrityCheck(false)
									 		  ->from(array('p' => 'selection_process_classroom'), array('p.classroom_id'))
		 							 		  ->where('selection_process_id = ?', $id);
        $select = $classroom->select(true)
                            ->where('status = ?', 'active')
							->where('id not in (?)', $selectIn);
        return $select;
    }
}
