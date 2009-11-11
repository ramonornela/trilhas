<?
class Station_Model_PersonAddress extends Station_Model_Abstract
{
	protected $_schema  = "station";
	protected $_name    = "person_adress";
	protected $_primary = array( "adress_id" , "person_id" );

	public $filters = array(
		'*'  => 'StringTrim',
		'adress_id' => 'Int',
		'person_id' => 'Int'
	);

	public $validators = array(
		'adress_id'		=> array(  'Int' ,  'NotEmpty' ),
		'person_id'		=> array(  'Int' ,  'NotEmpty' )
	);

	protected $_dependentTables = array(  );

	protected $_referenceMap = array(
		array(
			 'refTableClass' => 'Share_Model_Person',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'person_id' )
		),
		array(
			 'refTableClass' => 'Station_Model_Adress',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'adress_id' )
		)
	);
}