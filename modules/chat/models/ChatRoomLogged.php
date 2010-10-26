<?php
class ChatRoomLogged extends Table
{
	protected $_name    = "trails_chat_room_logged";
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
			 'refTableClass' => 'Person',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'person_id' )
		),
		array(
			 'refTableClass' => 'ChatRoom',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'chat_room_id' )
		) 
	);
}