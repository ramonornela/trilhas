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
 * @category   Page
 * @package    Page_Form_Page
 * @copyright  Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 * @license    http://www.gnu.org/licenses/  GNU GPL
 */
class Page_Form_Page extends Zend_Form
{
    /**
     * (non-PHPdoc)
     * @see Zend_Form#init()
     */
    public function init()
    {
        $table      = new Tri_Db_Table('page');
        $validators = $table->getValidators();
        $filters    = $table->getFilters();

        $this->setAction('page/index/save')
             ->setMethod('post');

        $id = new Zend_Form_Element_Hidden('id');
        $id->addValidators($validators['id'])
           ->addFilters($filters['id'])
           ->removeDecorator('Label')
           ->removeDecorator('HtmlTag');

        $filters['title'][] = 'StripTags';
        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('Title')
             ->addValidators($validators['title'])
             ->addFilters($filters['title']);

        $position = new Zend_Form_Element_Text('position');
        $position->setLabel('Position')
             ->addValidators($validators['position'])
             ->addFilters($filters['position']);

        $description = new Zend_Form_Element_Textarea('description');
        $description->setLabel('Description')
                    ->addValidators($validators['description'])
                    ->addFilters($filters['description'])
                    ->setAttrib('rows', 15)
                    ->setAttrib('id', 'page-description-text')
                    ->setAllowEmpty(false);

        $status = new Zend_Form_Element_Select('status');
        $status->addMultiOptions(array('active' => 'active', 'inactive' => 'inactive'))
               ->setRegisterInArrayValidator(false);
        
        $this->addElement($id)
             ->addElement($title)
             ->addElement($description)
             ->addElement($position)
             ->addElement($status)
             ->addElement('submit', 'Save');
   }
}
