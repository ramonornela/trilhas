<?php
class Period extends Table 
{
	protected $_name 	= 'trails_period';
    protected $_primary = 'id';

	public $filters = array(
		'*'	 => 'StringTrim',
	    'id' => 'Int'
	);
		
	public $validators = array(
	    'id'   => 'Int',
		'entered' => array( 'NotEmpty', array( 'Date', "Y-m-d" ) , array( "dateDependency" , "expired_period" ) ),
		'expired' => array( 'NotEmpty', array( 'Date', "Y-m-d" ) )
	);
	
	protected $_dependentTables = array( "FormCoursePeriod" );
	
	public function fetchPeriodDateCurrent( $courseId , $periodId , $formId )
	{
		$select = $this->select();
		if ( ! $courseId ) $courseId = 0;
		if ( ! $periodId ) $periodId = 0;
		if ( ! $formId )   $formId = 0;
		
		$select->from( array( 'p' => $this->_name ) , new Zend_Db_Expr('*') )
			   ->join( array( 'fcp' => 'trails_form_course_period' ) , "p.id = fcp.period_id" , array()  )
			   ->join( array( 'f' => 'trails_form' ) , "fcp.form_id = f.id" , array() )
		       ->where( "p.entered <= ?" , date('Y-m-d')  )
		       ->where( "p.expired >= ?" , date("Y-m-d") )
		       ->where( "f.status = 'PB'" )
		       ->where( "fcp.course_id =?" , $courseId )
		       ->where( "p.id  =?" , $periodId )
		       ->where( "fcp.form_id   =?" , $formId );
		
		return $this->fetchRow( $select );
	}
	
	public function findPeriodCourse( $courseId , $formId = null )
	{
		$select  = $this->select();
		
		$select->from( array( 'p' => $this->_name ) , new Zend_Db_Expr('period_id , form_id') )
		       ->join( array( 'fcp' => 'trails_form_course_period' ) , "p.id = fcp.period_id" , array()  )
			   ->where( "fcp.course_id =?" , $courseId );
		 
		$nots = $this->fetchAll( $select )->toArray();
		
		foreach ( $nots as $not )
		{
			if ( $formId != $not['form_id'] )
				$notIn[] = $not['period_id'];
		}
		
		if ( ! $notIn )
			$notIn = array(0);
			
		$select2 = $this->select();
		
		$select2->from( array( 'p' => $this->_name ) , new Zend_Db_Expr('*') )
		        ->where( "id not in(" . join( "," , $notIn ) . ")" );
		
		return $this->fetchAll( $select2 );
	}

    public function fetchFormCoursePeriod($form_id, $course_id)
	{
		$select = $this->select();

		$select->from( array( 'f' => 'trails_form') , '*')
               ->setIntegrityCheck(false)
		       ->join( array( 'pf' => 'trails_form_course_period' ) , "f.id = pf.form_id")
		       ->join( array( 'p' => 'trails_period' ) , "p.id = pf.period_id")
               ->where('form_id = ?', $form_id)
               ->where('course_id =?' , $course_id);

		return $this->fetchAll($select);
	}
}