<?php
class Evaluation extends Table
{
	protected $_name    = "trails_evaluation";
	protected $_primary = "id";
	
	protected $_dependentTables = array( "EvaluationQuestionRel" , "Bulletin" , "EvaluationNote" );
	
	public $filters = array(
		'*'	 => 'StringTrim',
	    'id' => 'Int'
	);
		
	public $validators = array(
	    'id'    => 'Int',
		'name'  => array( 'NotEmpty', array( 'StringLength', 0 , 255 ) ),
		'started'  => array( 'NotEmpty', array( 'Date', "Y-m-d" ) ),
		'finished'  => array( array( 'Date', "Y-m-d" ) )
	);
	
}