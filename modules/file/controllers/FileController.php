<?php
class File_FileController extends Application_Controller_Abstract
{
    protected $_model = "File_Model_File";

	public function indexAction()
	{
		$numberBreadCrumbs = Zend_Filter::filterStatic( $this->_getParam( 'number_bread_crumbs' ) , 'int' );

		$id 	 	= Zend_Filter::filterStatic( $this->_getParam( 'id' ) , 'int' );
		$user   	= new Zend_Session_Namespace( "user" );

        /*
         * objects of model class
         */
         $file          = new File_Model_File();
         $personGroup   = new Station_Model_ClassPerson();
         $folder        = new File_Model_Folder();

		$courseDisciplineGroup = $personGroup->getCourseDiscipline( $user->person_id , $user->roles[SYSTEM_ID]['current'] );
        
		if( !$user->folderHierarchyCreate ){
			$folder->create( $courseDisciplineGroup['data'] , $user );
		}
        
		if( !$id ){
			if ( $user->role_id != Share_Model_Role::INSTITUTION ){
				$this->view->rs = $folder->findByClass_Person( array( $user->group_id , $user->person_id ) )->current();
			}else{
				$this->view->rs = $folder->findByCourse_Class_Discipline( array( $user->course_id , $user->group_id , $user->discipline_id ) )->current();
			}
			$courseDisciplineGroup = array();

		}else{
			$this->view->rs = $folder->find( $id )->current();

			if( $id == File_Model_Folder::DEFAULT_FOLDER_FILE ||
			    ( $this->view->rs->relation && $numberBreadCrumbs <= File_Model_File::LIST_FOLDERS_PERSONS ) )
			{
				$this->view->folders = $this->getFolders( 0 , array() , $user , array( "relation IS NULL" ) );
			}
		}

		$this->view->location = $folder->getLocation( $this->view->rs , true , array( "div" => "file-file" , "uri" => "{$this->view->url}/file/file/index/id/" ) );

		$this->view->numberCallBreadCrumbs = $folder->getNumberCallBreadCrumbs();
        
		$this->view->dirs 	= $this->getFolders( ( !$id || ( $id != File_Model_Folder::DEFAULT_FOLDER_FILE && !$this->view->rs->relation ) ) ? 0 : $this->view->numberCallBreadCrumbs , $courseDisciplineGroup , $user );
		$this->view->files  = $this->view->rs->findManyToManyRowset( "File_Model_File" , "File_Model_FolderFile" );
		$this->view->user 	= $user;
        
		if( count( $this->view->folders ) ){
			$this->view->dirs->join( $this->view->folders );
		}

		$this->view->allowButtons = $this->allowButtons();
	}

	/**
	 * @access public
	 * @return void
	 */
	public function downloadAction()
	{
		$read = $this->_getParam( 'read' );

        $read = isset( $read ) ? true : false;

        $address  = UPLOAD ."/". base64_decode( $this->_getParam( "file" ) );

        if( $read ){
            $this->readFile( $address );
        }

        $this->download( $address );
	}

	/**
	 * @access public
	 * @return void
	 */
	public function saveAction()
	{
        $folder = new File_Model_Folder();

        $folder_id = $_POST['data']['File_Model_Folder']['folder_id'];

        $numberCallBreadCrumbs = $_POST['data']['File_Model_Folder']['number_call_bread_crumbs'];

        $folder->save( $_POST['data'] );

        $this->_redirect( "/file/file/index/" );
	}


	/**
	 * @access public
	 * @return void
	 */
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

