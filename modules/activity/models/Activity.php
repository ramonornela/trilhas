<?php
class Activity extends Table 
{
    protected $_name 	= 'trails_activity';
	protected $_primary = 'id';
	
	const INDIVIDUALLY = "I";
	const GROUPED      = "G";
	
	protected $_dependentTables = array( 'ActivityGroup' , 'ActivityStage' , 'ActivityTextGroup' , 'Bulletin' , 'ActivityGroupPerson' , 'ActivityTextPerson' );
	
	protected $_referenceMap = array( 
		array(
			'refTableClass' => 'Person',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'person_id' )
		)
	);
	
	public $filters = array(
		'*'	 => 'StringTrim',
	    'id' => 'Int'
	);
	
	public $validators = array(
	    'id'                => 'Int',
		'title'             => array( 'NotEmpty', array( 'StringLength', 0 , 255 ) ),
		'started'           => array( 'NotEmpty', array( 'Date', "Y-m-d" ) , array( "dateDependency" , "finished_activity" ) ),
		'finished'          => array( 'NotEmpty', array( 'Date', "Y-m-d" ) ),
		'composition_type'  => array( 'NotEmpty', array( 'StringLength', 0 , 1 ) ),
	);
	
	public function saveActivityStage( $saves )
	{
		$id = parent::save( $saves );
		
		if ( $saves['id'] )
			return $id;
			
		$activityStage = new ActivityStage();
		
		$values = array(
			array(
				"title" => "titulo 1", 
				"ds" 	 => "ds 1"
			),
			array(
				"title" => "titulo 2", 
				"ds" 	 => "ds 2"
			),
			array(
				"title" => "titulo 3", 
				"ds" 	 => "ds 3"
			)
		);
		
		foreach( $values as $val )
		{
			$stageSaves["activity_id"] = $id;
			$stageSaves["title"] 	   = $val['title'];
			$stageSaves["ds"] 		   = $val['ds'];
		
			$activityStage->save( $stageSaves );
			$activityStage->id = null;
		}
		
		return $id;
	}
}