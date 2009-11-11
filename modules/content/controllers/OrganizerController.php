<?php
class Content_OrganizerController extends Application_Controller_Abstract
{
	protected $_model = false;
	
	public function indexAction()
	{
		$id		 = Zend_Filter::filterStatic( $this->_getParam("id") , "int" );
		$user	 = new Zend_Session_Namespace("user");
		$content = new Content_Model_Content();
		$db      = $content->getAdapter();
				
		if( $id ){
			$this->view->id = $id;

			$where = $content->select()
							 ->from( 'content' ,  array( "id" , "title" , "content_id" ) , 'trails' )
							 ->where( 'discipline_id = ?', $user->discipline_id )
							 ->where( 'content_id = ?' , $id )
							 ->order( array( 'position','id' ) );

			$this->view->rs = $content->fetchAll( $where )->toArray();
		}
		else{
			$where = $content->select()
							 ->from( 'content' ,  array( "id" , "title" , "content_id" ) , 'trails' )
							 ->where( 'discipline_id = ?', $user->discipline_id )
							 ->where( 'content_id IS NULL' )
							 ->order( array( 'position','id' ) );

			$this->view->rs = $content->fetchAll( $where )->toArray();
		}
	}
	
	public function saveAction()
	{
		$data = Zend_JSON::decode( $_POST['json'] );
		$id   = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
		$content = new Content_Model_Content();
		$user	 = new Zend_Session_Namespace("user");
		
		foreach( $data as $key => $val ){
			$data = null;
			$data['Content_Model_Content']['id'] 	     = $val['id'];
			$data['Content_Model_Content']['position'] = $val['position'];
			
			$content->save( $data );

			$user->contents = false;
		}
		
		$this->_helper->_flashMessenger->addMessage( $this->view->translate( "registered successfully" ) );
		$this->_redirect( "/content/organizer/index/id/" . $id );
	}
}
