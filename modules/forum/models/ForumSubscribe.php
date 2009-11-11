<?php
class Forum_Model_ForumSubscribe extends Forum_Model_Abstract
{
    protected $_name = 'forum_subscribe';
    protected $_primary = array( 'person_id' , 'forum_id' );
	
    public $filters = array(
		'*'	 => 'StringTrim',
    	'person_id' => 'Int',
	    'forum_id' => 'Int'
	);
		
	public $validators = array(
		'person_id'	=> array( 'NotEmpty' , 'Int' ),
		'forum_id'	=> array( 'NotEmpty' , 'Int' )
	);
    
	protected $_dependentTables = array();
	
    protected $_referenceMap = array( 
		array(
			 'refTableClass' => 'Share_Model_Person',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'person_id' )
		),
		array(
			 'refTableClass' => 'Forum_Model_Forum',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'forum_id' )
		)
	);
	
	public function delete( $forum_id , $person_id )
	{
		$db = $this->getAdapter();
		
		$where = $db->quoteInto( 'person_id = ?', $person_id );
		$where .= $db->quoteInto( ' AND forum_id = ?', $forum_id );
		
		$db->delete( "trails.".$this->_name , $where );
	}
}