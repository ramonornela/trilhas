<?php
class Activity_ActivityController extends Controller {
    public function indexAction() {
        $user  = new Zend_Session_Namespace( "user" );

        if ( Role::STUDENT == $user->role_id ) {
            $where = array( "started  <= ?" => date('Y-m-d'),
                    "finished >= ?" => date('Y-m-d'),
                    "composition_type = ?" => Activity::INDIVIDUALLY );
        }else {
            $where = array( "composition_type = ?" => Activity::INDIVIDUALLY );
        }

        $this->view->role      = $user->role_id;
        $this->view->person_id = $user->person_id;

        $this->view->rs      = $this->Activity->fetchRelation( $where , "title" );
        $this->view->grouped = $this->ActivityGroup->fetchActivityByGroup( $user->person_id );
        
        $this->render();
    }

    public function inputAction() {
        $id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );

        if( $id ) {
            $this->view->data = $this->Activity->fetchRow( array( "id = ?" => $id ) );
        }


        $this->view->composition = array(
                Activity::INDIVIDUALLY => $this->view->translate("individually"),
                Activity::GROUPED => $this->view->translate("grouped")
        );

        $this->view->jsonValidate = Zend_Json::encode( $this->Activity->validators );

        $this->render();
    }

    public function saveAction() {
        $user 	   = new Zend_Session_NameSpace( 'user' );
        $relation  = new Zend_Session_NameSpace( 'relation' );

        if( isset( $_POST['finished'] ) && $_POST['finished'] ) {
            $_POST['finished'] = date( "Y-m-d" , strtotime( $_POST['finished'] ) );
        }

        if(isset($_POST['started']) && $_POST['started']) {
            $_POST['started'] = date( "Y-m-d" , strtotime( $_POST['started'] ) );
        }

        $input = $this->preSave();

        if ( $input->isValid() ) {
            $data = $this->setNull( $input->toArray() );
            $data['person_id'] = $user->person_id;

            $id = $this->Activity->saveActivityStage( $data );

            if ( $id ) {
                if( $data['composition_type'] == Activity::GROUPED )
                    $this->_redirect( "/activity/group/index/id/" . $id );
                else {
                    $relation->redirect = "/activity/activity/";
                    $this->_redirect( "/relation/index/model/Activity/div/activity/id/" . $id );
                }
            }
            else
                $this->postSave( false , $input );
        }
        else {
            $this->view->validate 	 = $input->getMessages();
            $this->view->data 	  	 = $input->toView();

            $composition = array(
                    array( "ID" => Activity::INDIVIDUALLY, "NAME" => $this->view->translate("individually") ),
                    array( "ID" => Activity::GROUPED, "NAME" => $this->view->translate("grouped") )
            );

            $this->view->composition = $this->toSelect( $composition );

            $this->render( "input" );
        }
    }

    public function deleteAction() {
        $id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );

        try {
            $this->ActivityStage->delete( $id , "activity_id" );
            $this->Activity->delete( $id );
            $this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted successfully" ) );
        }
        catch( Exception $e ) {
            $this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted error" ) );
        }

        $this->_redirect( "/activity/activity/index" );
    }
}