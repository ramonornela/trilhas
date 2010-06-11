<?php
class Calendar_IndexController extends Tri_Controller_Action {
    public function indexAction() {
        $calendar = new Zend_Db_Table('calendar');
        $id       = Zend_Filter::filterStatic($this->_getParam('id'), 'int');

        $where = array('classroom_id IS NULL');

        if ($id) {
            $where = array('classroom_id = ?' => $id);
        }

        $where['end IS NULL OR end > ?'] = date('Y-m-d');
        
        $this->view->calendar = $calendar->fetchAll($where, 'begin');
    }

    public function formAction() {
        $id   = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $form = new Calendar_Form_Form();
        
        if ($id) {
            $calendar = new Zend_Db_Table('calendar');
            $row      = $calendar->find($id)->current();

            if ($row) {
                $form->populate($row->toArray());
            }
        }
        
        $this->view->form = $form;
    }

    public function saveAction() {
        $form  = new Calendar_Form_Form();
        $table = new Zend_Db_Table('calendar');
        $data  = $this->_getAllParams();

        if ($form->isValid($data)) {
            $data = $form->getValues();
            $data['user_id'] = 1;

            if (isset($data['id']) && $data['id']) {
                $row = $table->find($data['id'])->current();
                $row->setFromArray($data);
            } else {
                unset($data['id']);
                $row = $table->createRow($data);
            }

            $id = $row->save();

            $this->_helper->_flashMessenger->addMessage('Success');
            $this->_redirect('calendar/index/form/id/'.$id);
        }

        $this->_helper->_flashMessenger->addMessage('Error');
        $this->view->form = $form;
        $this->render('form');
    }

    public function deleteAction() {
        $calendar = new Zend_Db_Table('calendar');
        $id       = Zend_Filter::filterStatic($this->_getParam('id'), 'int');

        if (!$id) {
            $this->_redirect('calendar/index/');
        }

        $calendar->delete(array('id = ?' => $id));

        $this->_helper->_flashMessenger->addMessage('Success');
        $this->_redirect('calendar/index/');
    }
}