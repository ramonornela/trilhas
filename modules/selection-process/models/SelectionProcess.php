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
 * @category   SelectionProcess
 * @package    SelectionProcess_Model
 * @copyright  Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 * @license    http://www.gnu.org/licenses/  GNU GPL
 */
class SelectionProcess_Model_SelectionProcess
{
   /**
    * Status type: waiting
    */
	const WAITING = 'waiting';
	
   /**
    * Status type: accepts
    */
	const ACCEPTS = 'accepts';
	
   /**
    * Status type: rejected
    */	
	const REJECTED = 'rejected';
	
	/**
	 * Get all selection process it's available by class
	 *
	 * @param int $id
	 * @return object select
	 */
    public static function getAvailableProcessByCourse($course_id)
    {
		$user_id = Zend_Auth::getInstance()->getIdentity()->id;
		
		$selectionProcess = new Tri_Db_Table('selection_process');
		$select = $selectionProcess->select()->setIntegrityCheck(false)
								   ->from(array('p' => 'selection_process'), array('pname' => 'name', 'pid' => 'id'))
								   ->join(array('pc' => 'selection_process_classroom'), 'p.id = pc.selection_process_id', array())
								   ->join(array('c' => 'classroom'), "pc.classroom_id = c.id", array('id', 'course_id', 'name', 'begin', 'end'))
				  	 			   ->join(array('co' => 'course'), "c.course_id = co.id", array())
								   ->where('p.begin <= ?', date('Y-m-d'))
								   ->where('p.end >= ?', date('Y-m-d'))
								   ->where('co.id =?', $course_id);
        return $selectionProcess->fetchAll($select);
    }

	/**
	 * Get all class it's available to selection process
	 *
	 * @param int $id
	 * @return object select
	 */
    public static function getAvailableClass($id)
    {
		$selectionProcessClassroom = new Tri_Db_Table('selection_process_classroom');
		$select = $selectionProcessClassroom->select()->setIntegrityCheck(false)
									 		->from(array('p' => 'selection_process_classroom'), array())
											->join(array('c' => 'classroom'), "p.classroom_id = c.id", array('id', 'name', 'begin', 'end'))
		 							 	    ->where('p.selection_process_id = ?', $id);
        return $selectionProcessClassroom->fetchAll($select);
    }

	/**
	 * Verifies that the student has made the pre-registration
	 *
	 * @param int $user_id
	 * @param int $selection_process_id
	 * @return boolean
	 */
    public static function verifyUserPermission($user_id, $selection_process_id)
    {
		$selectionProcessUser = new Tri_Db_Table('selection_process_user');
		$select = $selectionProcessUser->select()->setIntegrityCheck(false)
								 	   ->from(array('p' => 'selection_process_user'), array('*'))
									   ->where('user_id = ?', $user_id)
									   ->where('selection_process_id = ?', $selection_process_id);
		$result = $selectionProcessUser->fetchAll($select);
		if ($result->count()) {
			return false;
		}
		return true;
    }

	/**
	 * Get all the students interested in courses of the selection process
	 *
	 * @param int $id
	 * @param int $course_id
	 * @return object select
	 */
    public static function listPreRegistration($id = null, $course_id = null)
    {
		$selectionProcessUser = new Tri_Db_Table('selection_process_user');
		$select = $selectionProcessUser->select()->setIntegrityCheck(false)
									   ->from(array('pu' => 'selection_process_user'), array('id' => 'selection_process_id', 'date', 'justify', 'status'))
									   ->join(array('c' => 'classroom'), 'pu.classroom_id = c.id', array('cid' => 'id', 'cname' => 'name'))
									   ->join(array('co' => 'course'), 'c.course_id = co.id', array('coid' => 'id', 'coname' => 'name'))
									   ->join(array('u' => 'user'), 'u.id = pu.user_id', array('uid' => 'id', 'uname' => 'name', 'image'))
									   ->order(array('c.name', 'pu.date', 'u.name'));
		if (!empty($id)) {
			$select->where('pu.selection_process_id = ?', $id);
		}							
		if (!empty($course_id)) {
			$select->where('c.course_id = ?', $course_id);
		}
		
        return $select;
    }
	
	/**
	 * Get all courses it's available to selection process
	 *
	 * @param int $selection_process_id
	 * @return array
	 */
	public static function getCourses($selection_process_id)
	{
		$selectionProcessClassroom = new Tri_Db_Table('selection_process_classroom');
		$select = $selectionProcessClassroom->select()->setIntegrityCheck(false)
									 		->from(array('p' => 'selection_process_classroom'), array())
											->join(array('c' => 'classroom'), "p.classroom_id = c.id", array())
											->join(array('co' => 'course'), 'c.course_id = co.id', array('id', 'name'))
		 							 	    ->where('p.selection_process_id = ?', $selection_process_id);
        return $selectionProcessClassroom->fetchAll($select);
	}
}
