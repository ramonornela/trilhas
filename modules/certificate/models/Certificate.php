<?
class Certificate_Model_Certificate extends Certificate_Model_Abstract
{
	protected $_name    = "certificate";
	protected $_primary = "id";	
	
	public $filters = array(
		'*'  => 'StringTrim',
		'id' => 'Int'
	);
	
	public $validators = array( 
		'discipline_id'	=> array( 'Int' , 'NotEmpty' ), 
		'hours'			=> array( 'NotEmpty' ,  array( 'StringLength' , '0' , '10' ) ), 
		'started'       => array( 'NotEmpty', array( 'Date', "Y-m-d" ) , array( "dateDependency" , "finished_certificate" ) ), 
		'finished'      => array( 'NotEmpty', array( 'Date', "Y-m-d" ) ) 
	);
	
	protected $_dependentTables = array( "Certificate_Model_CertificatePerson" );
	
	protected $_referenceMap = array( 
		array(
			 'refTableClass' => 'Staton_Model_ClassModel',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'class_id' )
		) 
	);
	
	public function findByPerson( $group_id )
	{
		$user = new Zend_Session_Namespace( 'user' );
		$select = $this->select();
		
		$select->from( array( 'c' => $this->_name ) , array( new Zend_Db_Expr( 'p.name , d.title, c.hours , c.started , c.finished' ) ) )
			   ->join( array( 'cp' => 'trails.certificate_person' ) , 'c.id = certificate_id' , array() )
			   ->join( array( 'p' => 'station_person' ) , 'p.id = cp.person_id' , array() )
			   ->join( array( 'g' => 'station_class' ) , 'g.id = c.class_id' , array() )
			   ->join( array( 'd' => 'station_discipline' ) , 'd.id = g.discipline_id' , array() )
			   ->where( 'c.class_id =?' , $group_id )
			   ->where( 'cp.person_id =?' , $user->person_id ); 
		
		return $this->fetchRow( $select );
	}
	
}