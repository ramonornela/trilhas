<?php
class CourseController extends Tri_Controller_Action
{
    public function indexAction()
    {
        $page  = Zend_Filter::filterStatic($this->_getParam('page'), 'int');
        $query = Zend_Filter::filterStatic($this->_getParam('query'), 'alnum');
        $course = new Zend_Db_Table('course');
        $where  = array();

        if ($query) {
            $where['name LIKE (?)'] = "%$query%";
        }

        $this->view->params = array('query' => $query,
                                    'page' => $page);

        $select    = $course->fetchAll($where, 'status');
        $paginator = new Tri_Paginator($select, $page);
        $this->view->data = $paginator->getResult();
    }

    public function viewAction()
    {
        $id = Zend_Filter::filterStatic($this->_getParam('id'), 'int');

        if ($id) {
            $course    = new Tri_Db_Table('course');
            $classroom = new Tri_Db_Table('classroom');

            $this->view->data = $course->find($id)->current();
            $where = array('course_id = ?' => $id, 'status = ?' => 'Active');
            $this->view->classroom = $classroom->fetchAll($where, 'begin');
        }
    }

    public function formAction()
    {
        $id   = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $form = new Application_Form_Course();

        if ($id) {
            $calendar = new Tri_Db_Table('course');
            $row      = $calendar->find($id)->current();

            if ($row) {
                $form->populate($row->toArray());
            }
        }

        $this->view->form = $form;
    }

    public function saveAction()
    {
        $form  = new Application_Form_Course();
        $table = new Tri_Db_Table('course');
        $data  = $this->_getAllParams();

        if ($form->isValid($data)) {
            if (!$form->image->receive()) {
                $this->_helper->_flashMessenger->addMessage('Image fail');
            }

            $data = $form->getValues();
            if (!$form->image->getValue()) {
                unset($data['image']);
            }

            $data['user_id'] = 1;

            if (isset($data['id']) && $data['id']) {
                $row = $table->find($data['id'])->current();
                $row->setFromArray($data);
                $id = $row->save();
            } else {
                unset($data['id']);
                $classroom = new Zend_Db_Table('classroom');
                $row = $table->createRow($data);
                $id = $row->save();

                $data = array('course_id'   => $id,
                              'responsible' => $data['responsible'],
                              'name'        => 'Open ' . $data['name'],
                              'begin'       => date('Y-m-d'));
                $row = $classroom->createRow($data);
                $row->save();
            }

            $this->_helper->_flashMessenger->addMessage('Success');
            $this->_redirect('course/form/id/'.$id);
        }

        $this->_helper->_flashMessenger->addMessage('Error');
        $this->view->form = $form;
        $this->render('form');
    }
}
