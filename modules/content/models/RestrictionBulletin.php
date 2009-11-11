<?php
class Content_Model_RestrictionBulletin extends Content_Model_Abstract
{
	protected $_name 	= 'restriction_bulletin';
    protected $_primary = 'id';

    protected $_restriction;
    
	public $filters = array(
		'*'	 => 'StringTrim',
	    'id' => 'Int',
		'note_restriction' => 'DefaultValue',
	);
		
	public $validators = array(
		'bulletin_group_id' => array( 'NotEmpty' , 'Int' ),
		'note' => array( 'NotEmpty' ),
	);
	
	protected $_referenceMap = array(
		array(
			 'refTableClass' => 'Content_Model_Restriction',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'restriction_id' )
		),
		array(
			 'refTableClass' => 'Bulletin_Model_BulletinGroup',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'bulletin_group_id' )
		),
	);
	
	public function fetchBulletinGroup()
	{
		$select = $this->select();
		
		$select->from( array( 'rb' => $this->_name ) , new Zend_Db_Expr('*') , 'trails' )
			   ->join( array( 'r' => 'trails.restriction' ) , "r.id = rb.restriction_id" , array() )
			   ->order('r.id');
			   
		return $this->fetchAll( $select );
	}
	
	public function fetchRestrictionByContent($in)
	{
		$user = new Zend_Session_Namespace('user');
		
		$select = $this->select()->setIntegrityCheck(false);
		
		$select->from( array('rb'=>$this->_name),'*','trails')
			   ->join( array('r'=>'restriction'),"r.id = rb.restriction_id",array(),'trails')
			   ->where( '(r.class_id = ?' , $user->group_id )
			   ->orWhere( 'r.class_id isnull )' )
			   ->where( 'content_id IN(' . $in  . ')' );
		
		return $this->fetchAll( $select );
	}
	

}