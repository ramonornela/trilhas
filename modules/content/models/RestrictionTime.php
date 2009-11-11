<?php
class Content_Model_RestrictionTime extends Content_Model_Abstract
{
	protected $_name 	= 'restriction_time';
    protected $_primary = 'id';


	public $filters = array(
		'*'	 => 'StringTrim',
	    'id' => 'Int',
		'started'	=> array( 'Date' ),
		'finished'	=> array( 'Date' )
	);
		
	public $validators = array(
		'started'  => array( 'NotEmpty' , 'Date' ),
		'finished' => array( 'NotEmpty' , 'Date' , array( "dateGreaterThan" , "Content_Model_RestrictionTime-started" ) )
	);
	
	protected $_referenceMap = array(
		array(
			 'refTableClass' => 'Content_Model_Restriction',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'restriction_id' )
		)
	);
	
	public function fetchTime()
	{
		$select = $this->select()->setIntegrityCheck(false);
		
		$select->from( array( 'rt' => $this->_name ) , '*' , 'trails' )
			   ->join( array( 'r' => 'trails.restriction' ),
					   "r.id = rt.restriction_id" , array() , 'trails' )
			   ->order( 'r.id' );

		return $this->fetchAll( $select );
	}
}