<?php
class Composer_OrganizerController extends Controller 
{
	//public $uses = array( "Content" , "Narration" , "Notepad" , "Restriction" );
	
	public function indexAction()
	{
		$id   = Zend_Filter::filterStatic( $this->_getParam("id") , "int" );
		$user = new Zend_Session_Namespace("user");
		$db   = $this->Content->getAdapter();
				
		if( $id ){
			$this->view->content = $id;
			/**
			 * @todo implement in model
			 */
			$sql = "SELECT id , title, content_id FROM trails_content WHERE ";
			$sql .= $db->quoteInto( "discipline_id = ? " , $user->discipline_id , 'INTEGER');
			$sql .= $db->quoteInto( " AND content_id = ? " , $id , 'INTEGER');
			$sql .= " ORDER BY position,id";
			$this->view->rs  = array_map('array_change_key_case' , $db->fetchAll( $sql ) );
		}
		else{
			/**
			 * @todo implement in model
			 */
			$sql = $db->quoteInto( "SELECT id , title, content_id FROM trails_content WHERE discipline_id = ? AND content_id IS NULL ORDER BY position,id" , $user->discipline_id , 'INTEGER');
			$this->view->rs  = array_map('array_change_key_case' , $db->fetchAll( $sql ) );
		}
		
		$this->render( null ,  $this->getLayout() );
	}
	
	public function inputAction()
	{
		$id   = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
		$user = new Zend_Session_Namespace("user");
		
		if( $id )
			$this->view->data = $this->Content->find( $id )->current();
		
		/**
		 * @todo implement in model
		 */
		$db  = $this->Content->getAdapter();
		$sql = $db->quoteInto( "SELECT id , title, content_id FROM trails_content WHERE discipline_id = ? ORDER BY content_id NULLS FIRST,position,id" , $user->discipline_id , 'INTEGER');
		$rs  = $db->fetchAll( $sql );
		
		$this->view->contents = $this->toSelectContent( $this->organize( $rs ) );
		$this->view->jsonValidate = Zend_Json::encode( $this->Content->validators );
		$this->render( null , "ajax" );
	}
	
	public function saveAction()
	{
		$data = Zend_Json::decode( $_POST['json'] );
		$id   = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
		
		foreach( $data as $key => $val ){
			$data = null;
			$data['id'] 	  = Zend_Filter::filterStatic( $val['id'] , "int" );
			$data['position'] = Zend_Filter::filterStatic( $val['position'] , "int" );
			$this->Content->save( $data );
		}
		
		$this->_helper->_flashMessenger->addMessage( $this->view->translate( "registered successfully" ) );
		$this->_redirect( "/composer/organizer/index/id/" . $id );
	}
	
	public function deleteAction()
	{
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
		
		if( $id )
			$this->Content->delete( $id );
		
		exit("ok");
	}
	
	/**
	 * @todo call method organize of the organize
	 * @deprecated 
	 * @param array $datas
	 * @return array
	 */
	private function organize( $datas )
	{
		foreach( $datas as $data )
		{
			if( !$data['content_id'] )
			{
				$return[$data['id']]['value'] = $data;
			}
			else
			{
				$return = $this->recursion( $return , $data );
			}
		}
		
		$this->view->contents = $return;
		return $return;
	}
	
	/**
	 * @todo call method organize of the organize
	 * @deprecated 
	 * @param array $datas
	 * @return array
	 */
	private function recursion( $return , $data )
	{
		foreach( $return as $key => $val )
		{
			if( $return[$data['content_id']]['value'] )
			{
				$return[$data['content_id']]['child'][$data['id']]['value'] = $data;
				return $return;
			}
			else
			{
				if( $val['child'] )
				{
					$return[$val['value']['id']]['child'] = $this->recursion( $val['child'] , $data );
				}
			}
		}
		
		return $return;
	}
	
	private function toSelectContent( $data , $xhtml = array() , $line = null )
	{
		foreach( $data as $key => $val )
		{
			if( !$val['child'] )
			{
				$xhtml[$val['value']['id']] = $line . ". " . $val['value']['title'];
			}
			else
			{
				$xhtml[$val['value']['id']] = $line . ". " . $val['value']['title'];
				
				$xhtml = $this->toSelectContent( $val['child'] , $xhtml , $line . ".." );
			}
		}
		
		return $xhtml;
	}
}
