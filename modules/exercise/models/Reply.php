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
 * @category   Exercise
 * @package    Exercise_Model
 * @copyright  Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 * @license    http://www.gnu.org/licenses/  GNU GPL
 */
class Exercise_Model_Reply
{
    /**
     * Checks if the user still has attempted and period
     *
     * @param integer $userId
     * @param integer $exerciseId
     */
    public static function isDisabled($userId, $exerciseId)
    {
        $exercise = new Zend_Db_Table('exercise');
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                     ->from('exercise_note', 'COUNT(0) as total')
                     ->where('user_id = ?', $userId)
                     ->where('exercise_id = ?', $exerciseId);
        $result = $db->fetchRow($select);

        $where = array('id = ?' => $exerciseId,
                       'begin  <= ?' => date('Y-m-d'),
                       'end >= ? OR end IS NULL' => date('Y-m-d'));
        $row = $exercise->fetchRow($where);

        if ($row) {
            if ($row->attempts === "0"
                || ((int) $result['total'] < (int) $row->attempts)) {
                return false;
            } else {
                return 'Number of attempts exec';
            }
        } else {
            return 'Expired period';
        }

        return false;
    }

    public static  function isFinal($exerciseId)
    {

    }

    public static function sumNote($noteId)
    {
        $db = Zend_Db_Table::getDefaultAdapter();
        $select = $db->select()
                     ->from(array('a' => 'exercise_answer'), 'SUM(note) as total')
                     ->join(array('o' => 'exercise_option'), 'a.`exercise_option_id` = o.id')
                     ->join(array('q' => 'exercise_question'), 'q.id = o.exercise_question_id')
                     ->where('o.status = ?', 'right')
                     ->where('exercise_note_id = ?', $noteId);
        $row = $db->fetchRow($select);
        return (int) $row['total'];
    }
}