<?
class Certificate extends Table
{
	protected $_name    = "trails_certificate";
	protected $_primary = "id";	
	
	public $filters = array(
		'*'  => 'StringTrim',
		'id' => 'Int'
	);
	
	public $validators = array( 
		'id'			=> array( 'Int' ),
		'group_id'	=> array( 'Int' , 'NotEmpty' ), 
		'hours'			=> array( 'NotEmpty' ,  array( 'StringLength' , '0' , '10' ) ), 
		'started'       => array( 'NotEmpty', array( 'Date', "d/m/Y" ) , array( "dateDependency" , "finished_certificate" ) ), 
		'finished'      => array( 'NotEmpty', array( 'Date', "d/m/Y" ) ) 
	);
	
	protected $_dependentTables = array( "CertificatePerson" );
	
	protected $_referenceMap = array( 
		array(
			 'refTableClass' => 'Group',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'group_id' )
		) 
	);
	
	public function findByPerson( $group_id )
	{
		$user = new Zend_Session_Namespace( 'user' );
		$select = $this->select();
		
		$select->from( array( 'c' => $this->_name ) , array( new Zend_Db_Expr( 'p.name , d.title, c.hours , c.started , c.finished' ) ) )
			   ->join( array( 'cp' => 'trails_certificate_person' ) , 'c.id = certificate_id' , array() )
			   ->join( array( 'p' => 'trails_person' ) , 'p.id = cp.person_id' , array() )
			   ->join( array( 'g' => 'trails_group' ) , 'g.id = c.group_id' , array() )
			   ->join( array( 'd' => 'trails_discipline' ) , 'd.id = g.discipline_id' , array() )
			   ->where( 'c.group_id =?' , $group_id )
			   ->where( 'cp.person_id =?' , $user->person_id ); 
		
		return $this->fetchRow( $select );
	}
	
}