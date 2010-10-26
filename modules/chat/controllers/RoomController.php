<?php
class Chat_RoomController extends Controller 
{
	//public $uses = array( "ChatRoom" , "Logged" , "Chat", "Person" );
	
	public function indexAction()
	{
		$this->view->rooms = $this->ChatRoom->fetchRelation();
		$this->render();
	}
}