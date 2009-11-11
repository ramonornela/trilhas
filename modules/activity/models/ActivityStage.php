<?php

class Activity_Model_ActivityStage extends Activity_Model_Abstract
{
    protected $_name 	= 'activity_stage';
	protected $_primary = 'id';
	
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
		'title' => array( 'NotEmpty', array( 'StringLength', 0 , 255 ) ),
		'ds'    => 'NotEmpty'
	);
	
	public function _save()
	{
		if( ! $this->_data['Activity_Model_ActivityStage']['activity_id'] ){
			$activity = new Zend_Session_NameSpace( 'activity' );

        	$this->_data['Activity_Model_ActivityStage']['activity_id'] = $activity->id;
		}
	}
	
	public function saveActivityStage( $activity_id )
	{
		if ( $_POST['data']['Activity_Model_Activity']['id'] )
			return false;
		
		$values = array(
			array(
				"title" => "versão original",
				"ds" 	 => "ds 1"
			)
		);
		
		foreach( $values as $val )
		{
			$data["Activity_Model_ActivityStage"]["activity_id"] = $activity_id;
			$data["Activity_Model_ActivityStage"]["title"] 	  = $val['title'];
			$data["Activity_Model_ActivityStage"]["ds"] 		  = $val['ds'];
		
			$this->save( $data , false );
		}
	}
}