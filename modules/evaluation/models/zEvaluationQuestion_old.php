<?php
class EvaluationQuestion extends Table
{
	protected $_name    = "trails_evaluation_question";
	protected $_primary = "id"; 
      public    $fileSave = 'label';
	
	protected $_dependentTables = array( "EvaluationQuestionRel" , "EvaluationValue" , "EvaluationReply" );

	public $filters = array(
		'*'	 => 'StringTrim',
	    'id' => 'Int'
	);
		
	public $validators = array(
	    'id'       => 'Int',
		'label'    => array( 'NotEmpty' ),
		'type'     => 'NotEmpty',
		'position' => array( 'Int', array( 'StringLength', 0 , 255 ) ),
		'uniq'     => array( array( 'StringLength', 0 , 1 ) ),
		'note'    => array( 'NotEmpty' ),
	);
	
	public function fetchFieldTextArea( $id , $person_id = null )
	{
		$select = $this->select();
		
		$select->from( array( 'eq' => $this->_name ) , array( new Zend_Db_Expr( '*' ) ) )
			   ->joinLeft( array ( 'er' => 'trails_evaluation_reply' ) , 'er.evaluation_question_id = eq.id' , array() )
			   ->joinLeft( array ( 'eqn' => 'trails_evaluation_reply_note' ) , 'eqn.evaluation_reply_id = er.id' , array() )
			   ->where( "er.evaluation_id =? " , $id )
			   ->where( "er.person_id =? " , $person_id )
			   ->where( "eq.type = 'textarea'" );
	  	
		return $this->fetchRow($select);
	}
	
	public function fetchQuestion( $formId , $number_question = null )
	{
		$select = $this->select();
		
		if ( $number_question )
			$distinct = "DISTINCT ( evaluation_question_id ),";
			
		$select->from( array( 'eq' => $this->_name ) , array( new Zend_Db_Expr( $distinct . 'evaluation_question_id , type , label , eq.id , note , evaluation_id' ) ) )
			   ->order( "position" );
			   
		if ( $number_question )
		{
			$select->join( array ( 'er' => 'trails_evaluation_reply' ) , 'er.evaluation_question_id = eq.id' , array() );
			$select->where( "er.evaluation_id =? " , $formId );
		}
		else
		{
			$select->join( array ( 'eqr' => 'trails_evaluation_question_rel' ) , 'eqr.evaluation_question_id = eq.id' , array() );
			$select->where( "eqr.evaluation_id =? " , $formId );
		}
		
		return $this->fetchAll($select);
	}
	
	public function fetchQuestionRand( $qnt )
	{
		$select = $this->select();
		
		$select->from( array( 'eq' => $this->_name ) , array( new Zend_Db_Expr( '*' ) ) )
			   ->where( "eq.type <> 'textarea'" )
			   ->order( "RANDOM()" )
			   ->limit( $qnt );
			
		return $this->fetchAll($select);
	}
	
	
}