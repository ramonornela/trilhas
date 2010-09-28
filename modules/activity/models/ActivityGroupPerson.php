<?php

class ActivityGroupPerson extends Table 
{
    protected $_name 	= 'trails_activity_group_person';
	protected $_primary = array( 'group_id' , 'person_id' , 'activity_id' );
	
	protected $_referenceMap = array( 
		array(
			'refTableClass' => 'Person',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'person_id' )
		),
		array(
			'refTableClass' => 'ActivityGroup',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'group_id' )
		),
		array(
			'refTableClass' => 'Activity',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'activity_id' )
		)
	);
	
	public function save( $id , $activity_id )
	{
		parent::delete( $id , 'group_id' );
		
		foreach ( $_POST['activitygroupchecked'] as $val )
		{
			if ($val)
			{
				$saves['group_id']    = $id;
				$saves['person_id']   = $val;
				$saves['activity_id'] = $activity_id;
				
				parent::save( $saves );
			}
		}
	}
}