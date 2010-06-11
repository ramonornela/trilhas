<?php
class IndexController extends Tri_Controller_Action {
    public function indexAction() {
        $course   = new Zend_Db_Table('course');
        $calendar = new Zend_Db_Table('calendar');

        $this->view->courses  = $course->fetchAll(array('status = ?' => 'Active'),
                                                  array('name', 'category'));
        $this->view->calendar = $calendar->fetchAll(array('classroom_id IS NULL',
                                                          'end IS NULL OR end > ?' => date('Y-m-d')), 'begin', 10);
    }
}
