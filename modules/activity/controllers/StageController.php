<?php
class Activity_StageController extends Application_Controller_Abstract
{
	protected $_model = "Activity_Model_ActivityStage";
	
	public function indexAction()
	{
		$activity           = new Zend_Session_NameSpace( 'activity' );
		$user               = new Zend_Session_NameSpace( 'user' );
        $activityStage      = new Activity_Model_ActivityStage();
        $activityTextGroup  = new Activity_Model_ActivityTextGroup();
        $activityTextPerson = new Activity_Model_ActivityTextPerson();

		$id       = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
		$group_id = Zend_Filter::filterStatic( $this->_getParam( "group_id" ) , "int" );
		
		if( $id ){
			$activity->id        = $id;
			$activity->group_id  = $group_id;
			$activity->person_id = $user->person_id;
		}
		
		if ( $activity->group_id )
			$value = $activityTextGroup->fetchRow( array( "activity_id =?" => $activity->id  , "activity_group_id =?" => $activity->group_id ) , "id DESC" );
		else
			$value = $activityTextPerson->fetchRow( array( "activity_id = ?" => $activity->id , "person_id =?" => $activity->person_id ) , "id DESC" );
			
		if ( isset( $value->status ) && $value->status == Activity_Model_ActivityTextPerson::FINALITY_TEACHER )
			$this->_redirect( "/activity/text/view/" );
			
		$this->view->rs = $activityStage->fetchAll( array( "activity_id = ?" => $activity->id ) , "id" );
		$this->view->role = $user->roles[SYSTEM_ID]['current'];
	}
	
	public function inputAction()
	{
		$id = Zend_Filter::filterStatic( $this->_getParam("id") , "int" );

        $activityStage = new Activity_Model_ActivityStage();

        $this->view->data = $activityStage->createRow();

		if( $id ){
			$this->view->data = $activityStage->fetchRow( array( "id = ?" => $id ) );
		}
		
		$this->view->jsonValidate = Zend_Json::encode( $activityStage->validators );
	}
	
	public function saveAction()
	{
        $_POST['data']['Activity_Model_ActivityStage']['ds'] = tidy_repair_string(
			$_POST['data']['Activity_Model_ActivityStage']['ds'],
			array('hide-comments' => true,
				  'drop-proprietary-attributes' => true,
				  'bare' => true,
				  'word-2000' => true,
				  'logical-emphasis' => true),
			"utf8");
        
        parent::saveAction();
	}
}