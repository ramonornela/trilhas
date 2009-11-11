<?
class Application_Model_Logged extends Application_Model_Abstract
{
	protected $_name    = "logged";
	protected $_primary = "id";	
	
	public $filters = array(
		'*'  => 'StringTrim',
		'id' => 'Int'
	);
	
	public $validators = array( 
		'id'		=> array(  'Int' ,  'NotEmpty' ), 
		'created'		=> array(  'NotEmpty' ), 
		'discipline_id'		=> array(  'Int' ,  'NotEmpty' ), 
		'person_id'		=> array(  'Int' ,  'NotEmpty' ), 
		'chat_flag'		=> array(  'NotEmpty' ,  array( 'StringLength' , '0' , '1' ) ) 
	);
	
	protected $_dependentTables = array(  );
	
	protected $_referenceMap = array( 
		array(
			 'refTableClass' => 'Discipline',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'discipline_id' )
		),
		array(
			 'refTableClass' => 'Person',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'person_id' )
		) 
	);
}