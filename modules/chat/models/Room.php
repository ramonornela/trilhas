<?php
class Chat_Model_ChatRoom extends Table
{
	protected $_name    = "chat_room";
	protected $_primary = "id";	
	
	public $filters = array(
		'*'  => 'StringTrim',
		'id' => 'Int'
	);
	
	public $validators = array( 
		'id'		=> array(  'Int' ,  'NotEmpty' ), 
		'title'		=> array(  'NotEmpty' ,  array( 'StringLength' , '0' , '255' ) ), 
		'start'		=> array(  'NotEmpty' ), 
		'finish'		=> array(  'NotEmpty' ), 
		'created'		=> array(  'NotEmpty' ) 
	);
	
	protected $_dependentTables = array( "Chat_Model_ChatRoomLogged" , "Chat_Model_ChatRoomMessage" );
	
	protected $_referenceMap = array();
}