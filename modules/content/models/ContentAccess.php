<?php
/**
 * @author Preceptor Educa��o a Dist�ncia <contato@preceptoread.com.br>
 * @category Models
 * @package Content
 * @license http://www.preceptoread.com.br
 * @version 4.0
 * @final 
 */
class ContentAccess extends Table
{
	/**
	 * table name
	 * 
	 * @var string $_name
	 * @access protected
	 */
	protected $_name    = "trails_content_access";
	
	/**
	 * primary key 
	 * 
	 * @var string $_primary
	 * @access protected
	 */
	protected $_primary = "id";	
	
	/**
	 * @var array $filters
	 * @access public
	 */
	public $filters = array(
		'*'  => array( 'StringTrim' , 'Int' )
	);
	
	/**
	 * validators model
	 * 
	 * @var array $validators
	 * @access public
	 */
	public $validators = array( 
		'id'		 => array( 'Int' ), 
		'content_id' => array( 'Int' ,  'NotEmpty' ) ,
		'person_id'  => array( 'Int' ,  'NotEmpty' )
	);
	
	/**
	 * tables dependent this 
	 * 
	 * @var array $_dependentTables
	 * @access protected
	 */
	protected $_dependentTables = array();
	
	/**
	 * configuration 
	 * 
	 * @var array $_referenceMap
	 * @access public
	 */
	protected $_referenceMap = array( 
		array(
			 'refTableClass' => 'Content',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'content_id' )
		),
		array(
			 'refTableClass' => 'Person',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'person_id' )
		) 
	);
	
	public function countByPerson( $discipline_id , $person_id = null )
	{
		$user = new Zend_Session_Namespace('user');
		
		$select = $this->getAdapter()->select()->from( array( "ca" => $this->_name ), "c.id" )
											   ->distinct( true )
											   ->join( array( "c" => "trails_content" ) , "c.id = ca.content_id" , null )
											   ->where( "c.discipline_id = ?" , $discipline_id );
        if( $person_id ){
            $select->where( "person_id = ?" , $person_id );
        }else{
            $select->where( "person_id = ?" , $user->person_id );
        }
		$rs = $this->getAdapter()->fetchAll( $select );
		
		return count($rs);
	}

    public function lastContent()
    {
        $user = new Zend_Session_Namespace('user');
        $select = $this->select();
        
        $select->from( array( "ca" => $this->_name ), new Zend_Db_Expr("c.id") )
               ->join( array( "c" => "trails_content" ) , "c.id = ca.content_id" , null )
               ->where( "person_id = ?" , $user->person_id )
               ->order( 'ca.id DESC' )
               ->limit(1);

        $rs = $this->fetchRow( $select );

        return $rs['id'];
    }
}