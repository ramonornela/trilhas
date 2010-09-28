<?php
class Activity_GroupController extends Controller 
{
	//public $uses = array( "ActivityGroup" , "Activity" , "ActivityGroupPerson" , "Person" , "Course" , "Discipline" , "Group" , "ActivityTextGroup");
	
	protected $_model = "ActivityGroup";

	public function indexAction()
	{
		$activity = new Zend_Session_NameSpace( 'activity' );
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );

        if( $id )
			$activity->id = $id;
		
		$this->view->rs = $this->ActivityGroup->fetchAll( array( "activity_id = ?" => $activity->id ) , 'id DESC' );
		
		$this->render();
	}	
	
	public function inputAction()
	{
		$activity = new Zend_Session_NameSpace( 'activity' );
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
		$discipline = Zend_Filter::filterStatic( $this->_getParam( "discipline_id" ) , "int" );
		$group      = Zend_Filter::filterStatic( $this->_getParam( "group_id" ) , "int" );
		
		if ( $discipline )
		{
			$this->discipline( $discipline );
			return false;
		}	
		
		if ( $group )
		{
			$this->group( $group );
			return false;
		}
		
		$this->view->checked = array();
		$this->view->all     = array();
		
		if ( $id )
		{
			$this->view->data    = $this->ActivityGroup->fetchRow( array( 'id =?' => $id ) );
			$this->view->checked = $this->toSelect( $this->Person->findPersonByActivity( $id , $activity->id )  , 'id' , 'name' , null );
			
			if ( ! $this->view->checked )
				$this->view->checked = array();
		}	
		$this->view->courses = $this->toSelect( $this->Course->fetchAll( null , "title" )  , 'id' , 'title' );
		$this->view->jsonValidate = Zend_Json::encode( $this->ActivityGroup->validators );
		$this->view->id = $id;
		
		$this->render();
	}
	
	public function findAction()
	{
		$id       = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
		$activity = new Zend_Session_NameSpace( 'activity' );
		
		$notIn = $this->ActivityGroupPerson->forIn( array ( "activity_id =?" => $activity->id ) , "person_id" );
		
		$this->view->checked = array();
		$this->view->all 	 = $this->toSelect( $this->findPersonStudent( $notIn ) , 'id' , 'name' , null );
		
		if ( ! $this->view->all )
			$this->view->all = array();
			
		if ( $id ){
			$this->view->checked = $this->toSelect( $this->Person->findPersonByActivity( $id , $activity->id )  , 'id' , 'name' , null );
			if ( ! $this->view->checked )
				$this->view->checked = array();
		}
		
		$this->view->jsonValidate = Zend_Json::encode( $this->ActivityGroup->validators );
		$this->view->id = $id;
		
		$this->render( "find" , "ajax" );
	}
	
	public function saveAction()
	{
		$activity = new Zend_Session_NameSpace( 'activity' );
		
		$input = $this->preSave();
			
		if ( $input->isValid() ) 
		{
			$data = $this->setNull( $input->toArray() );
			$data['activity_id'] = $activity->id;
			
			$id = $this->ActivityGroup->save( $data );
			
			if ( $id ) 
			{
				$this->ActivityGroupPerson->save( $id , $activity->id );
				
				$this->_helper->_flashMessenger->addMessage( $this->view->translate( "registered successfully" ) );
				$this->_redirect( "/activity/group/index" );
			}
			else
				$this->postSave( false , $input );
		}
		$this->postSave( false , $input );
		
	}
	
	public function findPersonStudent( $notIn = null )
	{
		if ( $_POST['course'] )
			$where['cd.course_id = ?'] = $_POST['course'];
		
		if( $_POST['discipline'] )
			$where['d.id = ?'] = $_POST['discipline'];
			
		if( $_POST['group'] )
			$where['g.id = ?'] = $_POST['group'];
			
		return $this->Person->findPersonStudent( $where , $notIn );	
	}
	
	public function discipline( $id )
	{
		$this->view->disciplines = $this->toSelect( $this->Discipline->fetchByCourse( $id ) , "id"  , "title" , $this->view->translate( "select" ) );
		$this->render( "discipline" , "ajax" );
	}
	
	public function group( $id )
	{
		$this->view->groups = $this->toSelect( $this->Group->fetchAll( array( "discipline_id = ?" => $id ) ) , "id" , "title" , $this->view->translate( "select" ) );
		$this->render( "group" , "ajax" );
	}
}