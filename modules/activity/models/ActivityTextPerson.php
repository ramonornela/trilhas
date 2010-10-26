<?php
class ActivityTextPerson extends Table 
{
    protected $_name 	= 'trails_activity_text_person';
	protected $_primary = 'id';
	
	const SAVE_STUDENT     = "SS";
	const FINALITY_STUDENT = "FS";
	const SAVE_TEACHER     = "ST";
	const FINALITY_TEACHER = "FT";
	
	protected $_referenceMap = array( 
		"P" => array(
			'refTableClass' => 'Person',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'person_id' )
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
	
	public function save( $data , $status , $person_id = null )
	{
		$user 	  = new Zend_Session_NameSpace( 'user' );
		$activity = new Zend_Session_NameSpace( 'activity' );
		
		$data['activity_id'] = $activity->id;

		if( $person_id )
			$data['person_id']  = $person_id;
		else
			$data['person_id']  = $user->person_id;
		
		$data['save_person_id'] = $user->person_id;
		$data['status']         = $status;
		
		parent::save( $data );
	}
	
	public function findTextFinality( $activity_id )
	{
		$select = $this->select();
		
		$select->from( array( "atp" => $this->_name ) , new Zend_Db_Expr('*') )
			   ->where( "activity_id =?" , $activity_id )
                           ->where( "id = ( SELECT MAX(id) FROM {$this->_name} WHERE person_id = atp.person_id AND activity_id = atp.activity_id )" );
		
		return $this->fetchAll( $select );
	}
	
}