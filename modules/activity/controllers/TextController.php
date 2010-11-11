<?php
class Activity_TextController extends Tri_Controller_Action
{
    public function init()
    {
        parent::init();
        $this->view->title = "Activity";
    }

    public function indexAction()
    {
        $table    = new Tri_Db_Table('activity_text');
        $activity = new Tri_Db_Table('activity');
        $id       = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $textId   = Zend_Filter::filterStatic($this->_getParam('textId'), 'int');
        $authId   = Zend_Auth::getInstance()->getIdentity()->id;
        $userId   = Zend_Filter::filterStatic($this->_getParam('userId', $authId), 'int');
        $form     = new Activity_Form_Text();

        if (!$id) {
            $this->_redirect('activity');
        }

        $select = $table->select(true)
                        ->setIntegrityCheck(false)
                        ->join('user', 'user.id = sender', array('user.id as uid','user.name','user.image','user.role'))
                        ->where('activity_id = ?', $id)
                        ->order('id DESC')
                        ->limit(6);

        if (Zend_Auth::getInstance()->getIdentity()->role == 'student') {
            $select->where('user_id = ?', $authId);
        } else {
            $select->where('(user_id = ?', $authId)
                   ->orWhere('user_id = ?)', $userId);
        }

        $data = $table->fetchAll($select);

        $populate = array('activity_id' => $id, 'user_id' => $userId);

        $current = null;
        if (count($data) && !$textId) {
            $current = current(current($data));
            $populate['description'] = $current['description'];
        } elseif ($textId) {
            $current = $table->find($textId)->current();
            $populate['description'] = $current->description;
        }

        if (Zend_Auth::getInstance()->getIdentity()->role == 'student' && !$this->_hasParam('nav')) {
            if ($current && ($current['status'] == 'final' || $current['status'] == 'close')) {
                $this->_redirect('activity/text/view/status/'.$current['status']);
            }
        }

        $form->populate($populate);
        
        $this->view->data   = $data;
        $this->view->parent = $activity->find($id)->current();
        $this->view->form   = $form;
        $this->view->id     = $id;
        $this->view->userId = $userId;
    }

    public function viewAction()
    {
        $this->view->status = $this->_getParam('status');
    }

    public function saveAction()
    {
        $form  = new Activity_Form_Text();
        $table = new Tri_Db_Table('activity_text');
        $session = new Zend_Session_Namespace('data');
        $data  = $this->_getAllParams();

        $statusList = array('openButton' => 'open', 'finalize' => 'final');
        if (Zend_Auth::getInstance()->getIdentity()->role == 'student') {
            $statusList['sendCorrection'] = 'close';
            $statusList['saveDraft'] = 'open';
        }

        if ($form->isValid($data)) {
            $data = $form->getValues();
            
            $data['sender']  = Zend_Auth::getInstance()->getIdentity()->id;
            $data['status']  = $statusList[$data['status']];

            $row = $table->createRow($data);
            $id = $row->save();

            if (isset($data['note']) && $data['note']) {
                Panel_Model_Panel::addNote($row->user_id, 'activity', $data['activity_id'], $data['note']);
            }

            $this->_helper->_flashMessenger->addMessage('Success');
            $this->_redirect('activity/text/index/id/'.$data['activity_id'].'/textId/'.$id.'/userId/'.$data['user_id']);
        }

        $activity = new Tri_Db_Table('activity');
        $this->view->parent = $activity->find($data['activity_id'])->current();
        $this->view->messages = array('Error');
        $this->view->form = $form;
        $this->render('index');
    }
}