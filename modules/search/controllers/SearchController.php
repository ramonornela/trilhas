<?php
class Search_SearchController extends Application_Controller_Abstract
{
	protected $string = "";
    protected $_model = false;

	public function indexAction()
	{
		$this->render();
	}

	public function glossaryAction()
	{
		$where = array ( "UPPER( word ) LIKE UPPER(?) OR UPPER( ds ) LIKE UPPER(?)" => "%$this->string%" );
		$result = $this->searchResult( $where , "Glossary_Model_Glossary", "glossary", $this->string );
        $this->pagination( $result );
	}

	public function forumAction()
	{
		$where = array ( "UPPER( ds ) LIKE UPPER(?) OR UPPER( title ) LIKE UPPER(?)" => "%$this->string%" );
		$result = $this->searchResult( $where , "Forum_Model_Forum", "forum", $this->string );
        $this->pagination( $result );
	}

	public function weblibraryAction()
	{
		$where = array ( 'UPPER( author ) LIKE UPPER(?) OR UPPER( title ) LIKE UPPER(?)' => "%$this->string%" , "type = 'WB'" );
		$result = $this->searchResult( $where , "File_Model_File", "weblibrary", $this->string );
        $this->pagination( $result );
	}

	public function rssAction()
	{
		$where = array ( "UPPER( channel ) LIKE UPPER(?)" => "%$this->string%");
		$result = $this->searchResult( $where , "Rss_Model_Rss", "rss", $this->string );
        $this->pagination( $result );
	}

	public function contentAction()
	{
		$user    = new Zend_Session_NameSpace( 'user' );
        $content = new Content_Model_Content();

		$this->view->string        = $this->string;
		$this->view->type          = "content";
		$this->view->contents      = $user->contents;
		$result                    = $content->searchContent( $this->string, $user->discipline_id );

        $this->pagination( $result );
	}


	public function searchResult($where, $model, $type, $string )
	{
		$objModel = new $model();

        $result = $objModel->fetchRelation( $where , "id DESC");

		$this->view->string = $string;
		$this->view->type = $type;

		return $result;
	}

	public function preDispatch()
	{
		$this->string = $this->_getParam( "string" );
	}

    public function pagination( $object )
	{
        if( !is_object( $object ) ){
            throw new Xend_Exception( "Variable is not of type array" );
        }

        $paginator = Zend_Paginator::factory( $object );
        
        $page = $this->_getParam( "page" );
        if( isset( $page ) && $page ){
            $paginator->setCurrentPageNumber( $page );
        }

        $this->view->paginator = $paginator;
	}
}