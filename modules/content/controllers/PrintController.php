<?php
/**
 * @author Preceptor Educação a Distância <contato@preceptoread.com.br>
 * @package Composer
 * @subpackage Print
 * @category Controller
 * @license http://www.preceptoread.com.br
 * @version 4.0
 * @final 
 */
class Composer_PrintController extends Controller 
{
	protected $_model = false;
	/**
	 * load models method init class parent
	 *  
	 * @var array $uses
	 * @access public
	 */
	//public $uses = array( "Content" );

	/**
	 * @access public
	 * @return void
	 */
	public function indexAction()
	{ 
		$user = new Zend_Session_Namespace("user");

		$select = $this->mountSelect( $user->discipline_id , 
									  array( "id" , "title" , "content_id" ) );
								  
		$rs  = $this->Content->fetchAll( $select )->toArray();
		
		$this->view->contents = $this->Content->organize( $rs );
	}
	
	/**
	 * @access public
	 * @return void
	 */
	public function viewAction()
	{
		$user = new Zend_Session_Namespace("user");
		$id = Zend_Filter::filterStatic( $this->_getParam( 'id' ) , 'Int' );

		$select = $this->mountSelect( $user->discipline_id ,
									  array( "tcs.id AS self_id",
										     "tcs.title AS self_title",
										     "tcs.content_id AS self_content_id",
										     "tcs.ds AS self_ds" , "tc.*" ),
									  $id );
							
		$rs = $this->Content->fetchAll( $select )->toArray();

		if( !$rs ){
			$this->view->contents = array( array( 'value' => $this->Content->fetchRow( array( "id = ?" => $id ))->toArray() ) );
		}else{
			$this->view->contents = $this->Content->organizePrint( $rs , $id );
		}
		
		$this->_helper->layout->setLayout('clear');
	}
	
	/**
	 * return select table content 
	 * 
	 * @param int $discipline => id discipline
	 * @param mixed $columns => columns apresentation select
	 * @param null|int $content => id content 
	 * @access private
	 * @return object Zend_Db_Table_Select 
	 */
	private function mountSelect( $discipline , $columns , $content_id = null )
	{
		$select = $this->Content->select()->setIntegrityCheck(false)
										  ->from( array( "tcs" => "content" ) , $columns , 'trails' )
										  ->where( "tcs.discipline_id = ?" , Zend_Filter::filterStatic( $discipline , 'Int' ) )
										  ->order( array( "tcs.content_id" , "tcs.position" , "tcs.id" ) ); 

		if( $content_id ){
			$select->join( array( "tc" => "trails.content" ) , "tcs.id = tc.content_id" , array() )
				   ->where( "( tcs.content_id = ?" , $content_id )
				   ->orWhere( "tcs.id = ? )" , $content_id );
		
		}
		
		return $select;
	}
}

