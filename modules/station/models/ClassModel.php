<?
class Station_Model_ClassModel extends Station_Model_Abstract
{
    const ACTIVE   = 1;
	const INACTIVE = 2;
    const YES	   = 'Y';
	const NO       = 'N';
	const PARTIAL  = 'P';

	protected $_schema  = "station";
	protected $_name    = "class";
	protected $_primary = "id";	
	
	public $filters = array(
		'*'  => 'StringTrim',
		'id' => 'Int'
	);
	
	public $validators = array( 
		'id'		    => array( 'Int' ), 
		'title'		    => array( 'NotEmpty' ,  array( 'StringLength' , '0' , '255' ) ), 
		'discipline_id' => array(  'Int' ,  'NotEmpty' ) 
	);
	
	protected $_dependentTables = array('Application_Model_Relation',
										'Certificate_Model_Certificate',
										'Content_Model_Restriction',
                                        'Station_Model_ClassPerson');
	
	protected $_referenceMap = array( 
		array(
			 'refTableClass' => 'Station_Model_Discipline',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'discipline_id' )
		),
		array(
			 'refTableClass' => 'Share_Model_Person',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'discipline_disponibility_id' )
		),
		array(
			 'refTableClass' => 'Station_Model_EmissionDocument',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'document_emission_id' )
		)

	);

    public function findByCourse( $course_id , $groups )
	{
		$select = $this->select();

		$select->from( array( 'g' => $this->_schema . "." . $this->_name ) , array( new Zend_Db_Expr( 'g.id , g.name as group_title, d.name' ) ) )
			   ->join( array( 'd' => 'station.discipline' ) , 'g.discipline_id = d.id' , array() )
			   ->join( array( 'cd' => 'station.course_discipline' ) , 'cd.discipline_id = d.id' , array() )
               ->where( 'cd.course_id = ?' , $course_id )
               ->where( 'g.id IN('.$groups.')' )
			   ->order( 'g.name' );
		
		$result = $this->fetchAll( $select );

        foreach( $result as $rs ){
            $return[] = array( 'id' => $rs->id,
                               'name' => $rs->name . ' - ' . $rs->group_title );
        }

        return $return;
	}
	
	public function findByDisciplineByCourse( $group_id , $discipline_id )
	{
		$select = $this->select();

		$select->from( array( 'g' => $this->_schema . "." . $this->_name ) , array( new Zend_Db_Expr( 'g.id , d.title , c.title' ) ) )
			   ->join( array( 'd' => 'station.discipline' ) , 'g.discipline_id = d.id' , array() )
			   ->join( array( 'cd' => 'station.course_discipline' ) , 'cd.discipline_id = d.id' , array() )
			   ->join( array( 'c' => 'station.course' ) , 'cd.course_id = c.id' , array() )
               ->where( 'g.id = ?' , $group_id )
               ->where( 'd.id = ?' , $discipline_id );
		
		$result = $this->fetchRow( $select );
	}

    public function fetchClassAvailable( $discipline_id , $class_id = null )
    {
        $where = $this->select()->setIntegrityCheck(false);

        $where->where( 'registration_available = ?' , Station_Model_Status::ACTIVE )
              ->where( 'discipline_id = ?' , $discipline_id );

        if( $class_id ){
            $where->where( 'id = ?' , $class_id );
        }

        return $this->fetchAll( $where );
    }
}