<?php
class ChatMessage extends Table
{
	protected $_name    = "trails_chat_message";
	protected $_primary = array( "chat_id" , "person_sender_id" , "person_receiver_id" );	
	
	public $filters = array(
		'*'  => 'StringTrim',
		'chat_id' => 'Int'
	);
	
	public $validators = array( 
		'chat_id'		     => array(  'Int' ,  'NotEmpty' ), 
		'person_sender_id'	 => array(  'Int' ,  'NotEmpty' ), 
		'person_receiver_id' => array(  'Int' ,  'NotEmpty' ) 
	);
	
	protected $_dependentTables = array(  );
	
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
		),
		array(
			 'refTableClass' => 'Person',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'person_sender_id' )
		) 
	);
	
	public function fetchMessage( $now )
	{
		$user = new Zend_Session_Namespace("user");
		
		$db  = $this->getAdapter();
		$sql = $db->quoteInto( "
			SELECT trails_chat.id as chatid, person_sender_id , person_receiver_id , ds , trails_chat.created, ps.name as psname , pr.name as prname
			FROM trails_chat_message
			JOIN trails_chat ON chat_id = trails_chat.id
			JOIN trails_person ps ON person_sender_id = ps.id
			JOIN trails_person pr ON person_receiver_id = pr.id
			WHERE ( person_receiver_id = ? OR person_sender_id = ? )
			AND trails_chat.id NOT IN( ( SELECT chat_id FROM trails_chat_read cr WHERE trails_chat.id = cr.chat_id AND ( cr.person_id = ? OR cr.person_id = ?) ) )
			ORDER BY trails_chat.created" , $user->person_id , 'INTEGER' );
		
		$result = array_map( "array_change_key_case", $db->fetchAll( $sql ) );

		if( $result )
		{
			$ChatRead = new ChatRead();
			
			foreach( $result as $rs )
			{
				$array = array("\r\n", "\n\r", "\n", "\r");
				$date = strtotime( $rs['created'] );
				$rs['created'] = date( "H:i" , $date );
				$rs['ds'] = str_replace( $array , "<br />" , $rs['ds'] );
				$tmp[$rs['chatid']] = $rs;
				
				$data['chat_id'] = $rs['chatid'];
				$data['person_id'] = $user->person_id;
				
				$ChatRead->save( $data );
			}
			
			if( $tmp ){
				foreach( $tmp as $value )
				{
					$return[] = $value;
				}
			}
		}
		
		return $return;
	}
}