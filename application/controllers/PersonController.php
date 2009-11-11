<?php
class PersonController extends Application_Controller_Abstract 
{
	protected $_model = "Share_Model_Person";

    public function inputAction()
	{
		$user   = new Zend_Session_Namespace('user');
		$person = new Share_Model_Person();
		
		$this->view->data = $person->find( $user->person_id )->current();

		$this->_helper->layout->setLayout('clearbox');
	}
	
	public function saveAction()
	{
		$user 	   = new Zend_Session_Namespace('user');
		$person    = new Share_Model_Person();
		$userModel = new Share_Model_User();
		
		if( $_FILES['file']['name'] ){
			$fileName = $this->upload();
		}
		
		$id = $person->saveinput( $fileName , $user->person_id );

		$userModel->savepass( $user->id );
		
		if( $id ){
			$user->name = $_POST['name'];
			
			if( $fileName ){
				$user->image = $fileName;
			}
			
			echo "<script>
					var url = new String( parent.location );
					parent.location = url.split( '#' )[0];
				</script>";
		}

		exit;
	}
    
	public function deleteAction()
	{
		$id 	= Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
		$person = new Share_Model_Person();
		$userRole = new Share_Model_UserRole();
		$user = new Share_Model_User();
		
        $db = $person->getAdapter();

        try{
            $db->beginTransaction();

            $user_id = $user->fetchRow( array( 'person_id = ?' => $id ) )->id;
            $userRole->delete( $user_id , "user_id" );
            $user->delete( $user_id );
            $person->delete( $id );

            $db->commit();
            
            $this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted successfully" ) );
        }
        catch( Exception $e ){
            $db->rollBack();
            $this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted error" ) );
        }

		$this->_redirect( "/form/formfielddata/index" );
	}

	public function verifyDataAction()
	{
		$person = new Share_Model_Person();
		$data   = $this->_getParam( "data" );
		
		$value = $person->fetchRow( array( "email =?" => $data ) );
											 
		if( $value ){
			echo Zend_Json::encode('ok');
			exit;
		}
		
		echo $value;
		exit;	
	}
	
	public function upload()
	{
		Zend_Loader::loadClass( "Upload" , DIR_LIBRARY . "/Upload/" );
		$upload = new Upload( $_FILES['file'] );
		
		if ( $upload->processed )
		{
			if( !$upload->file_src_size )
			{
				$this->getDataViewError( "file size exceeded" ); 
				return false;
			}
			$img_extensions = array("image/gif",
	                               "image/jpeg",
	                               "image/pjpeg",
	                               "image/png",
	                               "image/x-png",
			);					
			
			if( !in_array( $upload->file_src_mime, $img_extensions ) )
			{
				$this->_helper->_flashMessenger->addMessage( $this->view->translate( "extension invalid" ) );
				return false;
			}
			
			$name = $_FILES['file']['name'];
	
			$this->generationThumb( $upload );
		
			if( !$upload->processed )
				return false;
		
			$upload->clean();
			
			return $upload->file_dst_name;
			
		}
	}
	
	public function generationThumb( $upload )
	{
		$upload->image_resize = true;
        $upload->image_ratio_y = true;
		$upload->image_x = '100px';
		$upload->file_name_body_add = 'thumb_small_';
		$upload->process( UPLOAD );
		
		$upload->image_resize = true;
		$upload->image_ratio_y = true;
		$upload->image_x = '200px';
		$upload->file_name_body_add = 'thumb_medium_';
		$upload->process( UPLOAD );
		
		$upload->image_resize = true;
		$upload->image_ratio_y = true;
		$upload->image_x = '300px';
		$upload->file_name_body_add = '_';
		$upload->process( UPLOAD );
	}

    public function newAction()
    {
		$discipline_id = Zend_Filter::filterStatic( $this->_getParam( "discipline_id" ) , "int" );
		
        $this->view->discipline_id = $discipline_id;
        
        $this->_helper->layout->setLayout( "clearbox" );
    }

    public function saveNewAction()
    {
		$person = new Share_Model_Person();

        $result = $person->saveNew( $_POST['data'] );

        if( !$result['id'] && !is_array($result) ){
           $this->_helper->_flashMessenger->addMessage( $result );
           $this->_redirect( "/discipline/register/" );
        }

        if( $resultLogin = $this->loginSnapshot( $result['username'] , $result['password'] , $result['discipline_id'] ) ){
            echo "<script>
					document.location.reload(true);
				</script>";
            exit;
        }

        $this->_helper->_flashMessenger->addMessage( $this->view->translate( "error log" ) );
        $this->_redirect( "/discipline/register/discipline_id/{$result['discipline_id']}" );
    }

