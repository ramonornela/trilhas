<?php
class EvaluationQuestionRel extends Table
{
	protected $_name    = "trails_evaluation_question_rel";
	protected $_primary = array( 'evaluation_id' , 'evaluation_question_id' ); 
	
	protected $_referenceMap = array( 
		'EvaluationQuestion' => array(
			'refTableClass' => 'EvaluationQuestion',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'evaluation_question_id' )
		),
		'Evaluation' => array(
			'refTableClass' => 'Evaluation',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'evaluation_id' )
		)
	);

    function save( $data )
	{
        $rs['evaluation_question_id'] = $data['evaluation_question_id'];
        $rs['evaluation_id']          = $data['evaluation_id'];
		$this->delete( $rs );

		parent::save( $data );
	}

	//function save( $questionId , $evaluationId = null , $generation = false )
	//{
	//	parent::delete( $questionId  , 'evaluation_question_id' );
		
	//	$saves['evaluation_id'] = $evaluationId;
	//	$saves['evaluation_question_id'] = $questionId;
		
	//	parent::save($saves);
	//}
	
	public function fetchFields( $evaluationId )
	{
		$select = $this->select();
		
		$select->from( array( 'eqr' => $this->_name ) , new Zend_Db_Expr('*') )
		       ->join( array( 'eq' => 'trails_evaluation_question' ) , "ff.id = fgf.evaluation_question_id" , array() )
		       ->where( "fgf.evaluation_id =?" , $evaluationId );
		
		return $this->fetchAll( $select );
	}
	
	public function deleteRel( $evaluation , $question )
    {
		$db = $this->getAdapter();
		
		$where = $db->quoteInto( "evaluation_id = ?" , $evaluation );
		$where .= " AND " . $db->quoteInto( "evaluation_question_id = ?" , $question );
		
		$db->delete( "trails_evaluation_question_rel" , $where );
    }
	
}