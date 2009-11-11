<?php
class Application_Model_Abstract extends Xend_Db_Table
{
	protected $_schema = "trails";
    
	protected function _setup()
	{
		parent::_setup();
		$this->setRowsetClass( "Xend_Db_Table_Rowset" );
	}

    public function save( $data , $relation = true )
    {
        if( $relation && isset($_POST['json_relation']) ){
            $relation = new Application_Model_Relation();
            $data[get_class($this)]['relation'] = $relation->saveRelation();
        }
        
        return parent::save( $data );
    }
	
	public function verifyDependence( $id , $field )
	{
		$db = $this->getAdapter();
	
		$select = $db->select();
		
		$select->from( $this->_name, $field , 'trails' )
				->where( $field . ' = ?' , $id );

		$row = $db->fetchAll( $select );
		
		return $row;
	}
	
	public function count( $where = null )
	{
		$select = $this->select();

		if( $where )
			$select = $this->_where( $select , $where );
		
		$select->from( $this->_name , "COUNT(0) as count" , 'trails' );
		
		$row = $this->fetchRow( $select );
		
		return $row->count;
	}
	
	public function forIn( $where = null , $field = "id" )
	{
		$return = array();
		$result = $this->fetchAll( $where );
		
		foreach( $result as $rs ){
			$return[] = $rs->$field;
		}
		
		if ( !$return ){
			$return = array();
		}

		return join( "," , $return );
	}
	
	public function fetchRelation( $where = NULL , $order = NULL , $limit = NULL , $offset = NULL , $joinTables = NULL )
	{
		$user = new Zend_Session_Namespace( 'user' );

		$select = $this->select();
		$select->from( array ( 't' => $this->_name ) , new Zend_Db_Expr( "*" ) , 'trails' );
		
		if( $joinTables )
		{
			foreach( $joinTables as $alias => $infoTables )
				$select->join( array( $alias => $infoTables['table'] ) , $infoTables['on'] , array() );
		}
		
		if( $where )
			$select = $this->_where( $select , $where );

		if( $user->group_id )
		{
			$subquery = $this->getAdapter()->select();
			$subquery->from( "trails.relation" , "relation" );
			$subquery->where( "course_id = ?" , $user->course_id );
			$subquery->orWhere( "discipline_id = ?" , $user->discipline_id );
			$subquery->orWhere( "class_id = ?" , $user->group_id );
			$subquery->orWhere( "person_id = ?" , $user->person_id );
			$select->where( "relation IN( ( {$subquery->__toString()} ) )" );
		}
		elseif( $user->discipline_id )
		{
			$subquery = $this->getAdapter()->select();
			$subquery->from( "trails.relation" , "relation" );
			$subquery->where( "course_id = ?" , $user->course_id );
			$subquery->orWhere( "discipline_id = ?" , $user->discipline_id );
			$subquery->orWhere( "person_id = ?" , $user->person_id );
			$select->where( "relation IN( ( {$subquery->__toString()} ) )" );
		}
		elseif( $user->course_id )
		{
			$select->where( "relation IN( ( SELECT relation FROM trails.relation cdgp WHERE course_id = ? ) )" , $user->course_id );
		}
		
		if( $limit )
			$select->limit( $limit , $offset );
			
		if( $order )
			$select->order( $order );
		
		return $this->fetchAll( $select );
	}
	
	public function countRelation( $where = NULL , $joinTables = NULL )
	{
		$user = new Zend_Session_Namespace( 'user' );

		$select = $this->select();
		$select->from( array ( 't' => $this->_name ) , new Zend_Db_Expr( "COUNT(0) as count" ) , 'trails' );
		
		if( $joinTables )
		{
			foreach( $joinTables as $alias => $infoTables )
				$select->join( array( $alias => $infoTables['table'] ) , $infoTables['on'] , array() );
		}
		
		if( $where )
			$select = $this->_where( $select , $where );

		if( $user->group_id )
		{
			$subquery = $this->getAdapter()->select();
			$subquery->from( "trails.relation" , "relation" );
			$subquery->where( "course_id = ?" , $user->course_id );
			$subquery->orWhere( "discipline_id = ?" , $user->discipline_id );
			$subquery->orWhere( "class_id = ?" , $user->group_id );
			$select->where( "relation IN( ( {$subquery->__toString()} ) )" );
		}
		elseif( $user->discipline_id )
		{
			$subquery = $this->getAdapter()->select();
			$subquery->from( "trails.relation" , "relation" );
			$subquery->where( "course_id = ?" , $user->course_id );
			$subquery->orWhere( "discipline_id = ?" , $user->discipline_id );
			$select->where( "relation IN( ( {$subquery->__toString()} ) )" );
		}
		elseif( $user->course_id )
		{
			$select->where( "relation IN( ( SELECT relation FROM trails.relation cdgp WHERE course_id = ? ) )" , $user->course_id );
		}
		
		return $this->fetchRow( $select )->count;
	}
	
	public function getStatus()
	{
		$translate = Zend_Registry::get( 'Zend_Translate' ); 
		
		$db = $this->getAdapter();
		
		$select = $db->select();
		
		$select->from( array( "s" => "status" ) , "*" , "trails" )
			   ->join( array( "st" => "trails.status_table" ) , "s.id = st.status_id" , array() )
			   ->where( "table_name = ?" , $this->_name );

		$statusRelation = $db->fetchAll( $select );
		
		foreach( $statusRelation as $status )
			$data[$status["id"]] = $translate->_( $status["name"] ); 

		return $data;
	}
}