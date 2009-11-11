<?php
class Evaluation_Model_EvaluationQuestion extends Evaluation_Model_Abstract
{
	protected $_name    = "evaluation_question";
	protected $_primary = "id"; 
	
	protected $_dependentTables = array("Evaluation_Model_EvaluationQuestionRel",
										"Evaluation_Model_EvaluationValue",
										"Evaluation_Model_EvaluationReply" );

	public $filters = array(
		'*'	 => 'StringTrim',
	    'id' => 'Int'
	);
		
	public $validators = array(
		'type'     => 'NotEmpty',
		'position' => 'Int',
		'uniq'     => array( array( 'StringLength', 0 , 1 ) ),
		'note'     => array( 'NotEmpty' ),
	);

	protected function _postSave()
	{
		$evaluation = new Zend_Session_Namespace( "evaluation" );

		$evaluationValue = new Evaluation_Model_EvaluationValue();

		$evaluationQuestion = $this->_data['Evaluation_Model_EvaluationQuestion'];

		$evaluationValue->save( $evaluationQuestion['id'] );
	}
	
	public function fetchFieldTextArea( $id , $person_id = null , $version = null )
	{
        /**
         * search textarea with response
         */
        $select = $this->select()->setIntegrityCheck(false);
        
		$select->from( array( 'eq' => $this->_name ) , array( '*' , 'eqr.note AS question_note' , "er.value" , "eqn.note AS response_note" ) , 'trails' )
			   ->join( array ( 'eqr' => 'evaluation_question_rel' ) , 'eqr.evaluation_question_id = eq.id' , array() , "trails" )
			   ->joinLeft( array ( 'er' => 'evaluation_reply' ) , 'er.evaluation_question_id = eq.id' , array() , "trails" )
			   ->joinLeft( array ( 'eqn' => 'evaluation_reply_note' ) , 'eqn.evaluation_reply_id = er.id' , array() , "trails" )
			   ->where( "er.evaluation_id =? " , $id )
			   ->where( "er.person_id =? " , $person_id )
			   ->where( "eq.type = 'textarea'" );

        if( $version ){
            $select->where( "er.version = ?" , $version );
        }
               
		return $this->fetchAll($select);
	}
	
	public function fetchQuestion( $evaluation_id , $number_question = null , $question_id = null )
	{
		$distinct = null;
		$select = $this->select();
		
		if ( $number_question ){
			$distinct = "DISTINCT ( evaluation_question_id ),";
		}
		
		$select->from( array( 'eq' => $this->_name ) , array( new Zend_Db_Expr( $distinct . 'evaluation_question_id , type , label , eq.id , eqr.note , evaluation_id' ) ) , 'trails' )
			   ->order( "evaluation_question_id" );
			   
		if ( $number_question ){
			$select->join( array ( 'er' => 'evaluation_reply' ) , 'er.evaluation_question_id = eq.id' , array() , "trails" );
			$select->where( "er.evaluation_id = ? " , $evaluation_id );
		}else{
			$select->join( array ( 'eqr' => 'evaluation_question_rel' ) , 'eqr.evaluation_question_id = eq.id' , array() , "trails" );
			$select->where( "eqr.evaluation_id = ? " , $evaluation_id );
		}

        if( $question_id ){
            $select->where( "eqr.evaluation_question_id = ? " , $question_id );
        }

		return $this->fetchAll($select);
	}

    public function search( $query = null , $type = null )
    {
        $where = $this->select();

        if( $query ){
            $where->where( "station.accent_remove(label) ILIKE station.accent_remove(?)" , "%$query%" );
        }

        if( $type ){
            $where->where( "type =?" , "$type" );
        }

        $where->order( "id" )
              ->limit( 10 );
        
        return $this->fetchAll( $where );
    }
}