<?php
class Search_SearchController extends Controller
{
	//public $uses = array( "File", "Rss", "Glossary", "Content",  "Forum" );
	
	protected $string = "";
	
	public function indexAction()
	{
		$this->render();
	}
	
	public function glossaryAction()
	{
		$where = array ( "UPPER( word ) LIKE UPPER(?) OR UPPER( ds ) LIKE UPPER(?)" => "%$this->string%" );
		$this->view->searchGlossary = $this->searchResult( $where , "Glossary", "glossary", $this->string );
		
		$this->render();
	}
	
	public function forumAction()
	{
		$where = array ( "UPPER( ds ) LIKE UPPER(?) OR UPPER( title ) LIKE UPPER(?)" => "%$this->string%" );
		
		$this->view->searchForum = $this->searchResult( $where , "Forum", "forum", $this->string );
		
		$this->render();
	}
	
	public function mapAction()
	{
		$where = array ( "UPPER( ds ) LIKE UPPER(?) OR UPPER( title ) LIKE UPPER(?)" => "%$this->string%", "type = 'MP' OR type = 'MU'" );
		
		$this->view->searchMap = $this->searchResult( $where , "File", "map", $this->string );
		
		$this->render();
	}
	
	public function multimediaAction()
	{
		$where = array ( "UPPER( ds ) LIKE UPPER(?) OR UPPER( title ) LIKE UPPER(?)" => "%$this->string%", "type = 'DR'" ); 
		
		$this->view->searchMultimedia = $this->searchResult( $where , "File", "multimedia", $this->string );
		
		$this->render();
	}
	
	public function weblibraryAction()
	{
		$where = array ( 'UPPER( author ) LIKE UPPER(?) OR UPPER( title ) LIKE UPPER(?)' => "%$this->string%" , "type = 'WB'" );
		
		$this->view->searchWeblibrary= $this->searchResult( $where , "File", "weblibrary", $this->string );
		
		$this->render();
	}
	
	public function rssAction()
	{
		$where = array ( "UPPER( channel ) LIKE UPPER(?)" => "%$this->string%");
		
		$this->view->searchRss = $this->searchResult( $where , "Rss", "rss", $this->string );
		
		$this->render();
	}
	
	public function contentAction()
	{	
		$user = new Zend_Session_NameSpace( 'user' );
		
		$this->view->searchContent = $this->Content->searchContent( $this->string, $user->discipline_id );
		$this->view->string        = $this->string;
		$this->view->type          = "content";
		
		$this->render();
	}
	
	
	public function searchResult($where, $model, $type, $string )
	{
		$result = $this->$model->fetchRelation( $where , "id DESC");
		
		$this->view->string = $string;
		$this->view->type = $type;
		
		return $result;
	}
	
	public function preDispatch()
	{
		$this->string = $this->_getParam( "string" );
	}
}