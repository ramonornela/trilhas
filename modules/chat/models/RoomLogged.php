<?php
class Chat_Model_ChatRoomLogged extends Table
{
	protected $_name    = "chat_room_logged";
	protected $_primary = "id";	
	
	public $filters = array(
		'*'  => 'StringTrim',
		'id' => 'Int'
	);
	
	public $validators = array( 
		'id'		=> array(  'Int' ,  'NotEmpty' ), 
		'chat_room_id'		=> array(  'Int' ,  'NotEmpty' ), 
		'created'		=> array(  'NotEmpty' ), 
		'person_id'		=> array(  'Int' ,  'NotEmpty' ) 
	);
	
	protected $_dependentTables = array(  );
	
	protected $_referenceMap = array( 
		array(
			 'refTableClass' => 'Preceptor_SharePerson',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'person_id' )
		),
		array(
			 'refTableClass' => 'Chat_Model_ChatRoom',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'chat_room_id' )
		) 
	);
}