			$data['File_Model_File']['title'] 	 = substr( $name , 0 , strrpos( $name , "." ) );
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
            
		}else if( $data['File_Model_File']['id'] ){
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

        $this->_redirect( "/file/file/index/id/" . $data['File']['folder_id'] );
	}

	/**
	 * @access public
	 * @return void
	 */
	public function inputFileAction()
	{
		$id = Zend_Filter::filterStatic( $this->_getParam( 'id' ) , 'int' );
		$this->view->folder_id = Zend_Filter::filterStatic( $this->_getParam( 'folder_id' ) , 'int' );

        $file = new File_Model_File();

		if( $id ){
			$this->view->data = $file->find( $id )->current();
		}
	}

	/**
	 * @access public
	 * @return void
	 */
	public function deleteAction()
	{
		$id 	   		   = Zend_Filter::filterStatic( $this->_getParam('id') , 'int' );
   		$folder_id 		   = Zend_Filter::filterStatic( $this->_getParam( "folder_id" ) , 'int' );
   		$numberBreadCrumbs = Zend_Filter::filterStatic( $this->_getParam( 'number_bread_crumbs' ) , 'int' );

        $folder = new File_Model_Folder();

   		try{
   			$msg = $this->view->translate( "deleted error" );

   			if( $folder->allowDelete( $id ) && !$folder->find( $id )->current()->relation )
   			{
   				$folder->delete( $id );
   				$msg = $this->view->translate( "deleted successfully" );
   			}

   			$this->_helper->_flashMessenger->addMessage( $msg );
			$this->_redirect( "/file/file/index/folder_id/$folder_id/number_bread_crumbs/$numberBreadCrumbs" );

   		}catch( Exception $e ){
   			$this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted error" ) );
   			$this->_redirect( "/file/file/index/folder_id/$folder_id/number_bread_crumbs/$numberBreadCrumbs" );
   		}
	}

	/**
	 * @access public
	 * @return void
	 */
	public function inputAction()
	{
		$id = Zend_Filter::filterStatic( $this->_getParam( 'id' ) , 'int' );
		$this->view->numberCallBreadCrumbs = Zend_Filter::filterStatic( $this->_getParam( 'number_bread_crumbs' ) , 'int' );

        $folder = new File_Model_Folder();

		$this->view->jsonValidate = Zend_Json::encode( $folder->validators );
		$this->view->visible      = File_Model_Folder::VISIBILITY_PUBLIC;

        $this->view->data = $folder->createRow();

        $this->view->data->folder_id = Zend_Filter::get( $this->_getParam( 'folder_id' ) , 'int' );

		if( $id ){
			$this->view->data = $folder->find( $id )->current();
		}
	}

	/**
	 * @access public
	 * @return void
	 */
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

	/**
	 * @access public
	 * @return void
	 */
	public function deleteFileAction()
	{
		$id 	   = Zend_Filter::filterStatic( $this->_getParam('id') , 'int' );
   		$folder_id = Zend_Filter::filterStatic( $this->_getParam( "folder_id" ) , 'int' );

		$file   = new File_Model_File();
		$folderFile = new File_Model_FolderFile();

   		try{
   			$folderFile->delete( array( "file_id" => $id ) );
			$file->delete( $id );

			$this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted successfully" ) );

			$this->_redirect( '/file/file/index/id/' . $folder_id );

   		}catch( Exception $e ){

   			$this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted error" ) );
   			$this->_redirect( '/file/file/index/id/' . $folder_id );
   		}
	}

	public function getDataViewError( $msg )
	{
		$this->view->messages = array( $this->view->translate( $msg ) );
		$this->view->data = (object) $_POST;
		$this->render( "input-file" );
	}

	/**
	 *
	 * @param int $numberCall
	 * @param array $ids
	 * @param Zend_Session_Namespace $user
	 * @param mixed $whereAdd
	 * @return Zend_Db_Table_Rowset
	 */
	public function getFolders( $numberCall , array $ids , $user = NULL , $whereAdd = NULL )
	{
		$folder         = new File_Model_Folder();
        $classperson    = new Station_Model_ClassPerson();
        
        switch( $numberCall )
		{
			case File_Model_File::LIST_FOLDERS_COURSES:
				$dirs = $folder->findByCourse( $ids['idCourses'] , $this->view->rs->id );
			break;

			case File_Model_File::LIST_FOLDERS_DISCIPLINES:
				$dirs = $folder->findByDiscipline( $ids['idDisciplines'] , $this->view->rs->id );
			break;

			case File_Model_File::LIST_FOLDERS_GROUPS:
				$dirs = $folder->findByClass( $ids['idClass'] , $this->view->rs->id );
			break;

			case File_Model_File::LIST_FOLDERS_PERSONS:
				$relation   = new Relation();
				$idsPersons = $classperson->fetchAll( array( 'group_id = ?' => $relation->fetchRow( array( 'relation = ?' => $this->view->rs->relation ) )->group_id ) )->toArray();
				/**
				 * @todo error
				 */
				$dirs = $folder->findByPerson( $this->toSelect( $idsPersons , "person_id" , "person_id" , NULL ) , $this->view->rs->id );
			break;

			default:
				$where = array(
					"folder_id = ?" => $this->view->rs->id,
					"( visibility = '".File_Model_Folder::VISIBILITY_PUBLIC."' ". Zend_Db_Select::SQL_OR ." person_id = ? )" => $user->person_id ,
                    "module = '".File_Model_Folder::FLAG_FILE."'"
				);

				$where = array_merge( $where , (array) $whereAdd );
				$dirs = $folder->fetchAll( $where );
		}

		return $dirs;
	}

	/**
	 * verify if user permission in folder in question
	 *
	 * @return boolean
	 */
	public function allowButtons()
	{
		return (
				(
					( $this->view->user->role_id != Share_Model_Role::STUDENT ) &&
					( $this->view->numberCallBreadCrumbs <= File_Model_File::LIST_FOLDERS_PERSONS ) &&
					(
						( $this->view->rs->relation )
						||
						( $this->view->rs->id == File_Model_Folder::DEFAULT_FOLDER_FILE )
					)
				)
				||
				(
					(
						( $this->view->numberCallBreadCrumbs == ( File_Model_File::LIST_FOLDERS_PERSONS+1) )
						||
						(
							( !$this->view->rs->relation )
							&&
							( $this->view->rs->id != File_Model_Folder::DEFAULT_FOLDER_FILE)
						)
					)
					&&
					( $this->view->rs->person_id  == $this->view->user->person_id )
				)
		);
	}

    public function download( $path, $name = null, $type = 'application/octet-stream')
    {
        if (headers_sent()) {
            echo 'File download failure: HTTP headers have already been sent and cannot be changed.';
            exit;
        }
		
        $path = realpath($path);
        if ($path === false || !is_file($path) || !is_readable($path)) {
            header('HTTP/1.0 204 No Content');
            exit;
        }
		
        $name = empty($name) ? basename($path) : $name;
        $size = filesize($path);

        header('Expires: Mon, 20 May 1974 23:58:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Cache-Control: private');
        header('Pragma: no-cache');
        header("Content-Transfer-Encoding: binary");
        header("Content-type: {$type}");
        header("Content-length: {$size}");
        header("Content-disposition: attachment; filename={$name}");

        while( ob_get_level() ){
            ob_get_clean();
        }

        readfile($path);
        exit;
    }

    public function readFile( $path )
    {
       if (headers_sent()) {
            echo 'File download failure: HTTP headers have already been sent and cannot be changed.';
            exit;
        }

        $path = realpath($path);
        if ($path === false || !is_file($path) || !is_readable($path)) {
            header('HTTP/1.0 204 No Content');
            exit;
        }

        while( ob_get_level() ){
            ob_get_clean();
        }

        readfile($path);
        exit;
    }
}
