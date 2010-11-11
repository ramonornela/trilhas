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

        $this->setAction('activity/text/save')
             ->setMethod('post');

        $activityId = new Zend_Form_Element_Hidden('activity_id');
        $activityId->addValidators($validators['activity_id'])
                   ->addFilters($filters['activity_id'])
                   ->removeDecorator('Label')
                   ->removeDecorator('HtmlTag');

        $userId = new Zend_Form_Element_Hidden('user_id');
        $userId->addValidators($validators['user_id'])
               ->addFilters($filters['user_id'])
               ->removeDecorator('Label')
               ->removeDecorator('HtmlTag');

        $description = new Zend_Form_Element_Textarea('description');
        $description->setLabel('Description')
                    ->addValidators($validators['description'])
                    ->addFilters($filters['description'])
                    ->setAttrib('rows', 20)
                    ->setAttrib('cols', '70%')
                    ->setAttrib('id', 'text-description-text')
                    ->setAllowEmpty(false);

        $status = new Zend_Form_Element_Hidden('status');
        $status->removeDecorator('Label')
               ->removeDecorator('HtmlTag');

        $this->addElement($userId)
             ->addElement($activityId)
             ->addElement($status)
             ->addElement($description);
        
        if (Zend_Auth::getInstance()->getIdentity()->role == 'student') {
            $saveDraft      = new Zend_Form_Element_Submit('saveDraft');
            $sendCorrection = new Zend_Form_Element_Submit('sendCorrection');

            $saveDraft->removeDecorator('Label')
                      ->removeDecorator('DtDdWrapper');

            $sendCorrection->removeDecorator('Label')
                           ->removeDecorator('DtDdWrapper');

            $this->addElement($saveDraft)
                 ->addElement($sendCorrection);
        } else {
            $openButton = new Zend_Form_Element_Submit('openButton');
            $finalize   = new Zend_Form_Element_Submit('finalize');

            $openButton->removeDecorator('Label')
                       ->removeDecorator('DtDdWrapper');

            $finalize->removeDecorator('Label')
                     ->removeDecorator('DtDdWrapper');

            $note = new Zend_Form_Element_Text('note');
            $note->setLabel('Note')
                 ->addValidator('Int')
                 ->addFilter('Digits');

            $this->addElement($note)
                 ->addElement($openButton)
                 ->addElement($finalize);
        }
   }
}
