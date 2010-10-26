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
class Exercise_Model_Question
{
    /**
     * Associate question to exercise
     * If no previous associate duplicate and associate
     *
     * @param integer $exerciseId
     * @param array $questionIds
     */
    public static function associate($exerciseId, $questionIds)
    {
        if (count($questionIds)) {
            $question = new Tri_Db_Table('exercise_question');
            foreach ($questionIds as $position => $questionId) {
                $row = $question->fetchRow(array('id = ?' => $questionId));
                if ($row) {
                    $row->position = $position;
                    if ($row->exercise_id != $exerciseId) {
                        $data = $row->toArray();
                        unset($data['id']);
                        $data['exercise_id'] = $exerciseId;
                        $question->createRow($data)->save();
                    } else {
                        $row->save();
                    }
                }
            }
        }
    }

    /**
     * Remove question from exercise
     *
     * @param array $questionIds
     */
    public static function remove($questionIds)
    {
        if (count($questionIds)) {
            $question = new Tri_Db_Table('exercise_question');
            foreach ($questionIds as $questionId) {
                $row = $question->fetchRow(array('id = ?' => $questionId));
                $row->exercise_id = null;
                $row->save();
            }
        }
    }
}
?>
