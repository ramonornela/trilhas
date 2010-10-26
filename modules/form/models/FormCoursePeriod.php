<?
class FormCoursePeriod extends Table
{
	protected $_name    = "trails_form_course_period";
	protected $_primary = array( "course_id" , "form_id" , "period_id" );	
	
	public $filters = array(
		'*'  => 'StringTrim',
		'id' => 'Int'
	);
	
	public $validators = array( 
		'course_id' => array(  'Int' ,  'NotEmpty' ), 
		'form_id'   => array(  'Int' ,  'NotEmpty' ) 
	);
	
	protected $_referenceMap = array( 
		array(
			 'refTableClass' => 'Course',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'course_id' )
		),
		array(
			 'refTableClass' => 'Period',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'period_id' )
		),
		array(
			 'refTableClass' => 'Form',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'form_id' )
		)
	);
	
	public function save( $id )
	{
		$this->delete( $id , 'form_id' );
	
		$relation = Zend_Json::decode( urldecode( $_POST["course_period"] ) );
		
		for( $i = 0; $i < count( $relation ); $i += 2 )
    	{
    		foreach( $relation[($i+1)] as $periodId )
    		{
    			$save['course_id'] = $relation[$i];
    			$save['period_id'] = $periodId;
    			$save['form_id']   = $id;	 
    			
				parent::save( $save );
    		}
    	}
	}
	
	public function fetchPerson( $form_id )
	{
		$select = $this->select();
		
		$select->from( array( 'fcp' => $this->_name ) , new Zend_Db_Expr('*') )
			   ->join( array( 's' => 'trails_signup' ) , "fcp.id = c.course_id" , array() )
			   ->where( 'form_id =?' , $form_id );
		debug( $select->__toString() , 1 );   
		return $this->fetchAll( $select );
		
	}
}