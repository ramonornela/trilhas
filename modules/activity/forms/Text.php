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
 * @category   Activity
 * @package    Activity_Form_Text
 * @copyright  Copyright (C) 2005-2010  Preceptor Educação a Distância Ltda. <http://www.preceptoead.com.br>
 * @license    http://www.gnu.org/licenses/  GNU GPL
 */
class Activity_Form_Text extends Zend_Form
{
    /**
     * (non-PHPdoc)
     * @see Zend_Form#init()
     */
    public function init()
    {
        $table         = new Tri_Db_Table('activity_text');
        $validators    = $table->getValidators();
        $filters       = $table->getFilters();
        $translate     = Zend_Registry::get('Zend_Translate');
        $statusOptions = array('open' => $translate->_('open'),
                               'final' => $translate->_('final'));
        
        if (Zend_Auth::getInstance()->getIdentity()->role != 'student') {
            $statusOptions['close'] = $translate->_('close');
        }

        $this->setAction('activity/text/save')
             ->setMethod('post');

        $activityId = new Zend_Form_Element_Hidden('activity_id');
        $activityId->addValidators($validators['activity_id'])
                ->addFilters($filters['activity_id'])
                ->removeDecorator('Label')
                ->removeDecorator('HtmlTag');

        $filters['description'][] = 'StripTags';
        $description = new Zend_Form_Element_Textarea('description');
        $description->setLabel('Description')
                    ->addValidators($validators['description'])
                    ->addFilters($filters['description'])
                    ->setAttrib('rows', 20)
                    ->setAttrib('cols', '70%')
                    ->setAllowEmpty(false);

        $status = new Zend_Form_Element_Select('status');
        $status->addMultiOptions($statusOptions)
               ->setRegisterInArrayValidator(false)
               ->setLabel('Status')
               ->addValidators($validators['status'])
               ->addFilters($filters['status']);
               
        $this->addElement($activityId)
             ->addElement($description)
             ->addElement($status)
             ->addElement('submit', 'Save');
   }
}
