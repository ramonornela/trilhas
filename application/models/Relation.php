<?
class Application_Model_Relation extends Application_Model_Abstract
{
	protected $_name    = "relation";
	protected $_primary = "id";	
	
	public $filters = array(
		'*'  => 'StringTrim',
		'id' => 'Int'
	);
	
	public $validators = array( 
		'id'		=> array(  'Int' ,  'NotEmpty' ), 
		'relation'		=> array(  'NotEmpty' ,  array( 'StringLength' , '0' , '12' ) ), 
		'group_id'		=> array(  'Int' ), 
		'discipline_id'	=> array(  'Int' ), 
		'course_id'		=> array(  'Int' ), 
		'person_id'		=> array(  'Int' ,  'NotEmpty' ) 
	);
	
	protected $_dependentTables = array(  );
	
	protected $_referenceMap = array( 
		array(
			 'refTableClass' => 'Group',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'group_id' )
		),
		array(
			 'refTableClass' => 'Course',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'course_id' )
		),
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

    public function saveRelation()
    {
        $result = Zend_Json::decode( $_POST['json_relation'] );
        $date = mktime();
        
		foreach( $result as $key => $val )
		{
			$data['Application_Model_Relation']['relation'] = $date;
			$data['Application_Model_Relation'][$val['type']] = $val['id'];
            
			$this->save( $data , false );

			$data = null;
		}
        
        return $date;
    }
}