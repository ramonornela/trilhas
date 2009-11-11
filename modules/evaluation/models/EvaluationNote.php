<?php
class Evaluation_Model_EvaluationNote extends Evaluation_Model_Abstract
{
	protected $_name    = "evaluation_note";
	protected $_primary = array( "evaluation_id" , "person_id" ); 
	
	protected $_referenceMap = array( 
		array(
			'refTableClass' => 'Bulletin_Model_Bulletin',
			'refColumns'	=> array( 'item' ),
			'columns'		=> array( 'evaluation_id' )
		),
		array(
			'refTableClass' => 'Evaluation_Model_Evaluation',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'evaluation_id' )
		)
	);
}