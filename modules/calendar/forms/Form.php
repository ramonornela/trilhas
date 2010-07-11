<?php
class Calendar_Form_Form extends Zend_Form
{
    public function init()
    {
        $calendar  = new Tri_Db_Table('calendar');
        $classroom = new Tri_Db_Table('classroom');

        $validators = $calendar->getValidators();
        $filters    = $calendar->getFilters();
        $options    = $classroom->fetchPairs('id', 'name', "status = 'Active'");

        $this->setAction('calendar/index/save')
             ->setMethod('post');

        $id = new Zend_Form_Element_Hidden('id');
        $id->addValidators($validators['id'])
           ->addFilters($filters['id'])
           ->removeDecorator('Label')
           ->removeDecorator('HtmlTag');

        $classroom_id = new Zend_Form_Element_Select('classroom_id');
        $classroom_id->setLabel('Classroom')
                     ->addValidators($validators['classroom_id'])
                     ->addFilters($filters['classroom_id'])
                     ->addMultiOptions(array('' => '[select]') + $options);

        $description = new Zend_Form_Element_Textarea('description');
        $description->setLabel('Description')
                    ->addValidators($validators['description'])
                    ->addFilters($filters['description'])
                    ->setAllowEmpty(false)
                    ->setAttrib('rows', 7);

        $begin = new Zend_Form_Element_Text('begin');
        $begin->setLabel('Begin')
              ->setAttrib('class', 'date')
              ->addFilters($filters['begin'])
              ->addValidators($validators['begin'])
              ->setAllowEmpty(false)
              ->getPluginLoader('filter')->addPrefixPath('Tri_Filter', 'Tri/Filter');

        $end = new Zend_Form_Element_Text('end');
        $end->setLabel('End')
            ->setAttrib('class', 'date')
            ->addFilters($filters['end'])
            ->getPluginLoader('filter')->addPrefixPath('Tri_Filter', 'Tri/Filter');

        $this->addElement($id);
        $this->addElement($classroom_id);
        $this->addElement($description);
        $this->addElement($begin);
        $this->addElement($end);
        $this->addElement('submit', 'Save');
    }
}
