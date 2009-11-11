<?php
class Activity_ActivityController extends Application_Controller_Abstract
{
    protected $_model = "Activity_Model_Activity";

	public function indexAction()
	{
		$user           = new Zend_Session_Namespace( "user" );
        $activity       = new Activity_Model_Activity();
        $activityGroup  = new Activity_Model_ActivityGroup();

		if ( Share_Model_Role::STUDENT == $user->roles[SYSTEM_ID]['current'] ){
			$where = array( "started  <= ?" => date('Y-m-d'),
				  			"finished >= ?" => date('Y-m-d'),
				   			"composition_type = ?" => Activity_Model_Activity::INDIVIDUALLY );
		}else{
			$where = array( "composition_type = ?" => Activity_Model_Activity::INDIVIDUALLY );
        }
		$this->view->role      = $user->roles[SYSTEM_ID]['current'];
		$this->view->person_id = $user->person_id;
		
		$this->view->rs      = $activity->fetchRelation( $where , "finished" );
		$this->view->grouped = $activityGroup->fetchActivityByGroup( $user->person_id );
	}	
	
	public function inputAction()
	{
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );

        $activity = new Activity_Model_Activity();

        $this->view->data = $activity->createRow();

		if( $id ){
			$this->view->data = $activity->fetchRow( array( "id = ?" => $id ) );
        }
        
        $composition = array(
                array( "id" => Activity_Model_Activity::INDIVIDUALLY, "name" => $this->view->translate("individually") ),
                array( "id" => Activity_Model_Activity::GROUPED, "name" => $this->view->translate("grouped") )
        );
                              
        $this->view->composition = Preceptor_Util::toSelect( $composition );
	}
	
	public function saveAction()
	{
		$activity = new Activity_Model_Activity();

        $result = $activity->save( $_POST['data'] );
		
		$this->_helper->_flashMessenger->addMessage( $result->message );

		if ( !$result->error ) {
			if( $_POST['data']['Activity_Model_Activity']['composition_type'] == Activity_Model_Activity::GROUPED ){
				$this->_redirect( "/activity/group/index/id/" . $result->detail['id'] );
			}else{
				$this->_redirect( "/activity/activity/" );
			}
		}else{
			$this->_redirect( $this->_getRedirector( 'Error' ) , array( 'prependBase' => true ) );
		}
	}
	
	public function deleteAction()
	{
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );

        $activity      = new Activity_Model_Activity();
        $activityStage = new Activity_Model_ActivityStage();
        $activityTextPerson = new Activity_Model_ActivityTextPerson();
        $activityTextGroup  = new Activity_Model_ActivityTextGroup();
        
        try{
            $activityTextPerson->delete( array( "activity_id" => $id ) );
            $activityTextGroup->delete( array( "activity_id" => $id ) );
            $activityStage->delete( array( "activity_id" => $id ) );
            $activity->delete( $id );
            $this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted successfully" ) );
        }
        catch( Exception $e ){
            $this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted error" ) );
        }
		
		$this->_redirect( "/activity/activity/index" );
	}
}