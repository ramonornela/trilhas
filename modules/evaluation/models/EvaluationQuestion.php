<?php
class EvaluationQuestion extends Table {
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
            'type'     => 'NotEmpty',
            'position' => array( 'Int', array( 'StringLength', 0 , 255 ) ),
            'uniq'     => array( array( 'StringLength', 0 , 1 ) ),
            //'note'    => array( 'NotEmpty' ),
    );

    //protected function _postSave()
    //{
    //	$evaluation = new Zend_Session_Namespace( "evaluation" );
//
    //	$evaluationValue = new Evaluation_Model_EvaluationValue();

    //	$evaluationQuestion = $this->_data['Evaluation_Model_EvaluationQuestion'];

    //	$evaluationValue->save( $evaluationQuestion['id'] );
    //}

    public function fetchFieldTextArea( $id , $person_id = null , $version = null ) {
        /**
         * search textarea with response
         */
        $select = $this->select()->setIntegrityCheck(false);

        $select->from( array( 'eq' => $this->_name ) , array( '*' , 'eqr.note AS question_note' , "er.value" , "eqn.note AS response_note" ) )
                ->join( array ( 'eqr' => 'trails_evaluation_question_rel' ) , 'eqr.evaluation_question_id = eq.id' , array()  )
                ->joinLeft( array ( 'er' => 'trails_evaluation_reply' ) , 'er.evaluation_question_id = eq.id' , array()  )
                ->joinLeft( array ( 'eqn' => 'trails_evaluation_reply_note' ) , 'eqn.evaluation_reply_id = er.id' , array() )
                ->where( "er.evaluation_id =? " , $id )
                ->where( "er.person_id =? " , $person_id )
                ->where( "eq.type = 'textarea'" );

        if( $version ) {
            $select->where( "er.version = ?" , $version );
        }

        return $this->fetchAll($select);
    }



    public function fetchQuestion( $evaluation_id , $number_question = null , $question_id = null , $discipline_id = null ) {
        $distinct = null;
        $select = $this->select();

        if ( $number_question ) {
            $distinct = "DISTINCT ( evaluation_question_id ),";
        }

        $select->from( array( 'eq' => $this->_name ) , array( new Zend_Db_Expr( $distinct . 'evaluation_question_id , type , labels , label , eq.id , eqr.note , evaluation_id, eq.theme' ) ) );

        if ( $number_question ) {
            $select->join( array ( 'er' => 'trails_evaluation_reply' ) , 'er.evaluation_question_id = eq.id' , array() );
            $select->where( "er.evaluation_id = ? " , $evaluation_id );
        }else {
            $select->join( array ( 'eqr' => 'trails_evaluation_question_rel' ) , 'eqr.evaluation_question_id = eq.id' , array() )
                    ->where( "eqr.evaluation_id = ? " , $evaluation_id )
                    ->order( "eqr.position" );
        }

        if( $question_id ) {
            $select->where( "eqr.evaluation_question_id = ? " , $question_id );
        }

        $select->order( "eq.id" );

        return $this->fetchAll($select);
    }

    public function search( $query = null , $type = null , $to = null , $limit = true ) {
        $limitNumber = 20;

        $where = $this->select();

        if( isset( $query ) && $query ) {
            $where->where( "UPPER( label ) LIKE UPPER(?) OR UPPER( labels ) LIKE UPPER(?) OR UPPER( theme ) LIKE UPPER(?)" , "%$query%" );
        }

        if( isset( $type ) && $type ) {
            $where->where( "type =?" , "$type" );
        }

        $where->order( "id DESC" );

        if( isset( $limit ) && $limit ) {
            if( isset( $to ) && $to ) {
                $where->limit( $limitNumber, $to * $limitNumber );
            }else {
                $where->limit( $limitNumber );
            }
        }

        return $this->fetchAll( $where );
    }

}
