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
 * @category   Content
 * @package    Content_Form
 * @copyright  Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 * @license    http://www.gnu.org/licenses/  GNU GPL
 */
class Content_Form_File extends Zend_Form
{
    /**
     * (non-PHPdoc)
     * @see Zend_Form#init()
     */
    public function init()
    {
        $table = new Tri_Db_Table('file');

        $validators    = $table->getValidators();
        $filters       = $table->getFilters();

        $this->setAction('content/file/save')
             ->setMethod('post')
             ->setAttrib('enctype', 'multipart/form-data');

        $filters['name'][] = 'StripTags';
        $name = new Zend_Form_Element_Text('name');
        $name->setLabel('Name')
             ->addValidators($validators['name'])
             ->addFilters($filters['name']);

        $file = new Zend_Form_Element_File('location');
        $file->setLabel('File')
             ->setDestination(APPLICATION_PATH . '/../data/upload/')
             ->setMaxFileSize(10485760)//10mb
             ->setValueDisabled(true)
             ->addFilter('Rename', uniqid())
             ->addValidator('Count', false, 1)
             ->addValidator('Size', false, 10485760)//10mb
             ->addValidator('Extension', false, 'doc,docx,pdf,ppt,pps,txt,odt,ods,jpg,png,gif,xls,xlsx,swf');

        $this->addElement($name)
             ->addElement($file)
             ->addElement('submit', 'Save');
   }
}
