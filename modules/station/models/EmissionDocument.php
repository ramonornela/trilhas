<?
class Station_Model_EmissionDocument extends Station_Model_Abstract
{
	protected $_schema  = "station";
	protected $_name    = "document_emission";
	protected $_primary = "id";

	public $filters = array(
		'*'  => 'StringTrim',
		'id' => 'Int'
	);

	protected $_dependentTables = array( 'Station_Model_ClassModel' );

	protected $_referenceMap = array(
	);
}