<?php
class Content_ComposerController extends Tri_Controller_Action
{
    public function init()
    {
        parent::init();
        $this->view->title = "Composer";
    }
    
    public function indexAction()
    {
        $classroom = new Zend_Db_Table('classroom');
        $session   = new Zend_Session_Namespace('data');
        $id        = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $go        = $this->_getParam("go");

        if (!$session->contents) {
            $session->contents = Zend_Json::encode(
                Application_Model_Content::fetchAllOrganize($session->course_id));
        }
        $this->view->contents = $session->contents;
        $this->view->current = 0;

        if ($id) {
            $this->view->current = Application_Model_Content::getPositionById($id,
                    Zend_Json::decode($session->contents));
        }
    }

    /**
     * @access public
     * @return void
     * @final
     */
    public function formAction()
    {
        $id   	 = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $parent  = $this->_getParam('parent', false);
        $session = new Zend_Session_Namespace('data');
        $form    = new Content_Form_Composer();

        if ($id) {
            $table = new Tri_Db_Table('content');
            $row   = $table->find($id)->current();

            if ($row) { 
                $form->populate($row->toArray());
            }
        }

        $this->view->form = $form;
        $this->view->id   = $id;
        $this->_helper->layout->disableLayout();
    }

    /**
     * @access public
     * @return void
     * @final
     */
    public function saveAction()
    {
        $form  = new Content_Form_Composer();
        $table = new Tri_Db_Table('content');
        $data  = $this->_getAllParams();
        $session = new Zend_Session_Namespace('data');

        if ($form->isValid($data)) {
            $data = $form->getValues();
            $data['course_id'] = $session->course_id;

            if (!$data['content_id']) {
                unset($data['content_id']);
            }

            if (isset($data['id']) && $data['id']) {
                $row = $table->find($data['id'])->current();
                $row->setFromArray($data);
                $id = $row->save();
            } else {
                unset($data['id']);
                $row = $table->createRow($data);
                $id = $row->save();
            }

            $session = new Zend_Session_Namespace('data');
            unset($session->contents);
            
            $this->_helper->_flashMessenger->addMessage('Success');
            $this->_redirect('/content/composer/index/id/'.$id);
        }

        $this->view->messages = array('Error');
        $this->view->form = $form;
        $this->render('form');
    }

    /**
     * @access public
     * @return void
     * @final
     */
    public function deleteAction() {
        $id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );

        $content 	   = new Tri_Db_Table('content');
        $contentAccess = new Tri_Db_Table('content_access');
        $restriction   = new Tri_Db_Table('restriction_panel');

        try {
            if( $id ) {
                $restriction->delete(array('content_id = ?' => $id));
                $contentAccess->delete(array('content_id = ?' => $id));
                $content->delete(array('id = ?' => $id));
            }
        }catch( Exception $e ) {
            $this->_helper->_flashMessenger->addMessage('Error');
        }

        $session = new Zend_Session_Namespace('data');
        unset($session->contents);

        $this->_redirect('/content/composer/index/');
    }
}
