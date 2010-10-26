<?php
class ChatRead extends Table
{
	protected $_name    = "trails_chat_read";
	protected $_primary = array( "chat_id" , "person_id" );	
	
	public $filters = array(
		'*'  => array( 'StringTrim', 'Int' )
	);
	
	public $validators = array( 
		'*'		=> array(  'Int' ,  'NotEmpty' )
	);
	
	protected $_dependentTables = array();
	
	protected $_referenceMap = array( 
 		array(
			 'refTableClass' => 'Person',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'person_receiver_id' )
		),
		array(
			 'refTableClass' => 'Chat',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'chat_id' )
		)
	);
}