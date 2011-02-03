<?php
class File_IndexController extends Tri_Controller_Action
{
    public function init()
    {
        parent::init();
        $this->view->title = "File";
    }
    
    public function indexAction()
    {
        $session  = new Zend_Session_Namespace('data');
        $table    = new Tri_Db_Table('file');
        $page     = Zend_Filter::filterStatic($this->_getParam('page'), 'int');
        $query    = Zend_Filter::filterStatic($this->_getParam("q"), 'stripTags');
        $select   = $table->select();

        $select->where('classroom_id = ?', $session->classroom_id);

        if ($query) {
            $select->where('UPPER(name) LIKE UPPER(?)', "%$query%");
        }
        
        $paginator = new Tri_Paginator($select, $page);
        $this->view->data = $paginator->getResult();
        $this->view->q = $query;
    }

    public function formAction()
    {
        $form = new File_Form_File();
        $this->view->form = $form;
    }

    public function saveAction()
    {
        $form  = new File_Form_File();
        $table = new Tri_Db_Table('file');
        $session = new Zend_Session_Namespace('data');
        $data  = $this->_getAllParams();

        if ($form->isValid($data)) {
            if (!$form->location->receive()) {
                $this->_helper->_flashMessenger->addMessage('File fail');
            }
            
            $data = $form->getValues();
            $data['user_id']      = Zend_Auth::getInstance()->getIdentity()->id;
            $data['classroom_id'] = $session->classroom_id;

            $row = $table->createRow($data);
            $id = $row->save();
            Application_Model_Timeline::save('saved a new file', $data['name']);
        } else {
            $this->_response->prepend('messages', $this->view->translate('Error'));
            $this->view->form = $form;
            $this->render('form');
        }
    }

    public function deleteAction()
    {
        $table    = new Tri_Db_Table('file');
        $id       = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $location = $this->_getParam('location');

        @unlink(APPLICATION_PATH . '/../data/upload/' . $location);

        if ($id) {
            $table->delete(array('id = ?' => $id));
            $this->_helper->_flashMessenger->addMessage('Success');
        }

        $this->_redirect('file/index/');
    }

    public function downloadAction()
    {
        $file = $this->_getParam('file');
        $name = urlencode($this->_getParam('name'));
        
        if (headers_sent()) {
            echo 'File download failure: HTTP headers have already been sent and cannot be changed.';
            exit;
        }

        $path = realpath(APPLICATION_PATH.'/../data/upload/'.$file);
        if ($path === false || !is_file($path) || !is_readable($path)) {
            header('HTTP/1.0 204 No Content');
            exit;
        }

        $size = filesize($path);

        header('Expires: Mon, 20 May 1974 23:58:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Cache-Control: private');
        header('Pragma: no-cache');
        header("Content-Transfer-Encoding: binary");
        header("Content-type: application/octet-stream");
        header("Content-length: {$size}");
        header("Content-disposition: attachment; filename={$name}");

        while( ob_get_level() ){
            ob_get_clean();
        }

        echo file_get_contents($path);
        exit;
    }
}