<?
class Station_Model_CourseDiscipline extends Station_Model_Abstract
{
	protected $_schema  = "station";
	protected $_name    = "course_discipline";
	protected $_primary = array( "course_id" , "discipline_id" );	
	
	public $filters = array(
		'*'  => 'StringTrim',
		'id' => 'Int'
	);
	
	public $validators = array( 
		'course_id'		=> array(  'Int' ,  'NotEmpty' ),
		'discipline_id' => array(  'Int' ,  'NotEmpty' ) 		 
	);
	
	protected $_referenceMap = array( 
		array(
			 'refTableClass' => 'Station_Model_Course',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'course_id' )
		),
		array(
			 'refTableClass' => 'Station_Model_Discipline',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'discipline_id' )
		) 
	);
}