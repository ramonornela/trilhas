<?php
class Chat_MessageController extends Controller 
{
	//public $uses = array( "Message" , "User" , "Chat", "Logged" , "ChatMessage" , "ChatRoomMessage" , "Person" , "File", "PersonGroup" );
	
	public function indexAction()
	{
		$user = new Zend_Session_NameSpace( 'user' );

		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
		$id = ( $id )?( $id ):( $user->person_id );

		$this->view->person 	= $id;
		$this->view->rsMessages = $this->Message->fetchAll( array( "person_receiver_id = ?" => $id ) );
		$this->view->teachers 	= $this->PersonGroup->fetchAll( array( "group_id = ?" => $user->group_id , "role_id = ?" => Role::TEACHER  ) );
		$this->view->friends 	= $this->PersonGroup->fetchAllStudents($user->group_id, true, true, 200);

		$this->render();
	}
	
	public function viewAction()
	{
		$user 		= new Zend_Session_NameSpace( 'user' );
		$id 		= Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
		$idMessage 	= Zend_Filter::filterStatic( $this->_getParam( "idMessage" ) , "int" );
		
		$this->view->rs 		= $this->Person->find( $id )->current();
		$this->view->rsMessages = $this->Message->fetchAll( array( "person_receiver_id = ?" => $id ), 'id DESC' );
		$this->view->person 	= $user->person_id;
		
		if( $idMessage )
			$this->view->data = $this->Message->fetchRow( array( "id = ?" => $idMessage ) );
		
		$this->render( null , $this->getLayout() );
	}
	
	public function saveAction()
	{
		$user = new Zend_Session_NameSpace( 'user' );
		$id   = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
				
		$input  = $this->preSave();
		if( $input->isValid() )
		{
			$data = $input->toArray();
			$data["person_sender_id"] = $user->person_id;

            try{
                $id = $this->Message->save( $data );

                if( $id )
                {
                    $this->_helper->_flashMessenger->addMessage( $this->view->translate( "registered successfully" ) );
                    $this->_redirect( "/chat/message/view/id/" . $data["person_receiver_id"] );
                }
            }
            catch(Exception $e){
                $this->_helper->_flashMessenger->addMessage( $this->view->translate( "error save" ) );
                $this->_redirect( "/chat/message/index/" );
            }
		}
		else
		{
			$this->_helper->_flashMessenger->addMessage( $this->view->translate( "save error" ) );
			$this->_redirect( "/chat/message/index/" );
		}
	}
	
	public function deleteAction()
	{
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
		$userId = Zend_Filter::filterStatic( $this->_getParam( "userId" ) , "int" );
		
		if( $id )
		{
			$this->Message->delete( $id );
			$this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted successfully" ) );
			$this->_redirect( "/chat/message/view/id/" . $userId );
		}
	}
}