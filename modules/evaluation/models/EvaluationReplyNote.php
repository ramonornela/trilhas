<?php
class EvaluationReplyNote extends Table
{
	protected $_name    = "trails_evaluation_reply_note";
	protected $_primary = array( "evaluation_reply_id" , "person_id" ) ; 
	
	protected $_referenceMap = array( 
		array(
			'refTableClass' => 'EvaluationReply',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'evaluation_reply_id' )
		)
	);
	 
}