<?php
class Activity_Model_ActivityGroup extends Activity_Model_Abstract
{
    protected $_name 	= 'activity_group';
	protected $_primary = 'id';
	
	protected $_dependentTables = array( 'Activity_Model_ActivityGroupPerson' , 'Activity_Model_ActivityTextGroup' );
	
	protected $_referenceMap = array( 
		array(
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
		'title'             => array( 'NotEmpty', array( 'StringLength', 0 , 255 ) ),
	);
	
	public function fetchActivityByGroup( $user_id )
	{
		$user  = new Zend_Session_Namespace( "user" );
		$select = $this->select();
		$activity = new Activity_Model_Activity();
		
		if ( Share_Model_Role::STUDENT == $user->roles[SYSTEM_ID]['current'] ){
			$select->from( array ( 'ag' => $this->_name ) , new Zend_Db_Expr( "a.id as aid , a.title , started , finished , composition_type , ag.id" ) , 'trails' )
				   ->join( array ( 'agp' => 'activity_group_person' )  , "ag.id = agp.activity_group_id" , array() , "trails"  )
				   ->join( array ( 'a' => 'activity' )  , "a.id = ag.activity_id"  , array() , "trails" )
				   ->where( "started  <= ?" , date('Y-m-d') )
				   ->where( "finished >= ?" , date('Y-m-d') )
				   ->where( "agp.person_id =?" , $user_id )
				   ->order( 'id , finished' );
				   
				   return $this->fetchAll( $select );
		}
		else{
			return $activity->fetchRelation( array( "composition_type =?" => Activity_Model_Activity::GROUPED ) );
		}
	}

    public function _save()
    {
        $activity  = new Zend_Session_Namespace( "activity" );

        $this->_data['Activity_Model_ActivityGroup']['activity_id'] = $activity->id;
    }

    public function _postSave()
    {
        $activity            = new Zend_Session_Namespace( "activity" );
        $activityGroupPerson = new Activity_Model_ActivityGroupPerson();

        $id = $this->_data['Activity_Model_ActivityGroup']['id'];

        $activityGroupPerson->save( $id , $activity->id );
    }
}