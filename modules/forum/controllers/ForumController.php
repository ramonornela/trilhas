<?php
class Forum_ForumController extends Application_Controller_Abstract
{
	protected $_model = "Forum_Model_Forum";

	public function indexAction()
	{
		$forum = new Forum_Model_Forum();
		$user 	= new Zend_Session_Namespace('user');

		$to 	= Zend_Filter::filterStatic( $this->_getParam('to') , 'int' );
		$status = Zend_Filter::filterStatic( $this->_getParam('status') , 'int' );
		$q 		= $this->_getParam('q');
		$limit 	= 5;

		$forum->closeExpired();
		$this->view->rs = new ArrayIterator();
		
		if( !( $q && $status ) ){
			$this->view->counts = $this->paginateSelect( ceil( $forum->countRelation( array( "forum_id IS NULL" , "started IS NULL OR started <= ?" => date("Y-m-d") ) ) / $limit ) );
			$this->view->rs 	= $forum->fetchRelation( array( "forum_id IS NULL" , "started IS NULL OR started <= ?" => date("Y-m-d") ) , array( "status" , "created DESC" ) ,  $limit , ( $to * $limit ) );
		}
		
		$this->view->status	= array(
			Application_Model_Status::ACTIVE => $this->view->translate("open"),
			Application_Model_Status::CLOSED => $this->view->translate("closed")
		);
		
		$this->view->limit  = $limit;
		$this->view->to 	= $to;
		$this->view->user	= $user; 
		$this->view->q 		= $q;
		$this->view->statusQ = $status;
	}
	
	public function findAction()
	{
		$forum = new Forum_Model_Forum();
		$q = $this->_getParam('q');
		$status = Zend_Filter::filterStatic( $this->_getParam('status') , 'int' );
		
		$this->view->rs = $forum->fetchRelation( array( "ds LIKE (?) OR title LIKE (?)" => "%$q%" , "status = ?" => $status ) , array( "status" , "created DESC" ) ,  10 );

		$this->view->status	= array(
			Application_Model_Status::ACTIVE => $this->view->translate("open"),
			Application_Model_Status::CLOSED => $this->view->translate("closed")
		);
		
		$this->view->q 		 = $q;
		$this->view->statusQ = $status;
		
		$this->_helper->layout->setLayout('clear');
	}

	public function viewAction()
	{
		$forum			= new Forum_Model_Forum();
		$forumSubscribe = new Forum_Model_ForumSubscribe();
		$user 	= new Zend_Session_Namespace('user');
		
		$id 	= Zend_Filter::filterStatic( $this->_getParam('id') , 'int' );
		$to 	= Zend_Filter::filterStatic( $this->_getParam('to') , 'int' );
		$limit 	= Zend_Filter::filterStatic( $this->_getParam('limit') , 'int' );
		$limit  = $limit ? $limit : 5;
		$q 		= $this->_getParam('q');
		$status = Zend_Filter::filterStatic( $this->_getParam('status') , 'int' );
		
		$this->view->rs		   = $forum->fetchAll( array( "forum_id = ? OR id = ?" => $id ) , "created" , $limit , ( $to * $limit ) );
        $this->view->subscribe = $forumSubscribe->fetchAll( array( "forum_id = ?" => $id , "person_id = ?" => $user->person_id ) );
        $this->view->counts    = $this->paginateSelect( ceil( $forum->count( array( "forum_id = ? OR id = ?" => $id ) ) / $limit ) );
		
		$this->view->limit    = $limit;
		$this->view->to 	  = $to;
		$this->view->forum_id = $id;
		$this->view->user     = $user;
		$this->view->q 		  = $q;
		$this->view->status   = $status;
	}
	
	public function inputAction()
	{
		$forum = new Forum_Model_Forum();
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
		
		if( $id ){
			$this->view->data = $forum->find( $id )->current();
		}
		
		$forum->validators['title'][1] = 'NotEmpty';
		$this->view->jsonValidate = Zend_Json::encode( $forum->validators );
	}
	