    public function loginSnapshot( $username , $password , $discipline_id )
    {
        $resultLogin = Preceptor_Share_Login::login( $username , $password );
        
        if ( $resultLogin->error ) {
				return false;
        } else {
            $user   = new Zend_Session_Namespace( "user" );
            $person = new Share_Model_Person();

            $rsPerson = $person->fetchRow( array( "user_id = ?" => $user->id ) );

            if( $rsPerson ){
                $user->person_id        = $rsPerson->id;
                $user->name             = $rsPerson->name;
                $user->email            = $rsPerson->email;
                $user->image            = $rsPerson->location;
                $user->courseIntention  = $discipline_id;
            }
            return true;
        }
    }
    
    public function verifyDataNewAction()
    {
        $cpf = Zend_Filter::filterStatic( $this->_getParam( "cpf" ) , "Digits" );
        $email = Zend_Filter::filterStatic( $this->_getParam( "email" ) , "HtmlEntities" );

        $person = new Share_Model_Person();

        $this->view->verify = false;
        
        if( isset( $email ) ){
            $resultEmail = $person->fetchRow( array( "email =?" => $email ) );
        }
        
        if( isset( $resultEmail->email ) ){
            $this->view->resultEmail =  $this->view->translate( "email already registered" );
        }

        if( isset( $cpf ) ){
            $resultCpf = $person->fetchRow( array( "cpf =?" => $cpf ) );
        }

        if( isset( $resultCpf->cpf ) ){
            $this->view->resultCpf = $this->view->translate( "cpf already registered" );
        }

        if( !isset( $resultCpf->cpf ) && !isset( $resultEmail->email ) ){
            $this->view->verify = true;
        }

        $this->_helper->layout->setLayout( "clear" );
    }

    public function registerClassAction()
    {
        $user        = new Zend_Session_Namespace('user');
        $class       = new Station_Model_ClassModel();
        $classPerson = new Station_Model_ClassPerson();

        $statusPerson = $classPerson->fetchRow( array( "class_id = ?" => $_POST['class_id'] , "person_id =?" => $user->person_id ) );
        
        if( !(isset( $statusPerson ) && $statusPerson->status == Station_Model_Status::WAITING) ){
            $data['Station_Model_ClassPerson']['class_id']  = $_POST['class_id'];
            $data['Station_Model_ClassPerson']['person_id'] = $user->person_id;
            $data['Station_Model_ClassPerson']['status']    = Station_Model_Status::WAITING;

            $result = $classPerson->save( $data );
            unset( $data );
        }

        $discount       = new Station_Model_Discount();
        $discountPerson = new Station_Model_DiscountPerson();
        
        if( isset( $_POST['promotion'] ) && $discount_id = $discount->fetchPromotion( $_POST['promotion'] ) ){
            $discipline_id = $class->fetchRow( array( "id =?" => $_POST['class_id'] ) )->discipline_id;

            $data['Station_Model_DiscountPerson']['class_id']     = $_POST['class_id'];
            $data['Station_Model_DiscountPerson']['person_id']    = $user->person_id;
            $data['Station_Model_DiscountPerson']['discount_id']  = $discount_id;
            $data['Station_Model_DiscountPerson']['discipline_id']= $discipline_id;
            
            $discountPerson->save( $data );
            unset( $data );
        }
        
        if( isset( $result->error ) && !$result->error ){
            $this->_helper->_flashMessenger->addMessage( $result->message );
        }else if( $result->error ){
            $this->_helper->_flashMessenger->addMessage( $result->message );
            $this->_redirect( "/course/my-course/" );
        }

        $this->_redirect( "/discipline/payment/class_id/{$_POST['class_id']}" );
    }

    public function registerClassCancelAction()
    {
        $class_id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "Int" );

        $user        = new Zend_Session_Namespace('user');
        $classPerson = new Station_Model_ClassPerson();

        $data = array( "person_id" => $user->person_id , "class_id" => $class_id );

        $classPerson->delete( $data );

        $discountPerson = new Station_Model_DiscountPerson();
        $discountPerson->delete( $data );

        $this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted successfully" ) );
        echo "<script>
					document.location.reload(true);
				</script>";
        exit;
        $this->_redirect( "/course/my-course/" );
    }
}