<?php
class Forum_ReplyController extends Tri_Controller_Action
{
    public function init()
    {
        parent::init();
        $this->view->title = "Forum";
    }

    public function indexAction()
    {
        $session  = new Zend_Session_Namespace('data');
        $table    = new Tri_Db_Table('forum_reply');
        $forum    = new Tri_Db_Table('forum');
        $page     = Zend_Filter::filterStatic($this->_getParam('page', 1), 'int');
        $id       = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $form     = new Forum_Form_Reply();

        $select = $table->select(true)
                        ->setIntegrityCheck(false)
                        ->join('user', 'user.id = user_id', array('user.id as uid','user.name','user.image'))
                        ->where('forum_id = ?', $id);

        $form->populate(array('forum_id' => $id));

        $paginator = new Tri_Paginator($select, $page);
        $this->view->data   = $paginator->getResult();
        $this->view->parent = $forum->find($id)->current();
        $this->view->form   = $form;
        $this->view->page   = $page;
    }

    public function formAction()
    {
        $id      = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $forumId = Zend_Filter::filterStatic($this->_getParam('forumId'), 'int');
        $form    = new Forum_Form_Reply();

        $form->populate(array('forum_id' => $forumId));

        if ($id) {
            $table = new Tri_Db_Table('forum');
            $row   = $table->find($id)->current();

            if ($row) {
                $form->populate($row->toArray());
            }
        }

        $this->view->form = $form;
    }

    public function saveAction()
    {
        $form  = new Forum_Form_Reply();
        $table = new Tri_Db_Table('forum_reply');
        $session = new Zend_Session_Namespace('data');
        $data  = $this->_getAllParams();

        if ($form->isValid($data)) {
            $data = $form->getValues();
            $data['user_id'] = Zend_Auth::getInstance()->getIdentity()->id;

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
            $this->_redirect('forum/reply/index/id/'.$data['forum_id']);
        }

        $this->_helper->_flashMessenger->addMessage('Error');
        $this->view->form = $form;
        $this->render('form');
    }

    public function deleteAction()
    {
        $table   = new Tri_Db_Table('forum_reply');
        $id      = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $forumId = Zend_Filter::filterStatic($this->_getParam('forumId'), 'int');

        if ($id) {
            $table->delete(array('id = ?' => $id));
            $this->_helper->_flashMessenger->addMessage('Success');
        }

        $this->_redirect('forum/reply/index/id/'.$forumId);
    }
}