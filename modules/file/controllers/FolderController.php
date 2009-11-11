<?php
class File_FolderController extends Application_Controller_Abstract
{
	protected $_model = "File_Model_Folder";
	
	public function indexAction()
	{
		$id = Zend_Filter::filterStatic( $this->_getParam( 'id' ) , 'int' );

		$folder = new File_Model_Folder();
		
		$config = array(
			"default" => File_Model_Folder::DEFAULT_FOLDER_COMPOSER,
			"id" 	  => $id,
			"module"  => File_Model_Folder::FLAG_COMPOSER,
			"div"	  => "file-folder",
			"uri"     => $this->view->url . "/file/folder/index/id/"
		);

		$values = $folder->getFoldersFiles( $config );

		$this->view->rs 	  = $values['rs'];
		$this->view->location = $values['location'];
		$this->view->files 	  = $values['files'];
		$this->view->dirs 	  = $values['folders'];
		$this->view->user 	  = $values['user'];
	}

	public function inputAction()
	{
		$id = Zend_Filter::get( $this->_getParam( 'id' ) , 'Int' );
		
		$folder = new File_Model_Folder();

		$this->view->visible = File_Model_Folder::VISIBILITY_PUBLIC;

		$this->view->data->visibility = null;
		$this->view->data->folder_id  = Zend_Filter::get( $this->_getParam( 'folder_id' ) , 'Int' );
		$this->view->jsonValidate     = Zend_Json::encode( $folder->validators );

		if( $id ){
			$this->view->data = $folder->find( $id )->current();
		}
	}

	public function inputFileAction()
	{
		$id = Zend_Filter::filterStatic( $this->_getParam( 'id' ) , 'int' );
		$this->_model = "File_Model_File";

		$file = new File_Model_File();

		$this->view->folder_id = Zend_Filter::filterStatic( $this->_getParam( 'folder_id' ) , 'int' );

		if( $id ){
			$this->view->data = $file->find( $id )->current();
		}

		$this->view->model = $this->_model;
	}

	public function saveFileAction()
	{
		$this->_model = "File_Model_File";
		Zend_Loader::loadClass( "Upload" , "Upload" );

		$user   = new Zend_Session_NameSpace( 'user' );

		$file = new File_Model_File();
		$folderFile = new File_Model_FolderFile();
		
		$data   = $_POST['data'];
        
        if( !$data['File_Model_File']['id'] ){
            $upload = new Upload( $_FILES['file'] );
        }else{
            $upload = new Upload( "" );
        }
        
		if ( $upload->processed && !$data['File_Model_File']['id'] ){
            
			if( !$upload->file_src_size ){
				$this->getDataViewError( "file size exceeded" );
				return false;
			}

			$name = $_FILES['file']['name'];

			$data['File_Model_File']['title'] 	  = substr( $name , 0 , strrpos( $name , "." ) );
			$data['File_Model_File']['location'] = $this->uploadFile( $upload , $data );

			if( !$data['File_Model_File']['title'] ){
				$this->getDataViewError( "file required" );
				return false;
			}

			if( !$data['File_Model_File']['location'] ){
				$this->getDataViewError( "error upload" );
				return false;
			}

			$upload->clean();
		}

		$data['File_Model_File']['type']		= File_Model_File::TYPE_FILE_COMPOSER;
		$data['File_Model_File']['person_id'] 	= $user->person_id;
        
		$result = $file->save( $data );
        $this->_helper->_flashMessenger->addMessage( $result->message );

        if( $result->error ){
            $this->_redirect( "/file/folder/input-file/folder_id/" .
				$data['File_Model_File']['folder_id'] , array( 'prependBase' => true ) );
        }else{
        	if( !$data['File_Model_File']['id'] ){
				$data = null;
				$data['File_Model_FolderFile']['file_id']   = $result->detail['id'];
				$data['File_Model_FolderFile']['folder_id'] = $_POST['data']['File_Model_File']['folder_id'];

				$folderFile->save( $data );
			}
        }

        $this->_redirect( "/file/folder/index/id/" . $data['File']['folder_id'] );
	}


	public function deleteFileAction()
	{
		$id 	   = Zend_Filter::filterStatic( $this->_getParam('id') , 'int' );
   		$folder_id = Zend_Filter::filterStatic( $this->_getParam( "folder_id" ) , 'int' );

		$folder = new File_Model_Folder();
		$file   = new File_Model_File();
		$folderFile = new File_Model_FolderFile();
		
   		try
   		{
   			$folderFile->delete( array( 'file_id' => $id ) );
			$file->delete( $id );

			$this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted successfully" ) );

			$this->_redirect( '/file/folder/index/id/' . $folder_id );
   		}
   		catch( Exception $e )
   		{
   			$this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted error" ) );
   			$this->_redirect( '/file/folder/index/id/' . $folder_id );
   		}
	}

	public function deleteAction()
	{
		$id 	   = Zend_Filter::filterStatic( $this->_getParam('id') , 'int' );
   		$folder_id = Zend_Filter::filterStatic( $this->_getParam( "folder_id" ) , 'int' );

		$folder = new File_Model_Folder();
		
		try
   		{
   			$msg = $this->view->translate( "deleted error" );

   			if( $folder->allowDelete( $id ) ){
   				$folder->delete( $id );
   				$msg = $this->view->translate( "deleted successfully" );
   			}
			
   			$this->_helper->_flashMessenger->addMessage( $msg );
			$this->_redirect( '/file/folder/index/id/' . $folder_id );
   		}
   		catch( Exception $e )
   		{
   			$this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted error" ) );
   			$this->_redirect( '/file/folder/index/id/' . $folder_id );
   		}
	}


	public function uploadFile( $upload , $data )
	{
		if( $upload->file_src_mime == "image/jpeg" 
			|| $upload->file_src_mime == "image/pjpeg"
			||  $upload->file_src_mime == "image/png"
			|| $upload->file_src_mime == "image/x-png"
			|| $upload->file_src_mime == "image/gif"  )
		{
			$upload->image_resize = true;
        	$upload->image_ratio_y = true;
			$upload->image_x = '50px';
			$upload->file_name_body_add = 'thumb_small_';
			$upload->process( UPLOAD );
		}

		$upload->file_name_body_add = '_';
		$upload->process( UPLOAD );

		if( !$upload->processed ){
			return false;
		}
		
		return $upload->file_dst_name;
	}

	public function getDataViewError( $msg )
	{
		$this->view->messages = array( $this->view->translate( $msg ) );
		$this->view->folder_id = $_POST['folder_id'];
		$this->view->data = (object) $_POST;
		$this->render( "input_file" );
	}
}
