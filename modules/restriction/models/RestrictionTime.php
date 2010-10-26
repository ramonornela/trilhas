<?php
class RestrictionTime extends Table 
{
	protected $_name 	= 'trails_restriction_time';
    protected $_primary = 'id';


	public $filters = array(
		'*'	 => 'StringTrim',
	    'id' => 'Int'
	);
		
	public $validators = array(
	    'id'   => 'Int',
		'started'  => array( array( 'Date', "Y-m-d" ) , array( "dateDependency" , "finished_restriction" ) ),
		'finished' => array( array( 'Date', "Y-m-d" ) )
	);
	
	protected $_referenceMap = array(
		array(
			 'refTableClass' => 'Restriction',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'restriction_id' )
		)
	);
	
	public function fetchTime()
	{
		$select = $this->select();
		
		$select->from( array( 'rt' => $this->_name ) , new Zend_Db_Expr('*') )
			   ->join( array( 'r' => 'trails_restriction' ) , "r.id = rt.restriction_id" , array() )
			   ->order( 'r.id' );	
		return $this->fetchAll( $select );
	}
}