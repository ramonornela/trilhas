<?php
class Activity_GroupController extends Application_Controller_Abstract
{
	protected $_model = "Activity_Model_ActivityGroup";
	
	public function indexAction()
	{
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );

		$activity       = new Zend_Session_NameSpace( 'activity' );
        $activityGroup  = new Activity_Model_ActivityGroup();

        if( $id ){
            $activity->id = $id;
        }
        
		$this->view->rs = $activityGroup->fetchAll( array( "activity_id = ?" => $activity->id ) , 'id DESC' );
	}	

	public function inputAction()
	{
		$id         = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
		$discipline = Zend_Filter::filterStatic( $this->_getParam( "discipline_id" ) , "int" );
		$group      = Zend_Filter::filterStatic( $this->_getParam( "group_id" ) , "int" );


        $person         = new Share_Model_Person();
        $course         = new Station_Model_Course();
		$activity       = new Zend_Session_NameSpace( 'activity' );
		$activityGroup  = new Activity_Model_ActivityGroup();

        $this->view->data = $activityGroup->createRow();

		if ( $discipline ){
			$this->discipline( $discipline );
			return false;
		}	
		
		if ( $group ){
			$this->group( $group );
			return false;
		}
		
		$this->view->checked = array();
		$this->view->all     = array();
		
		if ( $id ){
			$this->view->data    = $activityGroup->fetchRow( array( 'id =?' => $id ) );
			$this->view->checked = Preceptor_Util::toSelect( $person->findPersonByActivity( $id , $activity->id )  , array( "id" , "name" ) );
			
			if ( ! $this->view->checked )
				$this->view->checked = array();
		}

		$this->view->courses = Preceptor_Util::toSelect( $course->fetchAll( null , "name" ) , array( "id" , "name" ) );
		$this->view->jsonValidate = Zend_Json::encode( $activityGroup->validators );
		$this->view->id = $id;
	}
	
	public function findAction()
	{
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
        
        $person                 = new Share_Model_Person();
		$activity               = new Zend_Session_NameSpace( 'activity' );
        $activityGroup          = new Activity_Model_ActivityGroup();
        $activityGroupPerson    = new Activity_Model_ActivityGroupPerson();
		
		$notIn = $activityGroupPerson->forIn( array ( "activity_id =?" => $activity->id ) , "person_id" );
		
		$this->view->checked = array();
		$this->view->all 	 = Preceptor_Util::toSelect( $this->findPersonStudent( $notIn ) , array( 'id' , 'name' ) );
		
		if ( ! $this->view->all )
			$this->view->all = array();
			
		if ( $id ){
			$this->view->checked = Preceptor_Util::toSelect( $person->findPersonByActivity( $id , $activity->id )  , array( 'id' , 'name' ) );
			if ( ! $this->view->checked )
				$this->view->checked = array();
		}
		
		$this->view->jsonValidate = Zend_Json::encode( $activityGroup->validators );
		$this->view->id = $id;

		$this->_helper->layout->setLayout('clear');
	}
	
	public function findPersonStudent( $notIn = null )
	{
		$person = new Share_Model_Person();

        if ( $_POST['course'] )
			$where['cd.course_id = ?'] = $_POST['course'];
		
		if( $_POST['discipline'] )
			$where['d.id = ?'] = $_POST['discipline'];
			
		if( $_POST['group'] )
			$where['g.id = ?'] = $_POST['group'];
			
		return $person->findPersonStudent( $where , $notIn );
	}
	
	public function discipline( $id )
	{
        $discipline = new Station_Model_Discipline();
        
		$this->view->disciplines = Preceptor_Util::toSelect( $discipline->fetchByCourse( $id ) , array( "id" , "name" ) );
		$this->_helper->layout->setLayout('clear');
        $this->render( "discipline" );
	}
	
	public function group( $id )
	{
        $group = new Station_Model_ClassModel();

		$this->view->groups = Preceptor_Util::toSelect( $group->fetchAll( array( "discipline_id = ?" => $id ) ) , array( "id" , "name" ) );
		$this->_helper->layout->setLayout('clear');
        $this->render( "group" );
	}
}