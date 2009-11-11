<?
class Station_Model_ClassPerson extends Station_Model_Abstract
{
	protected $_schema   = "station";
	protected $_name     = "class_person";
	protected $_primary  = array( "person_id" , "class_id" );
	protected $_sequence = false;

	public $filters = array(
		'*'  => 'StringTrim',
		'person_id' => 'Int'
	);
	
	public $validators = array( 
		'person_id'		=> array(  'Int' ,  'NotEmpty' ), 
		'class_id'		=> array(  'Int' ,  'NotEmpty' )
	);
	
	protected $_dependentTables = array();
	
	protected $_referenceMap = array( 
		array(
			 'refTableClass' => 'Station_Model_ClassModel',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'class_id' )
		),
        array(
			 'refTableClass' => 'Share_Model_Person',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'person_id' )
		)
	);
	
	/**
	 * Return all users who are not registered in any group
	 *
	 * @param int $discipline_id Id da discipline
	 * @return array
	 */
	public function fetchAllInGroup( $id , $discipline_id )
	{
		$dicipline = new Station_Model_Discipline();
		
		$result = $dicipline->find( $discipline_id )
							->current()
							->findDependentRowset( "Station_Model_ClassModel" );
		
		foreach( $result as $rs ){
			$personGroups = $rs->findDependentRowset( "Station_Model_ClassPerson" ,
													  null ,
													  $this->select()
														   ->order( "role_id" )
														   ->where( "role_id = 2" ) );
			
			foreach( $personGroups as $personGroup ){
				$return[] = $personGroup;
			}
		}
		
		if( $id ){
			$result = $this->fetchAll( array( "class_id = ?" => $id ) , "role_id" );
	
			foreach( $result as $rs ){
				$return[] = $rs;
			}
		}
		
		return $return;
	}
	
	/**
	 * get course , discipline , group of the user
	 * 
	 * @param int $userId
	 * @return array 
	 */
	public function getCourseDiscipline( $userId , $roleId )
	{
		$db = $this->getAdapter();
		$select = $db->select();		
			
		$select->from( array( "g"  => "station.class" ) , new Zend_Db_Expr( "g.* , cd.* , c.name AS course_title , d.name AS discipline_title" ) )
			   ->join( array( "d"  => "station.discipline" ) , "d.id = g.discipline_id" , array() )
		       ->join( array( "cd" => "station.course_discipline" ) , "d.id = cd.discipline_id" , array() )
		       ->join( array( "c"  => "station.course" ) , "c.id = cd.course_id"  , array() )
		       ->order( array( "course_id" , "g.discipline_id" ) );
		
		if( $roleId != Share_Model_Role::INSTITUTION && $roleId != Share_Model_Role::SPECIALIST ){
			$select->join( array( "pg" => "$this->_schema.$this->_name" ) , "pg.class_id = g.id" , array()  )
			       ->where( "pg.person_id = ?" , $userId )
			       ->order( array( "pg.class_id" ) );
		}
        
        if( $roleId == Share_Model_Role::SPECIALIST ){
            $select->join( array( "dd" => "discipline_disponibility" ) , "d.id = dd.discipline_id" , array() , "station"  )
			       ->where( "dd.person_id = ?" , $userId );
        }
        
		$rows = $db->fetchAll( $select ); 
        
		$data = array();

        $idCourses       = array();
        $idDisciplines   = array();
        $idGroups        = array();

		foreach( $rows as $key => $row ){
			$data[$row['course_id']]['course'] = $row['course_title'];
			$data[$row['course_id']]['disciplines'][$row['discipline_id']]['discipline'] = $row['discipline_title'];
			$data[$row['course_id']]['disciplines'][$row['discipline_id']]['groups'][$row['id']]['group'] = $row['name'];
			
			$idCourses[] 	 = $row['course_id'];		
			$idDisciplines[] = $row['discipline_id'];
			$idGroups[]		 = $row['id']; 
		}

		return array( 
			'data'			=> $data ,
			'idCourses' 	=> array_unique( $idCourses ) ,
			'idDisciplines' => array_unique( $idDisciplines ),
			'idClass'	   	=> array_unique( $idGroups )
		);
		
	}

	public function forInClass( $person_id )
	{
		$query = $this->getAdapter()->select()
					   ->from( array( 'cp' => 'class_person' ) , 'class_id' , 'station' )
					   ->join( array( 'c' => 'class' ) , 'c.id = cp.class_id' , array() , 'station' )
					   ->where( 'person_id = ?' , $person_id )
					   ->where( 'using_elearning = ?' , Station_Model_ClassModel::YES )
                       ->where( 'status <> ?' , Station_Model_Status::ACTIVE );

		$result = $this->getAdapter()->fetchAll( $query );

        $return = array();

		foreach( $result as $rs ){
			$return[] = $rs['class_id'];
		}

		if ( !$return ){
			$return = array();
		}

		return join( "," , $return );
	}
}
