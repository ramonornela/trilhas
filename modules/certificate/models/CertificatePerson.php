<?
class CertificatePerson extends Table
{
	protected $_name    = "trails_certificate_person";
	protected $_primary = array( 'certificate_id' , 'person_id' );	
	
	public $filters = array(
		'*'  => 'StringTrim',
		'id' => 'Int'
	);
	
	protected $_dependentTables = array();
	
	protected $_referenceMap = array( 
		array(
			 'refTableClass' => 'Certificate',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'certificate_id' )
		),
		array(
			 'refTableClass' => 'Person',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'person_id' )
		) 
	);
	
	public function delete( $person_id , $certificate_id )
	{
		$db = $this->getAdapter();
		
		$where = $db->quoteInto( "person_id = ? " , $person_id );
		$where .= " AND " . $db->quoteInto( 'certificate_id = ?' , $certificate_id );

		$db->delete( $this->_name , $where );
	}
}