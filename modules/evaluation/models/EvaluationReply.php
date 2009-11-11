<?php
class Evaluation_Model_EvaluationReply extends Evaluation_Model_Abstract
{
	protected $_name    = "evaluation_reply";
	protected $_primary = "id"; 
	
	protected $_dependentTables = array( "Evaluation_Model_EvaluationReplyNote" );
	
	protected $_referenceMap = array( 
		array(
			'refTableClass' => 'Evaluation_Model_EvaluationQuestion',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'evaluation_question_id' )
		),
		array(
			 'refTableClass' => 'Evaluation_Model_EvaluationValue',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'evaluation_value_id' )
		),
		array(
			 'refTableClass' => 'Share_Model_Person',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'person_id' )
		)
	);
	
	public function fetchEvaluationUser( $person_id , $evaluation_id )
	{
		$select = $this->select();
		
		$select->from( array( 'er' => $this->_name ) , array( new Zend_Db_Expr( 'evaluation_id , person_id' ) ) , 'trails' )
			   ->join( array ( 'eq' => 'trails.evaluation_question' ) , 'er.evaluation_question_id = eq.id' , array() )
			   ->where( "er.person_id =? " , $person_id )
			   ->where( "er.evaluation_id =? " , $evaluation_id );
	  	
		return $this->fetchRow($select);
	}
	
	public function fetchPersonByReply( $evaluation_id )
	{
		$select = $this->select();
		
		$select->from( array( "er" => $this->_name ) , array( new Zend_Db_Expr( "er.id AS evaluation_reply_id , p.id , p.name , p.location" ) ) , "trails" )
			   ->join( array ( "eq" => "evaluation_question" ) , "er.evaluation_question_id = eq.id" , array() , "trails" )
			   ->join( array ( "p" => "person" ) , "er.person_id = p.id" , array() , "station" )
			   ->where( "eq.type = 'textarea'" )
			   ->where( "er.evaluation_id =? " , $evaluation_id )
               ->order( "p.id" );
	  	
		$result = $this->fetchAll( $select );

        $data = array();
        foreach( $result as $key => $rs ){
            if( !array_key_exists( $rs->id  , $data ) ){
                $version = $this->fetchReplyVersion( $rs->id , $evaluation_id )->current()->version;

                /**
                 * verify if exists question with value of the version
                 */
                $select = $this->select()->setIntegrityCheck( false );
                $select->from( array( "er" => $this->_name ) , array( "*" ) , "trails" )
                       ->where( "er.id =? " , $rs->evaluation_reply_id )
                       ->where( "er.version =? " , $version );

                $question = $this->fetchRow( $select );
                
                if( count( $question ) ){
                    /**
                     * if exists question, verify if question have note
                     */
                    $select = $this->select()->setIntegrityCheck( false );
                    $select->from( array( "er" => $this->_name ) , array( "*" ) , "trails" )
                           ->join( array( "ern" => "evaluation_reply_note" ) , "ern.evaluation_reply_id = er.id" , array() , "trails" )
                           ->where( "er.id =? " , $rs->evaluation_reply_id )
                           ->where( "er.version =? " , $version );
                           
                    $textarea = $this->fetchRow( $select );
                    /**
                     * if not note, store in an array the data of the person
                     */
                    if( !count( $textarea ) ){
                        $data[$rs->id]['id'] = $rs->id;
                        $data[$rs->id]['name'] = $rs->name;
                        $data[$rs->id]['location'] = $rs->location;
                    }
                }
            }
        }

        return $data;
	}

    public function fetchReplyVersion( $person_id , $evaluation_id )
    {
        $select = $this->select()->setIntegrityCheck(false);
        
		$select->from(  $this->_name , array( "version" ) , "trails" )
               ->where( "person_id =? " , $person_id )
               ->where( "evaluation_id =? " , $evaluation_id )
               ->order( "version DESC" );
        
		return $this->fetchAll( $select );
    }

    public function fetchStudentsEvaluated( $evaluation_id )
    {
        $select = $this->select()->setIntegrityCheck(false);

        $select->distinct()->from( $this->_name , array( "person_id" , "person_id AS id" ) , "trails" )
               ->where( "evaluation_id = ?" , $evaluation_id );

        return $this->fetchAll( $select );
    }
    
    public function fetchIdReply( $evaluation_id , $person_id )
    {
        $select = $this->select()->setIntegrityCheck(false);

        $select->from( $this->_name , array( "id" ) , "trails" )
               ->where( "evaluation_id = ?" , $evaluation_id )
               ->where( "person_id = ?" , $person_id );

        return $this->fetchAll( $select );
    }

}