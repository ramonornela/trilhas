<?php
class Form_FormController extends Controller
{
	//public $uses = array( "Form" , "Course" , "Period" , "FormCoursePeriod" , "FormGroupField" );

	public function indexAction()
	{
		$this->view->rs = $this->Form->fetchAll( null , 'id' );
		$this->render( "index" , "clearbox" );
	}
	
	public function inputAction()
	{
		$course_id = Zend_Filter::get( $this->_getParam( "courseId" ) , "int" );
		
		$user = new Zend_Session_NameSpace( 'user' );
		
		$id = Zend_Filter::get( $this->_getParam( "id" ) , "int" );
		
		$courses = $this->Course->fetchAll( 'id IN ( ' . $user->course->all . ' )' , "title" )->toArray();
		
		foreach ( $courses as $course ) {
			$relation[$course['id']]['course'] = $course;
			$relation[$course['id']]['period'] = $this->Period->findPeriodCourse( $course['id'] , $id );
		}
		
		$this->view->relation = $relation;
		
		if ( $id )
			$this->view->data = $this->Form->fetchRow( array( 'id =?' => $id ) , 'id' );
		
		
		$this->view->jsonValidate = Zend_Json::encode( $this->Form->validators );
		$this->view->form_id = $id;
		
		if ( $this->_getParam( "period" ) )
		{
			$this->render( "updateperiod" , "ajax" );
			return false;
		}
		
		$this->render( "input" , "clearbox" );
	}
	
	public function saveAction()
	{
		$user = new Zend_Session_NameSpace( 'user' );
		
		$input = $this->preSave();
		if( $input->isValid() )
		{
			$data = $this->setNull( $input->toArray() );
			$data['person_id'] = $user->person_id;
			
			if ( ! $data['status'] )
				$data['status'] = "PR";
				
			$id = $this->Form->save( $data );
			
			if ( $id ) 
			{
				if ( $_POST["course_period"] )
					$this->FormCoursePeriod->save( $id );
									
				$this->postSave( true , $input );
			}
			else
				$this->postSave( false , $input );
		}
		else
			$this->postSave( false , $input );
	}
	
	public function verificationPeriod( $course_id )
	{
		//$id = Zend_Filter::get( $this->_getParam( "id" ) , "int" );
		$period = $this->Period->findPeriodCourse( $course_id )->toArray();
		
		echo Zend_Json::encode( $rs );
		
		exit;
	}
	
	public function deleteAction()
	{
		$id = Zend_Filter::get( $this->_getParam('id') , 'int' );
		
//		$person = $this->FormCoursePeriod->fetchPerson( $id );
//		
//		if ( $person ){
//			$this->_helper->_flashMessenger->addMessage( $this->view->translate( "this form can not be deleted as there are registered users" ) );
//			$this->_redirect( "/form/form/" );
//		}
		
		$this->FormCoursePeriod->delete( $id , 'form_id' );
		$this->FormGroupField->delete( $id , 'form_id' );
		$this->Form->delete( $id );
		
		$this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted successfully" ) );
		$this->_redirect( "/form/form/" );
	}
	
}
?>
