<?php
class Evaluation_Model_Evaluation extends Evaluation_Model_Abstract
{
	protected $_name    = "evaluation";
	protected $_primary = "id";
	
	protected $_dependentTables = array("Evaluation_Model_EvaluationQuestionRel",
										"Bulletin_Model_Bulletin",
										"Evaluation_Model_EvaluationNote");
	
	public $filters = array(
		'*'        => 'StringTrim',
	    'id'       => 'Int',
		'time'     => array( 'DefaultValue' ),
        'started'  => array( 'Date' ),
		'finished' => array( 'Date' )
	);
		
	public $validators = array(
		'name'  => array( 'NotEmpty', array( 'StringLength', 0 , 255 ) ),
		'started'  => array( 'NotEmpty' ),
		'finished' => array( array( "dateGreaterThan" , "Evaluation_Model_Evaluation-started" ) )
	);
	
	public function _save()
	{
		$user = new Zend_Session_NameSpace( 'user' );
		$this->_data['Evaluation_Model_Evaluation']['person_id'] = $user->person_id;
	}
}