<?php
class Weblibrary_WeblibraryController extends Application_Controller_Abstract
{
	const LIMIT = 15;

	protected $_model = "File_Model_File";

	public function indexAction()
	{
        $file = new $this->_model();
        
		$this->view->rs = $file->fetchRelation( "type = '". File_Model_File::TYPE_FILE_WEBLIBRARY ."'" , "id DESC" , self::LIMIT , NULL );
	}

	public function findAction()
	{
		$letter = strtoupper( $this->_getParam( "letter" ));
		$word   = strtoupper( $this->_getParam( "word" ));

        $file = new $this->_model();

		if( $letter )
			$where = array( 'UPPER( title ) LIKE UPPER(?)' => "$letter%" , "type = '".File_Model_File::TYPE_FILE_WEBLIBRARY."'");

		if( $word )
			$where = array( 'UPPER( author ) LIKE UPPER(?) OR UPPER( title ) LIKE UPPER(?)' => "%$word%" , "type = '".File_Model_File::TYPE_FILE_WEBLIBRARY."'" );

		$this->view->rs 	= $file->fetchRelation( $where , "title");

		$this->view->word  = $word;

	}

    public function getDataViewError($msg)
    {
		$this->view->messages = array( $this->view->translate( $msg ) );
		$this->view->file = $this->view->data = (object) $_POST;
		$this->_redirect( "/weblibrary/weblibrary/input" );
    }

	public function saveAction()
	{
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
		
		$user     = new Zend_Session_NameSpace( 'user' );
		$relation = new Zend_Session_NameSpace( 'relation' );
        $file     = new File_Model_File();

		if( !$id ){
			$location = $this->upload();

			if( !$location ){
				$this->_helper->_flashMessenger->addMessage( $this->view->translate( "save error" ) );
				$this->_redirect( "/weblibrary/weblibrary/index/" );
			}

			$_POST['data'][$this->_model]['location'] = $location;
        }

        $_POST['data'][$this->_model]['person_id'] = $user->person_id;
        $_POST['data'][$this->_model]['type'] 	   = File_Model_File::TYPE_FILE_WEBLIBRARY;

        $result = $file->save( $_POST['data'] );

        $this->_helper->_flashMessenger->addMessage( $this->view->translate( $result->message ) );
        $this->_redirect( "/weblibrary/weblibrary/index/" );
	}

	public function upload()
	{
		Zend_Loader::loadClass( "Upload" , "Upload" );
        $file = $this->organizeFile( $_FILES['data'] );

		$upload = new Upload( $file );

        if( !$file['name'] ){
            $this->_helper->_flashMessenger->addMessage( $this->view->translate( "is necessary to send file" ) );
            $this->_redirect( "/weblibrary/weblibrary/input/" );
        }

		if ( $upload->processed ){
			if( !$upload->file_src_size ){
				$this->getDataViewError( "file size exceeded" );
				return false;
			}

			$name = $file['name'];
            
			$upload->process( UPLOAD . "/");

			if( !$upload->processed )
				return false;

			$upload->clean();

			return $upload->file_dst_name;
		}
	}

    public function organizeFile( $file )
    {
        if( !is_array($file) ){
            return "Type invalid, is not a array";
        }
        
        foreach( $file as $key => $value ){
            $data[$key] = $value[$this->_model]['file'];
        }
        
        return $data;
    }

	public function inputAction()
	{
		$this->view->data->id = null;
		$file = new File_Model_File();

		$this->view->jsonValidateWeblibrary = Zend_Json::encode( $file->validatorsWeblibrary );
		parent::inputAction();
	}
}