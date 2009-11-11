<?php
class Activity_Model_ActivityTextPerson extends Activity_Model_Abstract
{
    protected $_name 	= 'activity_text_person';
	protected $_primary = 'id';
	
	const SAVE_STUDENT     = "SS";
	const FINALITY_STUDENT = "FS";
	const SAVE_TEACHER 	   = "ST";
	const FINALITY_TEACHER = "FT";
	
	protected $_referenceMap = array( 
		"P" => array(
			'refTableClass' => 'Share_Model_Person',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'person_id' )
		),
		
		"SP" => array(
			'refTableClass' => 'Share_Model_Person',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'save_person_id' )
		),
		
		"A" => array(
			'refTableClass' => 'Activity_Model_Activity',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'activity_id' )
		)
		
	);
	
	public $filters = array(
		'*'	 => 'StringTrim',
	    'id' => 'Int'
	);
	
	public $validators = array(
		'ds' => 'NotEmpty'
	);
	
	public function saveText( $data , $status , $person_id = null )
	{
		$user 	  = new Zend_Session_NameSpace( 'user' );
		$activity = new Zend_Session_NameSpace( 'activity' );
		
		if( $person_id ){
			$data['Activity_Model_ActivityTextPerson']['person_id']  = $person_id;
        }else{
			$data['Activity_Model_ActivityTextPerson']['person_id']  = $user->person_id;
        }
        
		$data['Activity_Model_ActivityTextPerson']['activity_id']    = $activity->id;
		$data['Activity_Model_ActivityTextPerson']['save_person_id'] = $user->person_id;
		$data['Activity_Model_ActivityTextPerson']['status']         = $status;
		
		return parent::save( $data );
	}
	
	public function findTextFinality( $activity_id )
	{
		$select = $this->select();
		
		$select->from( array( "atp" => $this->_name ) , new Zend_Db_Expr('*') , 'trails' )
			   ->where( "activity_id =?" , $activity_id )
			   ->where( "id = ( SELECT MAX(id) FROM trails.{$this->_name} WHERE person_id = atp.person_id )" );
		
		return $this->fetchAll( $select );
	}
	
}