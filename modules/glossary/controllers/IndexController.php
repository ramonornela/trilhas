<?php
class Glossary_IndexController extends Tri_Controller_Action
{
    public function init()
    {
        parent::init();
        $this->view->title = "Glossary";
    }
    
    public function indexAction()
    {
        $session  = new Zend_Session_Namespace('data');
        $table    = new Tri_Db_Table('glossary');
        $page     = Zend_Filter::filterStatic($this->_getParam('page'), 'int');
        $select   = $table->select();

        $select->where('classroom_id = ?', $session->classroom_id);

        $query = base64_decode($this->_getParam("q"));

        if ($this->_hasParam("text")) {
            $query = $this->_getParam("text");
        }

        if ($query) {
            $where = strlen($query) == 1 ? "$query%" : "%$query%";
            $select->where('UPPER(word) LIKE UPPER(?)', $where);
        }
        
        $paginator = new Tri_Paginator($select, $page);
        $this->view->data = $paginator->getResult();
        $this->view->q = $query;
    }

    public function formAction()
    {
        $id   = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $form = new Glossary_Form_Glossary();

        if ($id) {
            $table = new Tri_Db_Table('glossary');
            $row   = $table->find($id)->current();

            if ($row) {
                $form->populate($row->toArray());
            }
        }

        $this->view->form = $form;
    }

    public function saveAction()
    {
        $form    = new Glossary_Form_Glossary();
        $table   = new Tri_Db_Table('glossary');
        $session = new Zend_Session_Namespace('data');
        $data    = $this->_getAllParams();

        if ($form->isValid($data)) {
            $data = $form->getValues();
            $data['user_id']      = Zend_Auth::getInstance()->getIdentity()->id;
            $data['classroom_id'] = $session->classroom_id;

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
            $this->_redirect('glossary/index/form/id/'.$id);
        }

        $this->view->messages = array('Error');
        $this->view->form = $form;
        $this->render('form');
    }

    public function deleteAction()
    {
        $table = new Tri_Db_Table('glossary');
        $id    = Zend_Filter::filterStatic($this->_getParam('id'), 'int');

        if ($id) {
            $table->delete(array('id = ?' => $id));
            $this->_helper->_flashMessenger->addMessage('Success');
        }

        $this->_redirect('glossary/index/');
    }
}