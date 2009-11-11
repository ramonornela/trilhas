<?
class Station_Model_DiscountPerson extends Station_Model_Abstract
{
	protected $_schema   = "station";
	protected $_name     = "discount_person";
	protected $_primary  = array( "discount_id" , "discipline_id" , "person_id" , "class_id" );

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
		),
        array(
			 'refTableClass' => 'Share_Model_Person',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'discipline_id' )
		)
	);
}
