<?php
class Panel_IndexController extends Tri_Controller_Action
{
    public function init()
    {
        parent::init();
        $this->view->title = "Panel";
    }
    
    public function indexAction()
    {
        $classroomUser = new Tri_Db_Table('classroom_user');
        $panel         = new Tri_Db_Table('panel');
        $session       = new Zend_Session_Namespace('data');
        
        $select = $classroomUser->select(true)
                                ->setIntegrityCheck(false)
                                ->join('user', 'classroom_user.user_id = user.id')
                                ->where('classroom_user.classroom_id = ?', $session->classroom_id)
                                ->where('user.role = ?', 'student')
                                ->order('name');
        
        if (Zend_Auth::getInstance()->getIdentity()->role == 'student') {
            $select->where('user.id = ?', Zend_Auth::getInstance()->getIdentity()->id);
        }

        $this->view->data  = $classroomUser->fetchAll($select);
        $this->view->panel = $panel->fetchAll(array('classroom_id = ?' => $session->classroom_id));
    }

    public function formAction()
    {
        $session = new Zend_Session_Namespace('data');
        $table = new Tri_Db_Table('panel');
        $where = array('classroom_id = ?' => $session->classroom_id);

        $this->view->data = $table->fetchAll($where);
    }

    public function findAction()
    {
        $session = new Zend_Session_Namespace('data');
        $type = $this->_getParam('type');
        $where = array('classroom_id = ?' => $session->classroom_id);

        switch ($type) {
            case 'activity':
                $table = new Tri_Db_Table('activity');
                $data = $table->fetchPairs('id', 'title', $where);
                break;
            case 'forum':
                $table = new Tri_Db_Table('forum');
                $data = $table->fetchPairs('id', 'title', $where);
                break;
            case 'exercise':
                $table = new Tri_Db_Table('exercise');
                $data = $table->fetchPairs('id', 'name', $where);
                break;
        }

        $this->view->data = $data;
    }

    public function saveAction()
    {
        $session = new Zend_Session_Namespace('data');
        $table   = new Tri_Db_Table('panel');

        $data = $this->_getAllParams();
        $data['classroom_id'] = $session->classroom_id;

        $table->createRow($data)->save();
        $this->_helper->_flashMessenger->addMessage('Success');

        $this->_redirect('panel/index/form');
    }

    public function deleteAction()
    {
        $table = new Tri_Db_Table('panel');
        $note  = new Tri_Db_Table('panel_note');
        $id    = Zend_Filter::filterStatic($this->_getParam('id'), 'int');

        if ($id) {
            $note->delete(array('panel_id = ?' => $id));
            $table->delete(array('id = ?' => $id));
            $this->_helper->_flashMessenger->addMessage('Success');
        }

        $this->_redirect('panel/index/form');
    }

    public function saveNoteAction()
    {
        $data      = $this->_getAllParams();
        $panelNote = new Tri_Db_Table('panel_note');

        $panelNote->delete(array('panel_id = ?' => $data['panel_id'],
                                 'user_id = ?' => $data['user_id']));

        $panelNote->createRow($data)->save();

        exit('ok');
    }
}