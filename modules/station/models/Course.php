<?
/**
 * @author Preceptor Educação a Distância <contato@preceptoread.com.br>
 * @category Models
 * @package Course
 * @version 4.0
 * @final 
 */
class Station_Model_Course extends Station_Model_Abstract
{
    const ACTIVE 	  = 1;
	const INACTIVE 	  = 2;
    const EAD         = 1;
    
	protected $_schema  = "station";
	protected $_name    = "course";
	protected $_primary = "id";	
	
	public $filters = array(
		'*'  => 'StringTrim',
		'id' => 'Int'
	);
	
	public $validators = array( 
		'id'		=> array( 'Int' ), 
		'title'		=> array( 'NotEmpty' ,  array( 'StringLength' , '0' , '255' ) )
	);
	
	protected $_dependentTables = array( "Discipline" , "CourseDiscipline" );
	
	protected $_referenceMap = array( 
		array(
			 'refTableClass' => 'Person',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'person_id' )
		) 
	);
	
	
	
}