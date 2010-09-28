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
	/**
	 * load models method init class parent
	 *  
	 * @var array $uses
	 * @access public
	 */
	

	/**
	 * @access public
	 * @return void
	 */
	public function indexAction()
	{ 
		$user = new Zend_Session_Namespace("user");
		
		//$rs  = $this->Content->fetchAll( $this->mountSelect( $user->discipline_id , "id , title , content_id" ) )->toArray();
		$rs = $this->Content->fetchAll( array ( "discipline_id = ?" => $user->discipline_id ) , array( "content_id" , "position" , "id" ) )->toArray();
		
		$this->view->contents = $this->Content->organize( $rs );
		
		$this->render();
	}
	
	/**
	 * @access public
	 * @return void
	 */
	public function viewAction()
	{
		$user = new Zend_Session_Namespace("user");
		
		$id = Zend_Filter::filterStatic( $this->_getParam( 'id' ) , 'Int' );
        $all = Zend_Filter::filterStatic( $this->_getParam( 'all' ) , 'HtmlEntities' );

        //print the whole course, else, print the selected text
        if( isset( $all ) && $all ){
            $result = $this->Content->fetchAll( array( 'discipline_id =?' => $user->discipline_id , 'content_id is null' ) , array( "content_id" , "position" , "id" ) )->toArray();
            $data = array();
            foreach( $result as $rs ){
                $data[] = $this->mountSelect( $rs['id'] , $content );
            }
            $this->view->contents = $data;
        }else{
            $content = $this->Content->fetchRow( array( 'id =?' => $id) )->toArray();
            $this->view->contents = $this->mountSelect( $id , $content );
        }
        
		$this->render( null , "print" );
	}
	
	/**
	 * @access private
	 * @return array
	 */
	private function mountSelect( $id , $datas = null )
	{
		$data = $this->Content->fetchAll( array( 'content_id =?' => $id ) , array( "content_id" , "position" , "id" ) )->toArray();
		
        foreach ( $data as $value ) {
        	if ( $value['content_id'] ){
				$datas[] = $this->mountSelect( $value['id'] , $value );
			}
        }
		
		return $datas;
	}

}

