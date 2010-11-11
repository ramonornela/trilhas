<?php
class Content_OrganizerController extends Tri_Controller_Action
{
    public function init()
    {
        parent::init();
        $this->view->title = "Organize";
    }

	public function indexAction()
	{
		$id		 = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
        $session = new Zend_Session_Namespace('data');
		$content = new Tri_Db_Table('content');

        if ($id) {
			$this->view->id = $id;

			$where = $content->select()
							 ->from('content', array('id', 'title', 'content_id'))
							 ->where('course_id = ?', $session->course_id)
							 ->where('content_id = ?', $id)
							 ->order(array('position', 'id'));

			$this->view->data = $content->fetchAll($where)->toArray();
		} else {
			$where = $content->select()
							 ->from('content', array('id', 'title', 'content_id'))
							 ->where('course_id = ?', $session->course_id)
							 ->where('content_id IS NULL')
							 ->order(array('position', 'id'));

			$this->view->data = $content->fetchAll($where)->toArray();
		}

        $this->view->save = Zend_Filter::filterStatic($this->_getParam('save'), 'int');

        if (!$this->_hasParam('layout')) {
            $this->_helper->layout->disableLayout();
        }
	}
	
	public function saveAction()
	{
        $data    = $_POST['position'];
        $id      = Zend_Filter::filterStatic($this->_getParam('id'), 'int');
		$content = new Tri_Db_Table('content');
		
		$i = 1;
        foreach ($data as $key => $val) {
			$row = $content->find($key)->current();
            $row->position = $i;
            $row->save();

            $i++;
		}
		
		$this->_helper->_flashMessenger->addMessage('Success');
		$this->_redirect('/content/organizer/index/id/' . $id);
	}
}
