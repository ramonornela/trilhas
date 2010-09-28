<?php
class EvaluationReply extends Table
{
	protected $_name    = "trails_evaluation_reply";
	protected $_primary = "id"; 
      public    $fileSave = 'value';
	
	protected $_dependentTables = array( "EvaluationReplyNote" );
	
	protected $_referenceMap = array( 
		array(
			'refTableClass' => 'EvaluationQuestion',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'evaluation_question_id' )
		),
		array(
			 'refTableClass' => 'EvaluationValue',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'evaluation_value_id' )
		),
		array(
			 'refTableClass' => 'Person',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'person_id' )
		)
	);
	
	public function fetchEvaluationUser( $person_id , $evaluation_id )
	{
		$select = $this->select();
		
		$select->from( array( 'er' => $this->_name ) , array( new Zend_Db_Expr( 'evaluation_id , person_id' ) ) )
			   ->join( array ( 'eq' => 'trails_evaluation_question' ) , 'er.evaluation_question_id = eq.id' , array() )
			   ->where( "er.person_id =? " , $person_id )
			   ->where( "er.evaluation_id =? " , $evaluation_id );
	  	
		return $this->fetchRow($select);
	}
	
	public function fetchPersonByReply( $evaluation_id )
	{
		$select = $this->select();
		
		$select->from( array( "er" => $this->_name ) , array( new Zend_Db_Expr( "p.id , p.name" ) ) )
			   ->join( array ( "eq" => "trails_evaluation_question" ) , "er.evaluation_question_id = eq.id" , array() )
			   ->join( array ( "p" => "trails_person" ) , "er.person_id = p.id" , array() )
			   ->where( "type = 'textarea'" )
			   ->where( "er.evaluation_id =? " , $evaluation_id )
			   ->group( array( "p.id" , "p.name" ) );
	  	
		return $this->fetchAll( $select );
	}
}