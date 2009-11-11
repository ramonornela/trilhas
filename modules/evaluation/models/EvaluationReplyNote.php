<?php
class Evaluation_Model_EvaluationReplyNote extends Evaluation_Model_Abstract
{
	protected $_name    = "evaluation_reply_note";
	protected $_primary = array( "evaluation_reply_id" , "person_id" ) ; 
	
	protected $_referenceMap = array( 
		array(
			'refTableClass' => 'Evaluation_Model_EvaluationReply',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'evaluation_reply_id' )
		)
	);
	 
}