	public function inputreplyAction()
	{
		$forum = new Forum_Model_Forum();
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
		
		$this->view->data->forum_id 	  = Zend_Filter::filterStatic( $this->_getParam( "forum_id" ) , "int" );
		$this->view->data->forum_reply_id = Zend_Filter::filterStatic( $this->_getParam( "forum_reply_id" ) , "int" );
		
		if( $id ){
			$this->view->data = $forum->find( $id )->current();
		}
		
		$this->view->status	= array(
			Application_Model_Status::ACTIVE => $this->view->translate("open"),
			Application_Model_Status::CLOSED => $this->view->translate("closed")
		);
		
		$this->_helper->layout->setLayout('clear');
	}

	public function savereplyAction()
	{
		$forum = new Forum_Model_Forum();
        $user = new Zend_Session_NameSpace( 'user' );
        $result = $forum->save( $_POST['data'] );
        
        $this->_helper->_flashMessenger->addMessage( $result->message );
        
        if( $result->error ){
            $this->_redirect( $this->_getRedirector( 'Error' ) , array( 'prependBase' => true ) );
        }else{
            $forum_id = $_POST['data']['Forum_Model_Forum']['forum_id'] 
						? $_POST['data']['Forum_Model_Forum']['forum_id']
						: $result->detail['id'];
						
            $this->sendEmail( $forum_id , $_POST['data'] , $user );
            $this->_redirect( "/forum/forum/view/id/" . $forum_id );
        }
	}
	
	public function subscribeAction()
	{
		$forumSubscribe = new Forum_Model_ForumSubscribe();
		$user = new Zend_Session_NameSpace( 'user' );
		$id   = $this->_getParam( "id" );
		$flag = $this->_getParam( "flag" );
		
		if( $flag == "T" ){
			$forumSubscribe->save( array(
                "Forum_Model_ForumSubscribe"=> array(
                    "person_id" => $user->person_id,
                    "forum_id"  => $id
                    )
                ) );
            
			$this->_helper->_flashMessenger->addMessage( $this->view->translate( "registered successfully" ) );
		}else{
			$forumSubscribe->delete( $id , $user->person_id );
			$this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted successfully" ) );
		}
		
		$this->_redirect( "/forum/forum/view/id/" . $id );
	}

	public function deleteAction()
    {
		$forum	= new Forum_Model_Forum();
		
   		$id 	  = Zend_Filter::filterStatic( $this->_getParam('id') , 'int' );
   		$forum_id = Zend_Filter::filterStatic( $this->_getParam( "forum_id" ) , 'int' );
		
   		try{
			$forum->delete( $id );
			$this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted successfully" ) );
			
			if( $id == $forum_id ){
				$this->_redirect( '/forum/forum/' );
			}else{
				$this->_redirect( '/forum/forum/view/id/' . $forum_id );
			}
   		}
   		catch( Exception $e )
   		{
   			$this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted error" ) );
   			$this->_redirect( "/forum/forum/view/id/" . $forum_id );
   		}
    }
    
    public function sendEmail( $forum_id , $data , $user )
    {
		$forum			= new Forum_Model_Forum();
		$forumSubscribe = new Forum_Model_ForumSubscribe();
    	$title = $forum->find( $forum_id )->current()->title;
    	$this->view->data = $data;
    	$this->view->user = $user;

    	$body 	= $this->render( "email" );
    	$result = $forumSubscribe->fetchAll( array( "forum_id = ?" => $forum_id ) );
    	
    	if( $result->count() )
    	{
		    $options = array(
				'auth' => 'login',
	            'username' => SMTP_USERNAME,
	            'password' => SMTP_PASSWORD,
                'port'     => SMTP_PORT
			); 
			
			$transport = new Zend_Mail_Transport_Smtp( SMTP_SERVER , $options );
				
			$mail = new Zend_Mail( APP_CHARSET );
			$mail->addHeader('X-Mailer', 'EAD');
			$mail->addHeader('Reply-To', $user->email );
			$mail->addHeader('Erros-To', "retorno@espacoead.com.br" );
			$mail->setReturnPath( "retorno@espacoead.com.br" );
			$mail->setFrom( 'trails@espacoead.com.br' , $user->name );
			$mail->setSubject( $title );
			$mail->setBodyHtml( $body );
			
	    	foreach( $result as $rs ){
	    		$person = $rs->findParentRow( "Share_Model_Person" );
	    		$mail->addBcc( $person->email );
	    	}
	    	
	    	try{
				$mail->send( $transport ); 
			}catch(Exception $e){}
						
			unset( $mail );
    	}
    }
}