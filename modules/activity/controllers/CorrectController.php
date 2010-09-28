<?php
class Activity_CorrectController extends Controller 
{
	public function indexAction()
	{
		$activity = new Zend_Session_NameSpace( 'activity' );
		$id   = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
		$group_id = Zend_Filter::filterStatic( $this->_getParam( "group_id" ) , "int" );
		
		if( $id ){
                    $activity->id = $id;
                    $this->view->activity = $this->Activity->fetchRow(array('id=?'=>$id));
                }
		
		if( $group_id ){
			$this->view->rs = $this->ActivityTextGroup->findTextFinality( $activity->id );
			$this->render("indexgroup");
		}else{
                        $this->view->rs    = $this->ActivityTextPerson->findTextFinality( $activity->id );
			$this->render("indexperson");
		}
	}	
	
	public function inputAction()
	{
		$activity   = new Zend_Session_NameSpace( 'activity' );
                $id   = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
		if( $id ){
                    $activity->id = $id;
                }

		$person_id  = Zend_Filter::filterStatic( $this->_getParam( "person_id" ) , "int" );
		$version_id = Zend_Filter::filterStatic( $this->_getParam( "version_id" ) , "int" );
		$group_id   = Zend_Filter::filterStatic( $this->_getParam( "group_id" ) , "int" );
			
		if ( $group_id )
			$activity->group_id = $group_id;
			
		if ( $person_id )
			$activity->person_id = $person_id;
		
		if ( !$activity->group_id )
		{
			$this->view->rs = $this->ActivityTextPerson->fetchAll( array( "activity_id =?" => $activity->id , "person_id =?" => $activity->person_id ) , "id DESC" );
			$this->view->jsonValidate = Zend_Json::encode( $this->ActivityTextPerson->validators );
		}	
		else
		{
			$this->view->rs = $this->ActivityTextGroup->fetchAll( array( "activity_id =?" => $activity->id  , "activity_group_id =?" => $activity->group_id ) , "id DESC" );
			$this->view->jsonValidate = Zend_Json::encode( $this->ActivityTextGroup->validators );
		}
			
		if( $version_id )
		{
			if ( !$activity->group_id ){
				$this->view->data = $this->ActivityTextPerson->fetchRow( array( "id = ?" => $version_id ) );
                        }else{
				$this->view->data = $this->ActivityTextGroup->fetchRow( array( "id = ?" => $version_id ) );
                        }

			$this->render( "update_input" , "ajax");
			return false;
		}
                
                $this->view->stage = $this->ActivityStage->fetchAll( array( "activity_id = ?" => $activity->id ) , "id" );
                $this->view->activity = $this->Activity->fetchRow(array('id=?'=>$activity->id));

		$this->render();
	}

	public function saveAction()
	{
		$user 	   = new Zend_Session_NameSpace( 'user' );
		$activity  = new Zend_Session_NameSpace( 'activity' );
			
		if ( ! $activity->group_id ){
            $param = $this->ActivityTextPerson;
        }else{
            $param = $this->ActivityTextGroup;
        }

        $input = $this->preSave( $param );
		if ( $input->isValid() ) 
		{
			$data = $this->setNull( $input->toArray() );
			if ( ! $activity->group_id )
				$this->ActivityTextPerson->save( $data , ActivityTextPerson::SAVE_TEACHER , $activity->person_id );
			else
				$this->ActivityTextGroup->save( $data , ActivityTextPerson::SAVE_TEACHER  );
			
			$this->_helper->_flashMessenger->addMessage( $this->view->translate( "registered successfully" ) );
			$this->_redirect( "/activity/activity" );
		}
		
		$this->postSave( false , $input );
	}
	
	public function finalizingAction()
	{
		$activity = new Zend_Session_NameSpace( 'activity' );
		
		if ( ! $activity->group_id ){
            $param = $this->ActivityTextPerson;
        }else{
            $param = $this->ActivityTextGroup;
        }

        $input = $this->preSave( $param );		
		if ( $input->isValid() ) 
		{
			$data = $this->setNull( $input->toArray() );
			
			if ( ! $activity->group_id )
				$this->ActivityTextPerson->save( $data , ActivityTextPerson::FINALITY_TEACHER , $activity->person_id );
			else
				$this->ActivityTextGroup->save( $data , ActivityTextPerson::FINALITY_TEACHER  );
				
			$this->_helper->_flashMessenger->addMessage( $this->view->translate( "registered successfully" ) );
			$this->_redirect( "/activity/text/view/" );
		}
		$this->postSave( false , $input );
	}
	
	
}