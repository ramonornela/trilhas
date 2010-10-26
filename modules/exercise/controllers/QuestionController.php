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
        $page     = Zend_Filter::filterStatic($this->_getParam('page'), 'int');
        $id       = Zend_Filter::filterStatic($this->_getParam('id'), 'int');

        $select = $table->select(true)->setIntegrityCheck(false)
                        ->joinLeft('exercise', 'exercise.id = exercise_question.exercise_id', array('name'))
                        ->where('exercise_question.status = ?', 'active')
                        ->where('(exercise.id = exercise_question.exercise_id AND classroom_id = ?)
                                  OR exercise_question.exercise_id IS NULL', $session->classroom_id)
                        ->order('id DESC');

        $session->exercise_id = null;
        
        if ($id) {
            $session->exercise_id = $id;
        }

        $paginator = new Tri_Paginator($select, $page);
        $this->view->data = $paginator->getResult();
    }

    public function formAction()
    {
        $id      = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $form    = new Exercise_Form_Question();
        $session = new Zend_Session_Namespace('data');

        $form->addMultipleText($id);
        if ($id) {
            $table = new Tri_Db_Table('exercise_question');
            $row   = $table->find($id)->current();

            if ($row) {
                $form->populate($row->toArray());
            }
            $this->view->update = $this->_getParam('update');
        }

        $this->view->form = $form;
        $this->view->exerciseId = $session->exercise_id;
    }

    public function saveAction()
    {
        $form    = new Exercise_Form_Question();
        $table   = new Tri_Db_Table('exercise_question');
        $option  = new Tri_Db_Table('exercise_option');
        $session = new Zend_Session_Namespace('data');
        $allData = $this->_getAllParams();

        if ($form->isValid($allData)) {
            $data = $form->getValues();

            if (isset($data['id']) && $data['id']) {
                $row = $table->find($data['id'])->current();
                $row->setFromArray($data);
                $id = $row->save();
            } else {
                unset($data['id']);
                if (isset($session->exercise_id) && $session->exercise_id) {
                    $data['exercise_id'] = $session->exercise_id;
                }
                $row = $table->createRow($data);
                $id = $row->save();
            }

            if (count($allData['option'])) {
                foreach ($allData['option'] as $key => $value) {
                    $status = "wrong";
                    if ($allData['right_option'] == $key) {
                        $status = "right";
                    }
                    
                    if (isset($allData['id_option'][$key]) && $allData['id_option'][$key] != 0) {
                        $row = $option->find($allData['id_option'][$key])->current();
                        $row->setFromArray(array('description' => $value,
                                                 'status' => $status));
                        $row->save();
                    } else {
                        $data = array('description' => $value,
                                      'exercise_question_id' => $id,
                                      'status' => $status);
                        $row = $option->createRow($data);
                        $row->save();
                    }
                }
            }

            $this->_helper->_flashMessenger->addMessage('Success');
            $this->_redirect('exercise/question/form/update/true/id/'.$id);
        }

        $this->view->messages = array('Error');
        $this->getResponse()->prepend('messages', $this->view->render('message.phtml'));
        
        $this->view->form = $form;
        $this->render('form');
    }
}