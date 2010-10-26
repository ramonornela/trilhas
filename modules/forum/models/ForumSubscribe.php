<?php
class ForumSubscribe extends Table
{
    protected $_name = 'trails_forum_subscribe';
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
			 'refTableClass' => 'Person',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'person_id' )
		),
		array(
			 'refTableClass' => 'Forum',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'forum_id' )
		)
	);
	
}