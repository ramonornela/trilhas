<?php
class Activity_Model_ActivityTextGroup extends Activity_Model_Abstract
{
    protected $_name 	= 'activity_text_group';
	protected $_primary = 'id';
	
	protected $_referenceMap = array( 
		"AG" => array(
			'refTableClass' => 'Activity_Model_ActivityGroup',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'activity_group_id' )
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
	
	public function saveText( $data , $status  )
	{
		$user 	  = new Zend_Session_NameSpace( 'user' );
		$activity = new Zend_Session_NameSpace( 'activity' );
		
		$data['Activity_Model_ActivityTextGroup']['activity_id']       = $activity->id;
		$data['Activity_Model_ActivityTextGroup']['save_person_id']    = $user->person_id;
		$data['Activity_Model_ActivityTextGroup']['activity_group_id'] = $activity->group_id;
		$data['Activity_Model_ActivityTextGroup']['status']            = $status;
		$data['Activity_Model_ActivityTextGroup']['name']              = $status;
		
		parent::save( $data );
	}
	
	public function findTextFinality( $activity_id )
	{
		$select = $this->select();
		
		$select->from( array( "atg" => $this->_name ) , new Zend_Db_Expr('*') , 'trails' )
			   ->where( "activity_id =?" , $activity_id )
			   ->where( "id = ( SELECT MAX(id) FROM trails.{$this->_name} WHERE activity_group_id = atg.activity_group_id )" );
		
		return $this->fetchAll( $select );
	}
}