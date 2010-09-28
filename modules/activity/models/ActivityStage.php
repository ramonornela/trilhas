<?php

class ActivityStage extends Table 
{
    protected $_name 	= 'trails_activity_stage';
	protected $_primary = 'id';
	
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
	    'id'    => 'Int',
		'title' => array( 'NotEmpty', array( 'StringLength', 0 , 255 ) ),
		'dss'    => 'NotEmpty'
	);
}