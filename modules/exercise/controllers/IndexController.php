<?php
class Exercise_IndexController extends Tri_Controller_Action
{
    public function init()
    {
        parent::init();
        $this->view->title = "Evaluation";
    }

    public function indexAction()
    {
        $identity = Zend_Auth::getInstance()->getIdentity();
        $exercise = new Tri_Db_Table('exercise');
        $session = new Zend_Session_Namespace('data');
        $where = array('classroom_id = ?' => $session->classroom_id);

        if ($identity->role == 'student') {
            $where['begin  <= ?'] = date('Y-m-d');
            $where['end >= ? OR end IS NULL'] = date('Y-m-d');
            $where['status IN(?)'] = array('active','final');
        }
        
        $this->view->data = $exercise->fetchAll($where , array("name", "id DESC"));
    }

    public function formAction()
    {
        $id   = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $form = new Exercise_Form_Exercise();

        if ($id) {
            $table = new Tri_Db_Table('exercise');
            $row   = $table->find($id)->current();

            if ($row) {
                $form->populate($row->toArray());
                $question = new Tri_Db_Table('exercise_question');
                $where    = array('exercise_id = ?' => $id);
                $this->view->questions = $question->fetchAll($where, 'position');
                $this->view->id = $id;
            }
        }

        $this->view->form = $form;
    }

    public function saveAction()
    {
        $form  = new Exercise_Form_Exercise();
        $table = new Tri_Db_Table('exercise');
        $session = new Zend_Session_Namespace('data');
        $data  = $this->_getAllParams();
        $questionIds = $data['question_id'];
        $removeIds = $data['remove_id'];
        
        if ($form->isValid($data)) {
            $data = $form->getValues();
            $data['user_id']      = Zend_Auth::getInstance()->getIdentity()->id;
            $data['classroom_id'] = $session->classroom_id;

            if (!$data['end']) {
                unset($data['end']);
            }

            if (isset($data['id']) && $data['id']) {
                $row = $table->find($data['id'])->current();
                $row->setFromArray($data);
                $id = $row->save();

                Exercise_Model_Question::remove($removeIds);
                Exercise_Model_Question::associate($id, $questionIds);
            } else {
                unset($data['id']);
                $row = $table->createRow($data);
                $id = $row->save();
                Application_Model_Timeline::save('created a new exercise', $data['title']);
            }

            $this->_helper->_flashMessenger->addMessage('Success');
            $this->_redirect('exercise/index/form/id/'.$id);
        }

        $this->view->messages = array('Error');
        $this->getResponse()->prepend('messages', $this->view->render('message.phtml'));
        
        $this->view->form = $form;
        $this->render('form');
    }
}