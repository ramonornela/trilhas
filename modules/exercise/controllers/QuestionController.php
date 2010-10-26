<?php
class Exercise_QuestionController extends Tri_Controller_Action
{
    public function init()
    {
        parent::init();
        $this->view->title = "Question";
    }

    public function indexAction()
    {
        $identity = Zend_Auth::getInstance()->getIdentity();
        $table    = new Tri_Db_Table('exercise_question');
        $session  = new Zend_Session_Namespace('data');

        $select = $table->select(true)->setIntegrityCheck(false)
                        ->joinLeft('exercise', 'exercise.id = exercise_question.exercise_id', array())
                        ->where('exercise_question.status = ?', 'active')
                        ->where('(exercise.id = exercise_question.exercise_id AND classroom_id = ?)
                                  OR exercise_question.exercise_id IS NULL', $session->classroom_id);

        $this->view->data = $table->fetchAll($select, "id DESC");
    }

    public function formAction()
    {
        $id   = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $form = new Exercise_Form_Question();

        $form->addMultipleText($id);
        if ($id) {
            $table = new Tri_Db_Table('exercise_question');
            $row   = $table->find($id)->current();

            if ($row) {
                $form->populate($row->toArray());
            }
        }

        $this->view->form = $form;
    }

    public function saveAction()
    {
        $form  = new Exercise_Form_Question();
        $table = new Tri_Db_Table('exercise_question');
        $session = new Zend_Session_Namespace('data');
        $data  = $this->_getAllParams();

        if ($form->isValid($data)) {
            $data = $form->getValues();

            if (isset($data['id']) && $data['id']) {
                $row = $table->find($data['id'])->current();
                $row->setFromArray($data);
                $id = $row->save();
            } else {
                unset($data['id']);
                $row = $table->createRow($data);
                $id = $row->save();
            }

            $this->_helper->_flashMessenger->addMessage('Success');
            $this->_redirect('exercise/question/form/id/'.$id);
        }

        $this->view->messages = array('Error');
        $this->getResponse()->prepend('messages', $this->view->render('message.phtml'));
        
        $this->view->form = $form;
        $this->render('form');
    }
}