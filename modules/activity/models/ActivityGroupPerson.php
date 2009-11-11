<?php

class Activity_Model_ActivityGroupPerson extends Activity_Model_Abstract
{
    protected $_name 	= 'activity_group_person';
	protected $_primary = array( 'activity_group_id' , 'person_id' , 'activity_id' );
	
	protected $_referenceMap = array( 
		array(
			'refTableClass' => 'Share_Model_Person',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'person_id' )
		),
		array(
			'refTableClass' => 'Activity_Model_ActivityGroup',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'activity_group_id' )
		),
		array(
			'refTableClass' => 'Activity_Model_Activity',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'activity_id' )
		)
	);
	
	public function save( $id , $activity_id )
	{
		$this->delete( array( 'activity_group_id' => $id ) );
		
		foreach( $_POST['activitygroupchecked'] as $key => $val ){
			if( $val ){
				$saves['Activity_Model_ActivityGroupPerson']['activity_group_id']    = $id;
				$saves['Activity_Model_ActivityGroupPerson']['person_id']            = $val;
				$saves['Activity_Model_ActivityGroupPerson']['activity_id']          = $activity_id;
                
				parent::save( $saves );
			}
		}
	}
}