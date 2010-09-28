<?php
class Activity_TextController extends Controller {
    //public $uses = array( "ActivityTextPerson" , "ActivityTextGroup" , "Activity" , "ActivityGroup" , "ActivityStage" , "Person" );

    public function indexAction() {
        $this->_redirect( "activity/activity" );
    }

    public function inputAction() {
        $activity = new Zend_Session_NameSpace( 'activity' );
        $user = new Zend_Session_NameSpace( 'user' );
        $id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );

        $this->view->user = $user;

        if ( ! $activity->group_id ) {
            $this->view->rs = $this->ActivityTextPerson->fetchAll( array( "activity_id =?" => $activity->id , "person_id =?" => $user->person_id ) , "id DESC" );
            $this->view->jsonValidate = Zend_Json::encode( $this->ActivityTextPerson->validators );
        }
        else {
            $this->view->rs = $this->ActivityTextGroup->fetchAll( array( "activity_id =?" => $activity->id  , "activity_group_id =?" => $activity->group_id ) , "id DESC" );
            $this->view->jsonValidate = Zend_Json::encode( $this->ActivityTextGroup->validators );
        }

        if( $id ) {
            if ( ! $activity->group_id )
                $this->view->data = $this->ActivityTextPerson->fetchRow( array( "id = ?" => $id ) );
            else
                $this->view->data = $this->ActivityTextGroup->fetchRow( array( "id = ?" => $id ) );

            $this->render( "update_input" , "ajax");
            return false;
        }

        $this->render( "input" , "ajax");
    }

    public function viewAction() {
        $activity   = new Zend_Session_NameSpace( 'activity' );
        $user = new Zend_Session_NameSpace( 'user' );

        $id         = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
        $version_id = Zend_Filter::filterStatic( $this->_getParam( "version_id" ) , "int" );
        $person_id  = Zend_Filter::filterStatic( $this->_getParam( "person_id" ) , "int" );
        $group_id   = Zend_Filter::filterStatic( $this->_getParam( "group_id" ) , "int" );

        if ( $id )
            $activity->id = $id;

        if ( $group_id )
            $activity->group_id = $group_id;

        if ( $person_id )
            $activity->person_id = $person_id;

        if ( ! $activity->group_id ) {
            $this->view->rs = $this->ActivityTextPerson->fetchAll( array( "activity_id =?" => $activity->id , "person_id =?" => $activity->person_id ) , "id DESC" );
            $table = $this->ActivityTextPerson;
        }
        else {
            $this->view->rs = $this->ActivityTextGroup->fetchAll( array( "activity_id =?" => $activity->id  , "activity_group_id =?" => $activity->group_id ) , "id DESC" );
            $table = $this->ActivityTextGroup;
        }

        $this->view->stage = $this->ActivityStage->fetchAll( array( "activity_id = ?" => $activity->id ) , "id" );

        if( $version_id )
            $this->view->data = $table->fetchRow( array( "id = ?" => $version_id ) );

        $this->render();
    }

    public function listAction() {
        $activity = new Zend_Session_NameSpace( 'activity' );
        $user = new Zend_Session_NameSpace( 'user' );

        if ( ! $activity->group_id )
            $this->view->rs = $this->ActivityTextPerson->fetchAll( array( "activity_id =?" => $activity->id , "person_id =?" => $user->person_id ) , "id DESC" );
        else
            $this->view->rs = $this->ActivityTextGroup->fetchAll( array( "activity_id =?" => $activity->id  , "activity_group_id =?" => $activity->group_id ) , "id DESC" );

        $this->render( null , "ajax" );
    }

    public function saveAction() {
        $user 	  = new Zend_Session_NameSpace( 'user' );
        $activity = new Zend_Session_NameSpace( 'activity' );

        if ( !$activity->group_id ) {
            $param = $this->ActivityTextPerson;
        }else {
            $param = $this->ActivityTextGroup;
        }

        $input = $this->preSave( $param );

        if ( $input->isValid() ) {
            $data = $this->setNull( $input->toArray() );

            if ( !$activity->group_id )
                $this->ActivityTextPerson->save( $data , ActivityTextPerson::SAVE_STUDENT );
            else
                $this->ActivityTextGroup->save( $data , ActivityTextPerson::SAVE_STUDENT  );

            $this->_redirect( "/activity/text/list" );
        }
        $this->postSave( false , $input );
    }

    public function finalizingAction() {
        $user 	  = new Zend_Session_NameSpace( 'user' );
        $activity = new Zend_Session_NameSpace( 'activity' );

        if ( ! $activity->group_id ) {
            $param = $this->ActivityTextPerson;
        }else {
            $param = $this->ActivityTextGroup;
        }

        $input = $this->preSave( $param );

        if ( $input->isValid() ) {
            $data = $this->setNull( $input->toArray() );

            if ( ! $activity->group_id )
                $this->ActivityTextPerson->save( $data , ActivityTextPerson::FINALITY_STUDENT );
            else
                $this->ActivityTextGroup->save( $data , ActivityTextPerson::FINALITY_STUDENT );

            $this->_helper->_flashMessenger->addMessage( $this->view->translate( "registered successfully" ) );
            $this->_redirect( "/activity/activity" );
        }

        $this->postSave( false , $input );
    }

}