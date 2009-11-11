<?php
class UserController extends Application_Controller_Abstract
{
	protected $_model = false;

	public function loginAction()
	{
        if( $this->getRequest()->isPost() )
		{
			$result = Preceptor_Share_Login::login( $_POST['username'] , $_POST['password'] );
			
			if ( $result->error ) {
				$this->view->messages = array( $this->view->translate( $result->message ) );
			} else {
				$user   = new Zend_Session_Namespace( "user" );
				$person = new Share_Model_Person();
				
				$rsPerson = $person->fetchRow( array( "user_id = ?" => $user->id ) );
				
				if( $rsPerson ){
					$user->person_id = $rsPerson->id;
					$user->name 	 = $rsPerson->name;
					$user->email 	 = $rsPerson->email;
                    $user->image     = $rsPerson->location;

                    if( $_POST['redirect'] ){
                        $this->_redirect( $_POST['redirect'] );
                    }

					$this->_redirect("/");
				} else {
					Zend_Auth::getInstance()->clearIdentity();
					$this->view->messages = array( $this->view->translate("user don't have a vinculated person!") );
				}
			}
		}
		
		if( !isset($_COOKIE['theme']) ){
			setcookie( 'theme' , DEFAULT_THEME , time()+60*60*24*365 ); //30 dias
		}

		$this->view->theme = $_COOKIE['theme'] ? $_COOKIE['theme'] : DEFAULT_THEME;

		$this->_helper->layout->setLayout('out');
	}

	public function selectAction()
	{
		$user = new Zend_Session_Namespace( "user" );
		$auth = Zend_Auth::getInstance();
		$userModel = new Share_Model_User();
		
		if( !$auth->hasIdentity() )
			$this->_redirect( "/user/login/" );
				
		if( $this->getRequest()->isPost() || $this->_getParam( "id" ) )
		{
			$role_id = $_POST["role_id"] ? $_POST["role_id"] : Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" ) ;
			Preceptor_Share_Login::select( $role_id );
			
			$this->_redirect( "/" );
		}
		
		$this->view->roles = Preceptor_Util::toSelect( 
			$userModel->fetchRow( array ( 'id = ?' => $user->id ) )
					  ->findManyToManyRowset( 'Preceptor_Share_Role' , 
											  'Preceptor_Share_UserRole' ,
											  null , 
											  null , 
											  $this->Preceptor_Share_UserRole
												   ->select()
												   ->where('system_id = ?' , SYSTEM_ID ) ) 
		);
        
		$this->_helper->layout->setLayout('out');
	}

	public function logoutAction()
	{
		session_unset();
		Zend_Auth::getInstance()->clearIdentity();
		    
		$this->_redirect( "/" );
	}
	
	public function forgotAction()
	{
		$userModel = new Share_Model_User();
		$user = $userModel->fetchRow( array( 'username=?' => $_POST['username'] ) );
		$password = $userModel->generateHash( 6 , User::LOGIN_TRASH );

		if ( ! $user->username ){
			$this->view->messages = array( $this->view->translate("your email is not registered in the system") );
			$this->render( 'login' , "out" );
			return false;
		}
		
		$options = array(
			'auth' => 'login',
	    	'username' => SMTP_USERNAME,
	        'password' => SMTP_PASSWORD,
	        'port'     => SMTP_PORT
		); 
			
		$transport = new Zend_Mail_Transport_Smtp( SMTP_SERVER , $options );
		$subject   = $this->view->translate( 'confirmation of registration' );
		
		$mail = new Zend_Mail( APP_CHARSET );
			
		$mail->setBodyHtml( $this->view->translate( 'new password' ) . ": " . $password['normal'] );
		$mail->setFrom( 'trails@espacoead.com.br' , $this->view->translate( 'institution' ) );
		$mail->addTo( $user->username , 'Preceptor' );
		$mail->addHeader( 'Reply-To', $user->username );
                
		$mail->setSubject( $this->view->translate( 'new password' ) );
		
		$data['Share_Model_User']['id'] = $user->id;
		$data['Share_Model_User']['password'] = $password['hash'];
		
		if ( $id = $userModel->save( $data ) ){
			$mail->send( $transport );
			$this->view->messages = array( $this->view->translate("your new password has been sent to your e-mail") );
		}
		
		$this->_helper->layout->setLayout('out');
		
		$this->render('login');
	}
}