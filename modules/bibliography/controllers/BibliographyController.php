<?php
class Bibliography_BibliographyController extends Application_Controller_Abstract
{
    protected $_model = "Bibliography_Model_Bibliography";

	public function indexAction()
	{
        $bibliography = new Bibliography_Model_Bibliography();

        $this->view->rs             = $bibliography->fetchRelation( 
                                                    array( "type = ?" => Bibliography_Model_Bibliography::BASIC ) , "title"
                                                );
                                                
		$this->view->rsComplement   = $bibliography->fetchRelation( 
                                                    array( "type = ?" => Bibliography_Model_Bibliography::ADDITIONAL ) , "title"
                                                );
	}

	public function inputAction()
	{
        $bibliography = new Bibliography_Model_Bibliography();

        $this->view->data = $bibliography->createRow();

		parent::inputAction();
	}
}