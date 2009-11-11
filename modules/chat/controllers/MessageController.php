<?php
class Chat_MessageController extends Application_Controller_Abstract
{
	protected $_model = "Chat_Model_Message";

	public function indexAction()
	{
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );

		$user        = new Zend_Session_NameSpace( 'user' );
		$message     = new Chat_Model_Message();
		$classPerson = new Station_Model_ClassPerson();
		$classModel  = new Station_Model_ClassModel();
		
		$id = ( $id )?( $id ):( $user->person_id );

		$this->view->person 	= $id;
		$this->view->rsMessages = $message->fetchAll( array( "person_receiver_id = ?" => $id ) );
		$this->view->teachers 	= $classModel->fetchAll( array( "id = ?" => $user->group_id  ) );
		$this->view->friends 	= $classPerson->fetchAll( array( "class_id = ?" => $user->group_id ) );
	}
	
	public function viewAction()
	{
		$user 	= new Zend_Session_NameSpace( 'user' );
		$person = new Share_Model_Person();
		$message = new Chat_Model_Message();
		
		$id 		= Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
		$idMessage 	= Zend_Filter::filterStatic( $this->_getParam( "idMessage" ) , "int" );
		
		$this->view->rs 		= $person->find( $id )->current();
		$this->view->rsMessages = $message->fetchAll( array( "person_receiver_id = ?" => $id ) );
		$this->view->person 	= $user->person_id;
		
		if( $idMessage ){
			$this->view->data = $message->fetchRow( array( "id = ?" => $idMessage ) );
		}
	}
	
	public function deleteAction()
	{
		$message = new Chat_Model_Message();
		
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
		$userId = Zend_Filter::filterStatic( $this->_getParam( "userId" ) , "int" );
		
		if( $id ){
			$result = $message->delete( $id );
			$this->_helper->_flashMessenger->addMessage( $result->message );
			$this->_redirect( "/chat/message/view/id/" . $userId );
		}
	}
}