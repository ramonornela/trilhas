<?php
class RelationController extends Application_Controller_Abstract 
{
	public function indexAction()
	{
		$user		   = new Zend_Session_NameSpace( 'user' );
		$relation	   = new Zend_Session_NameSpace( 'relation' );
		$course		   = new Station_Model_Course();
		$discipline	   = new Station_Model_Discipline();
		$classPerson   = new Station_Model_ClassPerson();
		$person		   = new Share_Model_Person();

		$relationModel = new Application_Model_Relation();

		$id         = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
		$model      = $this->_getParam( "model" );
		
		$relation->id         = $id;
		$relation->model      = $model;
		
		$Model = new $model();
		
		$this->view->jsonRelation = Zend_Json::encode( array() );
		
		$rs = $Model->find( $id )->current();
		
		$where = $course->select()->where( 'id IN ( ' . $user->course->all . ' )' );
		$courses  = $course->fetchAll( $where );
		
		if( isset($rs->relation) ){

            $result = $relationModel->fetchAll( array( "relation = ?" => $rs->relation ) );
			
			foreach( $result as $value )
			{
				if( $value->course_id )
				{
					$relations[] = array( 
						"type" 	=> "course_id", 
						"id" 	=> $value->course_id, 
						"text" 	=> $course->find( $value->course_id )->current()->name,
                        "model" => $model
					);
				}
				
				if( $value->discipline_id )
				{
					$relations[] = array( 
						"type" 	=> "discipline_id", 
						"id" 	=> $value->discipline_id, 
						"text" 	=> $discipline->find( $value->discipline_id )->current()->name,
                        "model" => $model
					);
				}
				
				if( $value->class_id )
				{
					$relations[] = array( 
						"type" 	=> "class_id",
						"id" 	=> $value->class_id,
						"text" 	=> $classModel->find( $value->class_id )->current()->name,
                        "model" => $model
					);
				}
				
				if( $value->person_id )
				{
					$relations[] = array( 
						"type" 	=> "person_id", 
						"id" 	=> $value->person_id, 
						"text" 	=> $person->find( $value->person_id )->current()->name,
                        "model" => $model
					);
				}
			}
			
			$this->view->jsonRelation = Zend_Json::encode( $relations );
		}
		
         $this->view->model      = $model;
         
		 $this->view->courses    = Preceptor_Util::toSelect( $courses );
		 $this->view->user 	     = $user; 
	}
	
	public function disciplineAction()
	{
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
		
		$user 	  = new Zend_Session_NameSpace( 'user' );
		$relation = new Zend_Session_NameSpace( 'relation' );
		$course	  = new Station_Model_Course();

		$rs = $course->find( $id )->current();
		$discpline = $rs->findManyToManyRowset(
			"Station_Model_Discipline",
			"Station_Model_CourseDiscipline",
			null,
			null,
			$rs->getTable()->select()->where( "id IN( {$user->discipline->all} )" )
		);
																									
		$this->view->model = $relation->model;
		
		$this->view->disciplines = Preceptor_Util::toSelect( $discpline );

        $this->_helper->layout->setLayout('clear');
	}
	
	public function groupAction()
	{
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
		
		$user 	  = new Zend_Session_NameSpace( 'user' );
		$relation = new Zend_Session_NameSpace( 'relation' );
		$classModel   = new Station_Model_ClassModel();
		
		$groups = $classModel->fetchAll( array( 'id IN ( ' . $user->group->all . ' )' , 'discipline_id =?' => $id ) );
		$this->view->model      = $relation->model;
		$this->view->groups = Preceptor_Util::toSelect( $groups );
		
		$this->_helper->layout->setLayout('clear');
	}
	
	public function personAction()
	{
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
		
		$user 	  = new Zend_Session_NameSpace( 'user' );
		$relation = new Zend_Session_NameSpace( 'relation' );
		$classModel   = new Station_Model_ClassModel();

		$group = $classModel->find( $id )->current();
		
		$persons = $group->findManyToManyRowSet( "Share_Model_Person" ,
													"Station_Model_ClassPerson" );

		$this->view->model   = $relation->model;
		$this->view->persons = Preceptor_Util::toSelect( $persons );
		
		$this->_helper->layout->setLayout('clear');
	}
	
	public function indexfolderAction(){}
}
