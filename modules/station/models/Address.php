<?
class Station_Model_Address extends Station_Model_Abstract
{
	protected $_schema  = "station";
	protected $_name    = "adress";
	protected $_primary = "id";

    public $validators = array(
		'complement' => array( 'NotEmpty' )
    );
}