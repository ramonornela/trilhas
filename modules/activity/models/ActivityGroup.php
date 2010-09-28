<?php

class ActivityGroup extends Table 
{
    protected $_name 	= 'trails_activity_group';
	protected $_primary = 'id';
	
	protected $_dependentTables = array( 'ActivityGroupPerson' , 'ActivityTextGroup' );
	
	protected $_referenceMap = array( 
		array(
			'refTableClass' => 'Activity',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'activity_id' )
		)
	);
	
	public $filters = array(
		'*'	 => 'StringTrim',
	    'id' => 'Int'
	);
	
	public $validators = array(
	    'id'                => 'Int',
		'title'             => array( 'NotEmpty', array( 'StringLength', 0 , 255 ) ),
	);
	
	public function fetchActivityByGroup( $user_id )
	{
		$user  = new Zend_Session_Namespace( "user" );
		$select = $this->select();
		$Activity = new Activity();
		
		if ( Role::STUDENT == $user->role_id )
		{
			$select->from( array ( 'ag' => $this->_name ) , new Zend_Db_Expr( "a.id as aid , a.title , started , finished , composition_type , ag.id" ) )
				   ->join( array ( 'agp' => 'trails_activity_group_person' )  , "ag.id = agp.group_id" , array()  )
				   ->join( array ( 'a' => 'trails_activity' )  , "a.id = ag.activity_id"  , array() )
				   ->where( "started  <= ?" , date('Y-m-d') )
				   ->where( "finished >= ?" , date('Y-m-d') )
				   ->where( "agp.person_id =?" , $user_id )
				   ->order( 'finished' );
				   
				   return $this->fetchAll( $select );
		}
		else
			return $Activity->fetchAll( array( "composition_type =?" => Activity::GROUPED ) );
			
	}
}