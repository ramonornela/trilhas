<?
class Station_Model_Discipline extends Station_Model_Abstract
{
    const ACTIVE 	  = 1;
	const INACTIVE 	  = 2;

	protected $_schema  = "station";
	protected $_name    = "discipline";
	protected $_primary = "id";	
	
	public $filters = array(
		'*'  => 'StringTrim',
		'id' => 'Int'
	);
	
	public $validators = array( 
		'id'		=> array(  'Int' ), 
		'title'		=> array(  'NotEmpty' ,  array( 'StringLength' , '0' , '255' ) ) 
	);
	
	protected $_dependentTables = array( "Certificate" , "Content_Model_Content" , "Application_Model_Group" , "Logged" , "Notepad_Model_Notepad" , "Station_Model_CourseDiscipline" );
	
	protected $_referenceMap = array( 
		array(
			 'refTableClass' => 'Course',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'course_id' )
		),
		array(
			 'refTableClass' => 'Person',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'person_id' )
		) 
	);
	
	
	public function fetchIn( $condOne , $condTwo )
	{		
        $select = $this->getAdapter()->select();
		
		$select->from( array ( 'd' => $this->_name ) , "cd.course_id" , "station" )
			   ->join( array( "cd" => "station.course_discipline" ) , "d.id = cd.discipline_id" , null )
               ->join( array( "c" => "station.course" ) , "c.id = cd.course_id" , null )
               ->where( 'c.status = ?' , Station_Model_Course::ACTIVE )
               ->where( $condOne )
			   ->where( $condTwo );
        
		$result = $this->getAdapter()->fetchAll( $select );

        $return = array();

		foreach( $result as $rs ){
			$return[] = $rs['course_id'];
		}
		
		if ( !$return ){
			$return = array();
		}
		
		$return = array_unique( $return );
		
		return join( "," , $return );
	}
	
	public function fetchByCourse( $id )
	{		
		$select = $this->getAdapter()->select();
		
		$select->from( array ( 'd' => $this->_name ) , "d.*" , "station" )
			   ->join( array( "cd" => "course_discipline" ) , "d.id = cd.discipline_id" , null , "station" )
			   ->where( "cd.course_id = ?" , $id )
			   ->order( "name" );
		
		return $this->getAdapter()->fetchAll( $select );
	}

    public function fetchForRegister( $discipline_id )
    {
        $where = $this->select();
        
        $where->from( array( "d" => $this->_name ) , new Zend_Db_Expr( "d.id , d.name , c.amount" ) , $this->_schema )
              ->joinLeft( array( "c" => "station.class" ) , "d.id = c.discipline_id" , array() )
              ->where( "d.id =?" , $discipline_id );
          
        return $this->fetchRow( $where );
    }
}