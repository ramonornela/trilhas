<?php
class Activity_Model_Activity extends Activity_Model_Abstract
{
    protected $_name 	= 'activity';
	protected $_primary = 'id';
	
	const INDIVIDUALLY = "I";
	const GROUPED      = "G";
	
	public $filters = array(
		'*'	       => 'StringTrim',
	    'id'       => 'Int',
        'started'  => 'Date',
        'finished' => 'Date'
	);

	public $validators = array(
		'title'             => array( 'NotEmpty', array( 'StringLength', 0 , 255 ) ),
		'started'           => array( 'NotEmpty', array( 'Date', "Y-MM-dd" ) ),
		'finished'          => array( 'NotEmpty', array( 'Date', "Y-MM-dd" ) ),
		'composition_type'  => array( 'NotEmpty', array( 'StringLength', 0 , 1 ) ),
	);
    
	protected $_dependentTables = array( 'Activity_Model_ActivityGroup' , 'Activity_Model_ActivityStage' , 'Activity_Model_ActivityTextGroup' , 'Bulletin_Model_Bulletin' , 'Activity_Model_ActivityGroupPerson' , 'Activity_Model_ActivityTextPerson' );
	
	protected $_referenceMap = array( 
		array(
			'refTableClass' => 'Share_Model_Person',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'person_id' )
		)
	);
	
	
	public function _save()
	{
		$user = new Zend_Session_Namespace('user');

        $this->_data['Activity_Model_Activity']['person_id'] = $user->person_id;
	}
	
	public function _postsave()
	{
		$activityStage = new Activity_Model_ActivityStage();
        $activityStage->saveActivityStage( $this->_data['Activity_Model_Activity']['id'] );
	}
}