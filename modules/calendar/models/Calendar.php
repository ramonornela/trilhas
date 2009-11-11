<?php
class Calendar_Model_Calendar extends Calendar_Model_Abstract
{
    protected $_name    = "calendar";
	protected $_primary = "id";	
	
	public $filters = array(
		'*'  => 'StringTrim',
		'id' => 'Int',
		'started'	=> array( 'Date' ),
		'finished'	=> array( 'Date' )
	);
	
	public $validators = array( 
		'ds'		=> array( 'NotEmpty' ), 
		'started'	=> array( 'NotEmpty' , 'Date' ),
		'finished'	=> array( 'NotEmpty' , 'Date' , array( "dateGreaterThan" , "Calendar_Model_Calendar-started" ) ),
	);
	
	protected $_dependentTables = array(  );
	
	protected $_referenceMap = array( 
		array(
			 'refTableClass' => 'Share_Model_Person',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'person_id' )
		) 
	);

	public function _save(){
		$user = new Zend_Session_Namespace("user");
		$this->_data['Calendar_Model_Calendar']['person_id'] = $user->person_id;
	}

	public function fetchRelation( $where = NULL , $order = NULL , $limit = NULL , $offset = NULL )
	{
		$user = new Zend_Session_Namespace("user");

		if( $user->course->all )
		{
			$select = $this->select();
			$select->from( array ( "t" => $this->_name ) , new Zend_Db_Expr( "*" ) , "trails" );

			$select = $this->_where( $select , $where );

			$subquery = $this->getAdapter()->select();
			$subquery->from( "trails.relation" , "relation" );
			$subquery->where( "course_id IN({$user->course->all} )" );
			$subquery->orWhere( "discipline_id IN({$user->discipline->all} )" );
			$subquery->orWhere( "class_id IN({$user->group->all} )" );
			$subquery->orWhere( "person_id = ?" , $user->person_id );

			$select->where( "relation IN( ( {$subquery->__toString()} ) ) )" )
				   ->orWhere( "relation IS NULL AND person_id = ?" , $user->person_id )
				   ->order( "started" );;
            
			return $this->fetchAll( $select );
		}

		return new Zend_Db_Table_Rowset(array());
	}
    
    public function fetchAllDate()
    {
    	$user = new Zend_Session_Namespace("user");
		
    	if( $user->course->all )
		{
			$select = $this->select();
	
			$select->from( array( "c" => $this->_name ) , array( "started" , "finished" ) , "trails" )
				   ->joinLeft( array( "u" => "trails.relation" ) , "c.relation = u.relation" , array() )
				   ->where( "course_id IN( ". $user->course->all ." )" )
				   ->orWhere( "u.discipline_id IN( ". $user->discipline->all ." )" )
				   ->orWhere( "u.class_id IN( ". $user->group->all ." )" )
				   ->orWhere( "u.person_id = ?" , $user->person_id )
				   ->orWhere( "u.relation IS NULL AND c.person_id = ?" , $user->person_id )
				   ->order( "started" );
            
			$result = $this->fetchAll( $select );
			
			$return[] = 0;
			foreach( $result as $key => $val )
			{
				$date = split( "-" , $val['started'] );
				$return[] = $date[1] . "/" . $date[2] . "/" . $date[0];
				
				$date = split( "-" , $val['finished'] );
				$return[] = $date[1] . "/" . $date[2] . "/" . $date[0];
			}
			return join( "," , $return );
		}
		
		return false;
    }
}