<?php
class ActivityTextGroup extends Table 
{
    protected $_name 	= 'trails_activity_text_group';
	protected $_primary = 'id';
	
	protected $_referenceMap = array( 
		"AG" => array(
			'refTableClass' => 'ActivityGroup',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'activity_group_id' )
		),
		
		"SP" => array(
			'refTableClass' => 'Person',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'save_person_id' )
		),
		
		"A" => array(
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
	    'id' => 'Int',
		'ds' => 'NotEmpty'
	);
	
	public function save( $data , $status  )
	{
		$user 	  = new Zend_Session_NameSpace( 'user' );
		$activity = new Zend_Session_NameSpace( 'activity' );
		
		$data['activity_id']       = $activity->id;
		$data['save_person_id']    = $user->person_id;
		$data['activity_group_id'] = $activity->group_id;
		$data['status']            = $status;
		
		parent::save( $data );
	}
	
	public function findTextFinality( $activity_id )
	{
		$select = $this->select();
		
		$select->from( array( "atg" => $this->_name ) , new Zend_Db_Expr('*') )
			   ->where( "activity_id =?" , $activity_id )
			   ->where( "id = ( SELECT MAX(id) FROM {$this->_name} WHERE activity_group_id = atg.activity_group_id AND activity_id = atg.activity_id )" );
		
		return $this->fetchAll( $select );
	}
}