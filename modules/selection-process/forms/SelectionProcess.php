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
 * @package    SelectionProcess_Form
 * @copyright  Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 * @license    http://www.gnu.org/licenses/  GNU GPL
 */
class SelectionProcess_Form_SelectionProcess extends Zend_Form
{
    /**
     * (non-PHPdoc)
     * @see Zend_Form#init()
     */
    public function init()
    {
        $table = new Tri_Db_Table('selection_process');

		$this->addElementPrefixPath('Tri_Filter', 'Tri/Filter', 'FILTER');

        $validators    = $table->getValidators();
        $filters       = $table->getFilters();

        $this->setAction('selection-process/index/save')
             ->setMethod('post');
		
		$id = new Zend_Form_Element_Hidden('id');
        $id->addValidators($validators['id'])
           ->addFilters($filters['id'])
           ->removeDecorator('Label')
           ->removeDecorator('HtmlTag');

		$user_id = new Zend_Form_Element_Hidden('user_id');
        $user_id->addValidators($validators['user_id'])
           	    ->addFilters($filters['user_id'])
                ->removeDecorator('Label')
                ->removeDecorator('HtmlTag');
		
        $filters['name'][] = 'StripTags';
        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('Name')
             ->addValidators($validators['name'])
             ->addFilters($filters['name']);

		$begin = new Zend_Form_Element_Text('begin');
        $begin->setLabel('Begin')
              ->setAttrib('class', 'date')
              ->addFilters($filters['begin'])
              ->addValidators($validators['begin'])
              ->setAllowEmpty(false);

        $end = new Zend_Form_Element_Text('end');
        $end->setLabel('End')
            ->setAttrib('class', 'date')
            ->addFilters($filters['end']);

        $this->addElement($id)
			 ->addElement($user_id)
			 ->addElement($name)
             ->addElement($begin)
			 ->addElement($end)
             ->addElement('submit', 'Save');
   }
}
