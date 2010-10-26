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
        $form     = new Activity_Form_Text();

        if (!$id) {
            $this->_redirect('activity');
        }

        $select = $table->select(true)
                        ->setIntegrityCheck(false)
                        ->join('user', 'user.id = sender', array('user.id as uid','user.name','user.image','user.role'))
                        ->where('activity_id = ?', $id)
                        ->where('user_id = ?', Zend_Auth::getInstance()->getIdentity()->id)
                        ->order('id DESC')
                        ->limit(6);

        $data = $table->fetchAll($select);

        $populate = array('activity_id' => $id);

        if (count($data) && !$textId) {
            $current = current(current($data));
            if ($current['status'] == 'final' || $current['status'] == 'close' ) {
                $this->_redirect('activity/text/view/status/'.$current['status']);
            }
            $populate['description'] = $current['description'];
        } elseif ($textId) {
            $current = $table->find($textId)->current();
            $populate['description'] = $current->description;
        }

        $form->populate($populate);
        
        $this->view->data   = $data;
        $this->view->parent = $activity->find($id)->current();
        $this->view->form   = $form;
        $this->view->page   = $page;
        $this->view->id     = $id;
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

        if ($form->isValid($data)) {
            $data = $form->getValues();
            $data['user_id'] = Zend_Auth::getInstance()->getIdentity()->id;
            $data['sender'] = Zend_Auth::getInstance()->getIdentity()->id;

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
            $this->_redirect('activity/text/index/id/'.$data['activity_id']);
        }

        $this->view->messages = array('Error');
        $this->view->form = $form;
        $this->render('form');
    }
}