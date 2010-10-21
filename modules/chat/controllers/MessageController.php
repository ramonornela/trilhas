<?php
class Chat_MessageController extends Tri_Controller_Action
{
	public function init()
    {
        parent::init();
        $this->view->title = "Message";
    }

    public function indexAction()
    {
        $id      = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $userId  = Zend_Filter::filterStatic($this->_getParam('userId'), 'int');
        $page    = Zend_Filter::filterStatic($this->_getParam('page'), 'int');
        $session = new Zend_Session_Namespace('data');
        $table   = new Tri_Db_Table('message');
        $form    = new Chat_Form_Message();
        $classroomUser = new Tri_Db_Table('classroom_user');
        $select  = $table->select(true)
                        ->setIntegrityCheck(false)
                        ->join('user', 'message.sender = user.id', array('name','image'))
                        ->order('id DESC');

        if (!$userId) {
            $userId = Zend_Auth::getInstance()->getIdentity()->id;
        }
        
        $form->populate(array('receiver' => $userId));
        $select->where('receiver = ?', $userId);

        if ($id) {
            $table = new Tri_Db_Table('message');
            $row   = $table->find($id)->current();

            if ($row) {
                $form->populate($row->toArray());
            }
        }


        $selectUser = $classroomUser->select(true)
                                    ->setIntegrityCheck(false)
                                    ->join('user', 'classroom_user.user_id = user.id')
                                    ->where('classroom_user.classroom_id = ?', $session->classroom_id)
                                    ->order('name');
        $this->view->users = $classroomUser->fetchAll($selectUser);

        $paginator = new Tri_Paginator($select, $page);
        $this->view->data = $paginator->getResult();
        $this->view->form = $form;
        $this->view->userId = $userId;
    }

    public function saveAction()
    {
        $form  = new Chat_Form_Message();
        $table = new Tri_Db_Table('message');
        $session = new Zend_Session_Namespace('data');
        $data  = $this->_getAllParams();

        if ($form->isValid($data)) {
            $data = $form->getValues();
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
            $this->_redirect('chat/message/index/userId/'.$data['receiver']);
        }

        $this->view->messages = array('Error');
        $this->view->form = $form;
        $this->render('index');
    }

    public function deleteAction()
    {
        $table = new Tri_Db_Table('message');
        $id    = Zend_Filter::filterStatic($this->_getParam('id'), 'int');

        if ($id) {
            $table->delete(array('id = ?' => $id));
            $this->_helper->_flashMessenger->addMessage('Success');
        }

        $this->_redirect('chat/message/index/');
    }
}