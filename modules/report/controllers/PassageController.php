<?php
class Report_PassageController extends Application_Controller_Abstract
{
    protected $_model = false;
    
	public function indexAction()
	{
		$person = new Share_Model_Person();
		$contentAccess = new Content_Model_ContentAccess();
		$content = new Content_Model_Content();
		
        $user = new Zend_Session_Namespace('user');
        
		$this->view->rs = $person->fetchAllPersonByGroup();
        $this->view->ContentAccess = $contentAccess;
        $this->view->Content = $content;
        $this->view->discipline_id = $user->discipline_id;
	}
}

