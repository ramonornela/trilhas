<?php
class Page_IndexController extends Tri_Controller_Action
{
    public function init()
    {
        parent::init();
        $this->view->title = "Page";
    }
    
    public function indexAction()
    {
        $session  = new Zend_Session_Namespace('data');
        $table    = new Tri_Db_Table('page');
        $page     = Zend_Filter::filterStatic($this->_getParam('page'), 'int');
        $select   = $table->select()->order('position');

        $query = $this->_getParam("q");

        if ($query) {
            $select->where('UPPER(title) LIKE UPPER(?)', "%$query%");
        }
        
        $paginator = new Tri_Paginator($select, $page);
        $this->view->data = $paginator->getResult();
        $this->view->q = $query;
    }

    public function formAction()
    {
        $id   = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $form = new Page_Form_Page();

        if ($id) {
            $table = new Tri_Db_Table('page');
            $row   = $table->find($id)->current();

            if ($row) {
                $form->populate($row->toArray());
            }
        }

        $this->view->form = $form;
    }

    public function viewAction()
    {
        $id   = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $form = new Page_Form_Page();

        if ($id) {
            $table = new Tri_Db_Table('page');
            $row   = $table->find($id)->current();

            if ($row) {
                $this->view->data = $row;
                $this->view->title = $row->title;
            } else {
                $this->_redirect('index');
            }
        } else {
            $this->_redirect('index');
        }
    }

    public function saveAction()
    {
        $form    = new Page_Form_Page();
        $table   = new Tri_Db_Table('page');
        $session = new Zend_Session_Namespace('data');
        $data    = $this->_getAllParams();

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
            $this->_redirect('page/index/form/id/'.$id);
        }

        $this->view->messages = array('Error');
        $this->view->form = $form;
        $this->render('form');
    }

    public function deleteAction()
    {
        $table = new Tri_Db_Table('page');
        $id    = Zend_Filter::filterStatic($this->_getParam('id'), 'int');

        if ($id) {
            $table->delete(array('id = ?' => $id));
            $this->_helper->_flashMessenger->addMessage('Success');
        }

        $this->_redirect('page/index/');
    }
}