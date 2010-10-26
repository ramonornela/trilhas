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
 * @category   Faq
 * @package    Faq_Form_Faq
 * @copyright  Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 * @license    http://www.gnu.org/licenses/  GNU GPL
 */
class Faq_Form_Faq extends Zend_Form
{
    /**
     * (non-PHPdoc)
     * @see Zend_Form#init()
     */
    public function init()
    {
        $table      = new Tri_Db_Table('faq');
        $validators = $table->getValidators();
        $filters    = $table->getFilters();

        $this->setAction('faq/index/save')
             ->setMethod('post');

        $id = new Zend_Form_Element_Hidden('id');
        $id->addValidators($validators['id'])
           ->addFilters($filters['id'])
           ->removeDecorator('Label')
           ->removeDecorator('HtmlTag');

        $filters['question'][] = 'StripTags';
        $question = new Zend_Form_Element_Textarea('question');
        $question->setLabel('Question')
                 ->addValidators($validators['question'])
                 ->addFilters($filters['question'])
                 ->setAttrib('rows', 10)
                 ->setAllowEmpty(false);

        $filters['answer'][] = 'StripTags';
        $answer = new Zend_Form_Element_Textarea('answer');
        $answer->setLabel('Answer')
               ->addValidators($validators['answer'])
               ->addFilters($filters['answer'])
               ->setAttrib('rows', 15)
               ->setAllowEmpty(false);

        $this->addElement($id)
             ->addElement($question)
             ->addElement($answer)
             ->addElement('submit', 'Save');
   }
}
