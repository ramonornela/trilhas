<?php
class Activity_TextController extends Application_Controller_Abstract
{
    protected $_model = false;

	public function indexAction()
	{
		$this->_redirect( "activity/activity" );
	}
	
	public function inputAction()
	{
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
        
		$user               = new Zend_Session_NameSpace( 'user' );
		$activity           = new Zend_Session_NameSpace( 'activity' );
        $activityTextPerson = new Activity_Model_ActivityTextPerson();
        $activityTextGroup = new Activity_Model_ActivityTextGroup();
		
        $this->view->user     = $user;
		$this->view->activity = $activity;
        
		if ( !$activity->group_id ){
			$this->view->rs = $activityTextPerson->fetchAll( array( "activity_id =?" => $activity->id , "person_id =?" => $user->person_id ) , "id DESC" );
		}else{
			$this->view->rs = $activityTextGroup->fetchAll( array( "activity_id =?" => $activity->id  , "activity_group_id =?" => $activity->group_id ) , "id DESC" );
		}
		
		if( $id ){
			if ( ! $activity->group_id ){
				$this->view->data = $activityTextPerson->fetchRow( array( "id = ?" => $id ) );
            }else{
				$this->view->data = $activityTextGroup->fetchRow( array( "id = ?" => $id ) );
            }

			$this->_helper->layout->setLayout('clear');
			$this->render( "update-input" );
		}
		
		$this->_helper->layout->setLayout('clear');
	}
	
	public function viewAction()
	{
		$id         = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
		$version_id = Zend_Filter::filterStatic( $this->_getParam( "version_id" ) , "int" );
		$person_id  = Zend_Filter::filterStatic( $this->_getParam( "person_id" ) , "int" );
		$group_id   = Zend_Filter::filterStatic( $this->_getParam( "group_id" ) , "int" );
		
		$activity           = new Zend_Session_NameSpace( 'activity' );
        $activityTextPerson = new Activity_Model_ActivityTextPerson();
        $activityTextGroup  = new Activity_Model_ActivityTextGroup();

		if ( $id )
			$activity->id = $id;
		
		if ( $group_id )
			$activity->group_id = $group_id;
			
		if ( $person_id )
			$activity->person_id = $person_id;
			
		if ( ! $activity->group_id ){
			$this->view->rs = $activityTextPerson->fetchAll( array( "activity_id =?" => $activity->id , "person_id =?" => $activity->person_id ) , "id DESC" );
			$table = $activityTextPerson;
		}
		else{
			$this->view->rs = $activityTextGroup->fetchAll( array( "activity_id =?" => $activity->id  , "activity_group_id =?" => $activity->group_id ) , "id DESC" );
			$table = $activityTextGroup;
		}

        $this->view->data = $table->createRow();

		if( $version_id )
			$this->view->data = $table->fetchRow( array( "id = ?" => $version_id ) );
			  
	}
	
	public function listAction()
	{
		$activity           = new Zend_Session_NameSpace( 'activity' );
		$user               = new Zend_Session_NameSpace( 'user' );
        $activityTextPerson = new Activity_Model_ActivityTextPerson();
        $activityTextGroup  = new Activity_Model_ActivityTextGroup();
		
		if ( ! $activity->group_id )
			$this->view->rs = $activityTextPerson->fetchAll( array( "activity_id =?" => $activity->id , "person_id =?" => $user->person_id ) , "id DESC" );
		else
			$this->view->rs = $activityTextGroup->fetchAll( array( "activity_id =?" => $activity->id  , "activity_group_id =?" => $activity->group_id ) , "id DESC" );

        $this->view->user = $user;

		$this->_helper->layout->setLayout('clear');
	}
	
	public function saveAction()
	{
		$activityTextPerson = new Activity_Model_ActivityTextPerson();
        $activityTextGroup  = new Activity_Model_ActivityTextGroup();
        
        if( !$this->getRequest()->isPost() ){
			throw new Xend_Exception( "Problem during submit. saveAction" );
		}
		
		$user 	  = new Zend_Session_NameSpace( 'user' );
		$activity = new Zend_Session_NameSpace( 'activity' );
		
		if ( ! $activity->group_id ){
			$result = $activityTextPerson->saveText( $_POST['data'] , Activity_Model_ActivityTextPerson::SAVE_STUDENT );
		}else{
			$result = $activityTextGroup->saveText( $_POST['data'] , Activity_Model_ActivityTextPerson::SAVE_STUDENT  );
		}
		
		$this->_helper->_flashMessenger->addMessage( $result->message );
		
		if( $result->error ){
            $this->_redirect( "/activity/text/input" );
        }else{
            $this->_redirect( "/activity/text/list" );
        }
	}
	
	public function finalizingAction()
	{
		if( !$this->getRequest()->isPost() ){
			throw new Xend_Exception( "Problem during submit. saveAction" );
		}
		
		$user               = new Zend_Session_NameSpace( 'user' );
		$activity           = new Zend_Session_NameSpace( 'activity' );
        $activityTextPerson = new Activity_Model_ActivityTextPerson();
        $activityTextGroup  = new Activity_Model_ActivityTextGroup();
		
		if ( !$activity->group_id ){
			$result = $activityTextPerson->saveText( $_POST['data'] , Activity_Model_ActivityTextPerson::FINALITY_STUDENT );
		}else{
			$result = $activityTextGroup->saveText( $_POST['data'] , Activity_Model_ActivityTextPerson::FINALITY_STUDENT  );
		}
		
		$this->_helper->_flashMessenger->addMessage( $result->message );
		
		if( $result->error ){
            $this->_redirect( "/activity/text/input" );
        }else{
            $this->_redirect( "/activity/activity" );
        }
	}

    public function deleteAction()
    {
        $id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "Int" );

        $activity           = new Zend_Session_NameSpace( 'activity' );
        $activityTextGroup  = new Activity_Model_ActivityTextGroup();
        $activityTextPerson = new Activity_Model_ActivityTextPerson();

        if ( isset( $activity->group_id ) && $activity->group_id ){
            $activityTextGroup->delete( array( "id" => $id ) );
		}else{
            $activityTextPerson->delete( array( "id" => $id ) );
		}

        $this->_redirect( "/activity/text/input" );
    }
	
}