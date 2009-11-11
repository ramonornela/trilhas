<?php
class Activity_CorrectController extends Application_Controller_Abstract
{
	public function indexAction()
	{
		$id       = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
		$group_id = Zend_Filter::filterStatic( $this->_getParam( "group_id" ) , "int" );
        
		$activity           = new Zend_Session_NameSpace( 'activity' );
        $activityTextGroup  = new Activity_Model_ActivityTextGroup();
        $activityTextPerson = new Activity_Model_ActivityTextPerson();

        $activity->id = isset( $id ) ? $id : $activity->id;

		if( $group_id ){
			$this->view->rs = $activityTextGroup->findTextFinality( $activity->id );
			$this->render("indexgroup");
		}else{
			$this->view->rs = $activityTextPerson->findTextFinality( $activity->id );
			$this->render("indexperson");
		}
	}	
	
	public function inputAction()
	{
		$person_id  = Zend_Filter::filterStatic( $this->_getParam( "person_id" ) , "int" );
		$version_id = Zend_Filter::filterStatic( $this->_getParam( "version_id" ) , "int" );
		$group_id   = Zend_Filter::filterStatic( $this->_getParam( "group_id" ) , "int" );

		$activity           = new Zend_Session_NameSpace( 'activity' );
        $activityTextGroup  = new Activity_Model_ActivityTextGroup();
        $activityTextPerson = new Activity_Model_ActivityTextPerson();

		if ( $group_id ){
			$activity->group_id = $group_id;
        }
			
		if ( $person_id ){
			$activity->person_id = $person_id;
        }
		
		if ( ! $activity->group_id ){
			$this->view->rs = $activityTextPerson->fetchAll( array( "activity_id =?" => $activity->id , "person_id =?" => $activity->person_id ) , "id DESC" );
			$this->view->jsonValidate = Zend_Json::encode( $activityTextPerson->validators );
		}else{
			$this->view->rs = $activityTextGroup->fetchAll( array( "activity_id =?" => $activity->id  , "activity_group_id =?" => $activity->group_id ) , "id DESC" );
			$this->view->jsonValidate = Zend_Json::encode( $activityTextGroup->validators );
		}
			
		if( $version_id ){
			if ( !$activity->group_id ){
				$this->view->data = $activityTextPerson->fetchRow( array( "id = ?" => $version_id ) );
            }else{
				$this->view->data = $activityTextGroup->fetchRow( array( "id = ?" => $version_id ) );
            }

			$this->render( "update_input");
			return false;
		}
        
        $this->_helper->layout->setLayout('clearbox');
	}

	public function saveAction()
	{
		if( !$this->getRequest()->isPost() ){
			throw new Xend_Exception( "Problem during submit. saveAction" );
		}
		
		$user               = new Zend_Session_NameSpace( 'user' );
		$activity           = new Zend_Session_NameSpace( 'activity' );
        $activityTextGroup  = new Activity_Model_ActivityTextGroup();
        $activityTextPerson = new Activity_Model_ActivityTextPerson();
		
		if ( ! $activity->group_id ){
			$result = $activityTextPerson->saveText( $_POST['data'] , Activity_Model_ActivityTextPerson::SAVE_TEACHER , $activity->person_id  );
		}else{
			$result = $activityTextGroup->saveText( $_POST['data'] , Activity_Model_ActivityTextPerson::SAVE_TEACHER );
		}
		
		$this->_helper->_flashMessenger->addMessage( $result->message );
		
		if( $result->error ){
            $this->_redirect( "/activity/text/input" );
        }else{
            $this->_redirect( "/activity/activity" );
        }
	}
	
	public function finalizingAction()
	{
		if( !$this->getRequest()->isPost() ){
			throw new Xend_Exception( "Problem during submit. saveAction" );
		}

		$activity           = new Zend_Session_NameSpace( 'activity' );
        $activityTextGroup  = new Activity_Model_ActivityTextGroup();
        $activityTextPerson = new Activity_Model_ActivityTextPerson();

		
		if ( ! $activity->group_id ){
			$result = $activityTextPerson->saveText( $_POST['data'] , Activity_Model_ActivityTextPerson::FINALITY_TEACHER , $activity->person_id  );
		}else{
			$result = $activityTextGroup->saveText( $_POST['data'] , Activity_Model_ActivityTextPerson::FINALITY_TEACHER );
		}
		
		$this->_helper->_flashMessenger->addMessage( $result->message );
		
		if( $result->error ){
            $this->_redirect( "/activity/text/input" );
        }else{
            $this->_redirect( "/activity/text/view/" );
        }
	}
	
	
}