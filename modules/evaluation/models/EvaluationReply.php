<?php
class EvaluationReply extends Table
{
	protected $_name    = "trails_evaluation_reply";
	protected $_primary = "id"; 
	
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
		
		$select->from( array( 'er' => $this->_name ) , array( new Zend_Db_Expr( 'evaluation_id , person_id' ) )  )
			   ->join( array ( 'eq' => 'trails_evaluation_question' ) , 'er.evaluation_question_id = eq.id' , array() )
			   ->where( "er.person_id =? " , $person_id )
			   ->where( "er.evaluation_id =? " , $evaluation_id );
	  	
		return $this->fetchRow($select);
	}
	
	public function fetchPersonByReply( $evaluation_id , $person_id = null )
	{
                $user = new Zend_Session_Namespace( "user" );
                
		$select = $this->select();
		
		$select->from( array( "er" => $this->_name ) , array( new Zend_Db_Expr( "er.id AS evaluation_reply_id , p.id , UPPER(p.name) as name, p.file_id" ) ) )
			   ->join( array ( "eq" => "trails_evaluation_question" ) , "er.evaluation_question_id = eq.id" , array()  )
			   ->join( array ( "p" => "trails_person" ) , "er.person_id = p.id" , array() )
                           ->join( array ( "pg" => "trails_person_group" ) , "pg.person_id = p.id" , array() )
			   ->where( "eq.type = 'textarea'" )
                           ->where( "pg.role_id = ?" , Role::STUDENT )
                           ->where( "pg.group_id = ?" , $user->group_id )
			   ->where( "er.evaluation_id =? " , $evaluation_id )
                           ->order( "p.name" );

                if($person_id){
                    $select->where( "er.person_id =? " , $person_id );
                }
                
		$result = $this->fetchAll( $select );
                
        $data = array();
        $evaluationPerson = new EvaluationPerson();
        $evaluationNote = new EvaluationNote();
        $db = $evaluationPerson->getAdapter();
        foreach( $result as $key => $rs ){
            if( !array_key_exists( $rs->id  , $data ) ){
                $version = $this->fetchReplyVersion( $rs->id , $evaluation_id )->current()->version;

                /**
                 * verify if exists question with value of the version
                 */
                $select = $db->select();
                $select->from( array( "ep" => "trails_evaluation_person" ) , array( 'en.*' )  )
                       ->join( array( "en" => "trails_evaluation_note" ), 'en.evaluation_id = ep.evaluation_id', array() )
                       ->where( "en.person_id =? " , $rs->id )
                       ->where( "en.evaluation_id =? " , $evaluation_id )
                       ->where( "en.group_id =?" , $user->group_id );
                
                $question = $db->fetchRow( $select );
                
                if( empty($question['NOTE']) ){
                    $data[$rs->id]['id'] = $rs->id;
                    $data[$rs->id]['name'] = $rs->name;
                    //$data[$rs->id]['location'] = $rs->location;
                }
            }
        }
        $evaluationNote = new EvaluationNote();
        
        return $data;
	}

    public function fetchReplyVersion( $person_id , $evaluation_id )
    {
		
        $select = $this->select()->setIntegrityCheck(false);
        
		$select->from(  $this->_name , array( "version" ) )
               ->where( "person_id =? " , $person_id )
               ->where( "evaluation_id =? " , $evaluation_id )
               ->order( "version DESC" );
               
		return $this->fetchAll( $select );
    }

    public function fetchStudentsEvaluated( $evaluation_id )
    {
        $select = $this->select()->setIntegrityCheck(false);

        $select->distinct()->from( $this->_name , array( "person_id" , "person_id AS id" ) )
               ->where( "evaluation_id = ?" , $evaluation_id );

        return $this->fetchAll( $select );
    }
    
    public function fetchIdReply( $evaluation_id , $person_id, $version = null )
    {
        $select = $this->select()->setIntegrityCheck(false);

        $select->from( $this->_name , array( "id, evaluation_question_id" ) )
               ->where( "evaluation_id = ?" , $evaluation_id )
               ->where( "person_id = ?" , $person_id );

        if(!empty($version)){
            $select->where( "version = ?" , $version );
        }
        
        return $this->fetchAll( $select );
    }

    public function fetchIdReplyGroup( $evaluation_id , $person_id, $version = null )
    {
        $select = $this->select()->setIntegrityCheck(false);

        $select->from( $this->_name , array( "evaluation_question_id" ) )
               ->where( "evaluation_id = ?" , $evaluation_id )
               ->where( "person_id = ?" , $person_id )
               ->group( array( "evaluation_question_id" ) );

        if(!empty($version)){
            $select->where( "version = ?" , $version );
        }

        return $this->fetchAll( $select );
    }

}