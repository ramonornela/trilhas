<?php
class Evaluation_Model_EvaluationQuestionRel extends Evaluation_Model_Abstract
{
	protected $_name    = "evaluation_question_rel";
	protected $_primary = array( 'evaluation_id' , 'evaluation_question_id' ); 
	
	protected $_referenceMap = array( 
		'EvaluationQuestion' => array(
			'refTableClass' => 'Evaluation_Model_EvaluationQuestion',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'evaluation_question_id' )
		),
		'Evaluation' => array(
			'refTableClass' => 'Evaluation_Model_Evaluation',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'evaluation_id' )
		)
	);
	
	function save( $data )
	{
        $questionId = $data['Evaluation_Model_EvaluationQuestionRel']['evaluation_question_id'];
		$this->delete( array( 'evaluation_question_id' => $questionId ) );
		
		parent::save( $data );
	}
	
	public function fetchFields( $evaluationId )
	{
		$select = $this->select();
		
		$select->from( array( 'eqr' => $this->_name ) , new Zend_Db_Expr('*') , 'trails' )
		       ->join( array( 'eq' => 'trails.evaluation_question' ) , "ff.id = fgf.evaluation_question_id" , array() )
		       ->where( "fgf.evaluation_id =?" , $evaluationId );
		
		return $this->fetchAll( $select );
	}
	
	public function deleteRel( $evaluation , $question )
    {
		$db = $this->getAdapter();
		
		$where = $db->quoteInto( "evaluation_id = ?" , $evaluation );
		$where .= " AND " . $db->quoteInto( "evaluation_question_id = ?" , $question );
		
		$db->delete( "trails.evaluation_question_rel" , $where );
    }
	
}