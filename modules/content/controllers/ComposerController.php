<?php
/**
 * @author Preceptor Educação a Distância <contato@preceptoread.com.br>
 * @category Controllers
 * @package Composer
 * @version 4.0
 * @license http://www.preceptoread.com.br
 * @final 
 */
class Content_ComposerController extends Application_Controller_Abstract
{
	
	protected $_model= "Content_Model_Content";
	/**
	 * @access public
	 * @return void
	 * @final 
	 */
	public function indexAction()
	{
		$id = Zend_Filter::filterStatic( $this->_getParam("id") , "int" );
		$go	= $this->_getParam("go");
		$user = new Zend_Session_Namespace("user");

		$content = new Content_Model_Content();

		if( !$user->contents ){
			$user->contents = Zend_Json::encode(
				$content->fetchAllOrganize( $user->discipline_id ) );
		}

		$this->view->contents = $user->contents;

		$this->view->go = $go;

		if( $id ){
			$this->view->current = $content->getPositionById( $id ,
										  Zend_Json::decode($user->contents) );
		}
        
        $this->_helper->layout->setLayout('clear');
	}
	
	/**
	 * @access public
	 * @return void
	 * @final 
	 */
	public function inputAction()
	{
		$id   	= Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
		$parent = $this->_getParam( "parent" ) ? true : false;
		
		$user      = new Zend_Session_Namespace("user");
		$content   = new Content_Model_Content();

		if( !$user->contents ){
			$user->contents = Zend_Json::encode(
				$content->fetchAllOrganize( $user->discipline_id ) );
		}
		
		if( $id ){
			$this->view->data 	   = $content->find( $id )->current();
		}
		
		$this->view->contents 	  = $this->toSelectContent( Zend_Json::decode($user->contents) );
		$this->view->jsonValidate = Zend_Json::encode( $content->validators );
		
		$this->_helper->layout->setLayout('clear');
	}
	
	/**
	 * @access public
	 * @return void
	 * @final
	 */
	public function saveAction()
	{
		$user    = new Zend_Session_Namespace("user");
		$content = new Content_Model_Content();

//		$processed = $this->uploadNarration();
//		if( $processed ){

		$_POST['data']['Content_Model_Content']['ds'] = tidy_repair_string(
			$_POST['data']['Content_Model_Content']['ds'],
			array(
				'hide-comments' => true,
				'drop-proprietary-attributes' => true,
				'bare' => true,
				'word-2000' => true,
				'logical-emphasis' => true
			),
			"utf8" );

		if( !$_POST['data']['Content_Model_Content']['content_id'] ){
			unset( $_POST['data']['Content_Model_Content']['content_id'] );
		}

		$_POST['data']['Content_Model_Content']['discipline_id'] = $user->discipline_id;

		$result = $content->save( $_POST['data'] );

		$this->_helper->_flashMessenger->addMessage( $result->message );

		if( $result->error ){
			$this->_redirect( "/content/composer/input/id/" .
				Zend_Filter::filterStatic( $_POST['data']['Content_Model_Content']['id'] , 'Int' ) );
		}else{
			$user->contents = false;
			$this->_redirect( '/content/composer/input/id/'.$result->detail['id']);
		}
	}
	
	/**
	 * @access public
	 * @return void
	 * @final 
	 */
	public function deleteAction()
	{
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );

		$user          = new Zend_Session_Namespace("user");
		$content 	   = new Content_Model_Content();
		$contentAccess = new Content_Model_ContentAccess();
		
		try{
			if( $id ){
				$contentAccess->delete( array( 'content_id' => $id ) );
				$content->delete( $id );

				$user->contents = false;
				
				$this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted successfully" ) );
			}
		}catch( Exception $e ){
			$this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted error" ) );
		}
		
		$this->_redirect( '/content/composer/index/go/previous' );
	}
	
	/**
	 * @param array $data
	 * @param array $xhtml
	 * @param null|string $line
	 * @access public
	 * @return array
	 */
	private function toSelectContent( $data )
	{
		$select = array( "0" => "Principal" );
		foreach( $data as $key => $val ){
			$select[$val['id']] = str_repeat('- ', $val['level']) . $val['title'];
		}
		
		return $select;
	}
	
	/**
	 * @access private
	 * @return bool
	 */
	private function uploadNarration()
	{
		if( $processed !== true ){
			$narration = new Content_Model_Narration();
			$file = new File_Model_File();

			$row = $narration->fetchRow( array( "content_id = ?" => $processed ) );
			$narration->delete( array( 'content_id' => $processed ) );

			if( $row ){
				$file->delete( $row->file_id );
			}

			$dataNarration = array(
				"Content_Model_Narration" => array(
					"file_id" => $processed,
					"content_id" => $result->detail['id'],
				)
			);

			$narration->save( $dataNarration );
		}
		
		if( $_FILES['narration']['name'] ){
			Zend_Loader::loadClass( "Upload" , DIR_LIBRARY . "/Upload/" );

			$upload = new Upload( $_FILES['narration'] );
			$user = new Zend_Session_Namespace( 'user' );
			$file = new File_Model_File();

			if ( $upload->processed ){
				if( !$upload->file_src_size ){
					$this->_helper->_flashMessenger->addMessage( $this->view->translate( "file size exceeded" ) );
					return false;
				}
				
				if( !( $upload->file_src_mime == "audio/mpeg" ) ){
					$this->_helper->_flashMessenger->addMessage( $this->view->translate( "extension invalid" ) );
					return false;
				}
				
				$upload->process( UPLOAD );
				
				if( !$upload->processed ){
					$this->_helper->_flashMessenger->addMessage( $this->view->translate( "error upload" ) );
					return false;
				}
					
				$upload->clean();
				
				$data['title'] 	   = substr( $_FILES['narration']['name'] , 0 , strrpos( $_FILES['narration']['name']  , "." ) );
				$data['location']  = $upload->file_dst_name;
				$data['person_id'] = $user->person_id;
				$data['type']	   = File_Model_File::TYPE_FILE_NARRATION;
					
				$result = $file->save( array( "File_Model_File" => $data ) );
					
				if ( $result->error ){
					return false;
				}

				return $result->detail['id'];
			}
			return true;
		}
		return true;
	}
	
	/**
	 * delete narration content 
	 * 
	 * @access public
	 * @return void
	 * @final 
	 */
	public function deleteNarrationAction()
	{
		$file_id    = Zend_Filter::filterStatic( $this->_getParam( 'file' ) ,  'Int' );
		$content = Zend_Filter::filterStatic( $this->_getParam( 'content' ) ,  'Int' );

		$narration = new Content_Model_Narration();
		$file = new File_Model_File();
				
		$narration->delete( array( 'file_id'=>$file_id ) );
		$file->delete( $file );
		
		$this->_redirect( "/content/composer/index/id/{$content}" );
	}
}
