<?php
class Form extends Table
{
	protected $_name    = "trails_form";
	protected $_primary = "id";
	
	protected $_dependentTables = array( "FormGroupField" , "FormCoursePeriod" );
	
	public $filters = array(
		'*'	 => 'StringTrim',
	    'id' => 'Int'
	);
		
	public $validators = array(
	    'id'         => 'Int',
		'name'       => array( 'NotEmpty', array( 'StringLength', 0 , 255 ) ),
		'email'      => array( array( 'StringLength', 0 , 255 ) ),
		'subject'    => array( array( 'StringLength', 0 , 255 ) ),
		//'status'     => array( 'NotEmpty', array( 'StringLength', 0 , 1 ) ),
	);
	
	public function fetchFormCoursePeriod()
	{
		$select = $this->select();
		
		$select->from( array( 'f' => $this->_name ) , new Zend_Db_Expr('*') )
			   ->join( array( 'cf' => 'trails_course_form' ) , "f.id = cf.form_id" , array()  )
			   ->join( array( 'c' => 'trails_course' ) , "c.id = cf.course_id" , array() )
		       ->join( array( 'pf' => 'trails_period_form' ) , "f.id = pf.form_id" , array() )
		       ->join( array( 'p' => 'trails_period' ) , "p.id = pf.period_id" , array() );
		
		return $this->fetchAll( $select );
	}
	
	public function fetchFormCoursePeriodDateCurrent()
	{
		$select = $this->select();
		
		$select->from( array( 'f' => $this->_name ) , new Zend_Db_Expr('*') )
			   ->join( array( 'fcp' => 'trails_form_course_period' ) , "f.id = fcp.form_id" , array()  )
			   ->join( array( 'c' => 'trails_course' ) , "c.id = fcp.course_id" , array() )
		       ->join( array( 'p' => 'trails_period' ) , "p.id = fcp.period_id" , array() )
		       ->where( 'p.entered <= ?' , date('Y-m-d')  )
		       ->where( 'p.expired >= ?' , date("Y-m-d") )
		       ->where( "f.status = 'PB'" )
		       ->order( "p.entered" )
		       ->order( "c.title" );
		
		return $this->fetchAll( $select );
	}
}