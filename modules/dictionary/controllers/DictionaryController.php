<?php
class Dictionary_DictionaryController extends Application_Controller_Abstract
{
    protected $_model = "Dictionary_Model_Dictionary";

    public function indexAction()
	{
		$dictionary = new Dictionary_Model_Dictionary();

        $this->view->rs   = $dictionary->fetchRelation( null , "id DESC" );
	}

    public function inputAction()
    {
		$dictionary = new Dictionary_Model_Dictionary();

        $this->view->data = $dictionary->createRow();

        parent::inputAction();
    }
}