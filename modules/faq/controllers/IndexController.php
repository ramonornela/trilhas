<?php
class Faq_IndexController extends Tri_Controller_Action
{
    public function init()
    {
        parent::init();
        $this->view->title = "Faq";
    }
    
    public function indexAction()
    {
        $session  = new Zend_Session_Namespace('data');
        $table    = new Tri_Db_Table('faq');
        $page     = Zend_Filter::filterStatic($this->_getParam('page'), 'int');
        $query    = Zend_Filter::filterStatic($this->_getParam("q"), 'stripTags');
        $select   = $table->select();

        $select->where('classroom_id = ?', $session->classroom_id);

        if ($query) {
            $select->where('UPPER(question) LIKE UPPER(?)', "%$query%");
        }
        
        $paginator = new Tri_Paginator($select, $page);
        $this->view->data = $paginator->getResult();
        $this->view->q = $query;
    }

    public function formAction()
    {
        $id   = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $form = new Faq_Form_Faq();

        if ($id) {
            $table = new Tri_Db_Table('faq');
            $row   = $table->find($id)->current();

            if ($row) {
                $form->populate($row->toArray());
            }
        }

        $this->view->form = $form;
    }

    public function saveAction()
    {
        $form  = new Faq_Form_Faq();
        $table = new Tri_Db_Table('faq');
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
            $this->_redirect('faq/index/form/id/'.$id);
        }

        $this->_helper->_flashMessenger->addMessage('Error');
        $this->view->form = $form;
        $this->render('form');
    }

    public function deleteAction()
    {
        $table = new Tri_Db_Table('faq');
        $id    = Zend_Filter::filterStatic($this->_getParam('id'), 'int');

        if ($id) {
            $table->delete(array('id = ?' => $id));
            $this->_helper->_flashMessenger->addMessage('Success');
        }

        $this->_redirect('faq/index/');
    }
}