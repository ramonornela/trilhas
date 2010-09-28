<?php
class Notepad_IndexController extends Tri_Controller_Action
{
    public function init()
    {
        parent::init();
        $this->view->title = "Notepad";
    }
    
    public function indexAction()
    {
        $id      = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $page    = Zend_Filter::filterStatic($this->_getParam('page'), 'int');
        $session = new Zend_Session_Namespace('data');
        $table   = new Tri_Db_Table('notepad');
        $form    = new Notepad_Form_Notepad();
        $select  = $table->select();

        $select->where('classroom_id = ?', $session->classroom_id);

        if ($id) {
            $table = new Tri_Db_Table('notepad');
            $row   = $table->find($id)->current();

            if ($row) {
                $form->populate($row->toArray());
            }
        }

        $paginator = new Tri_Paginator($select, $page);
        $this->view->data = $paginator->getResult();
        $this->view->form = $form;
    }

    public function saveAction()
    {
        $form  = new Notepad_Form_Notepad();
        $table = new Tri_Db_Table('notepad');
        $session = new Zend_Session_Namespace('data');
        $data  = $this->_getAllParams();

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
            $this->_redirect('notepad/index/index/');
        }

        $this->_helper->_flashMessenger->addMessage('Error');
        $this->view->form = $form;
        $this->render('index');
    }

    public function deleteAction()
    {
        $table = new Tri_Db_Table('notepad');
        $id    = Zend_Filter::filterStatic($this->_getParam('id'), 'int');

        if ($id) {
            $table->delete(array('id = ?' => $id));
            $this->_helper->_flashMessenger->addMessage('Success');
        }

        $this->_redirect('notepad/index/');
    }
}