<?php
class Chat extends Table
{
	protected $_name    = "trails_chat";
	protected $_primary = "id";	
	
	public $filters = array(
		'*'  => 'StringTrim',
		'id' => 'Int'
	);
	
	public $validators = array( 
		'id'		=> array( 'Int' , 'NotEmpty' ), 
		'ds'		=> array( 'NotEmpty' ), 
		'created'		=> array( 'NotEmpty' ) 
	);
	
	protected $_dependentTables = array( "ChatMessage" , "ChatRoomMessage" );
	
	protected $_referenceMap = array( 
 
	);
	
	public function listUsers()
	{
		$user = new Zend_Session_Namespace("user");
		
		$db  = $this->getAdapter();
		$sql = "
			SELECT distinct person.id personid , person.name , ac.id , ac.name acname , logged.status status , location
			FROM TRAILS_PERSON person
			JOIN trails_file ON person.file_id = trails_file.id
			JOIN trails_person_group pg ON pg.person_id = person.id
			JOIN trails_acl_role ac ON pg.role_id = ac.id
			LEFT JOIN trails_logged logged ON logged.person_id = person.id AND logged.created > ( sysdate - 20 /( 24 * 60 * 60 ) )";
		 
		$sql .= $db->quoteInto( "WHERE pg.group_id = ? " , $user->group_id , 'INTEGER' );
		$sql .= $db->quoteInto( "AND person.id <> ? " , $user->person_id , 'INTEGER' );
		$sql .= "ORDER BY status , ac.id DESC , person.name"; 
		
		return array_map( "array_change_key_case", $db->fetchAll( $sql ) );
	}
}