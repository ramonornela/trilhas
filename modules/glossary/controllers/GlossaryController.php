<?php
class Glossary_GlossaryController extends Application_Controller_Abstract
{
	protected $_model = "Glossary_Model_Glossary";

	public function indexAction()
	{
		$glossary = new Glossary_Model_Glossary();
		$q = $this->_getParam( "q" );

		$where = null;
		
		if( $q ){
			$query = strlen( $q ) == 1 ? "$q%" : "%$q%";
			$where = array( 'UPPER( word ) LIKE UPPER(?)' => $query );
            $this->view->q = $q;
		}
			
		$this->view->rs = $glossary->fetchRelation( $where , "id DESC" , 10 );
	}

    public function inputAction()
    {
        $this->view->data->id       = null;
        $this->view->data->word     = null;
        $this->view->data->ds       = null;

        parent::inputAction();
    }
	
    public function deleteAction()
	{
        $user = new Zend_Session_NameSpace( 'user' );
        $user->words = null;
        
        parent::deleteAction();
        exit;
    }
}