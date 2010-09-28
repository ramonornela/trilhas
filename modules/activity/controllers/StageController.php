<?php
class Activity_StageController extends Controller {
    protected $_model = "ActivityStage";

    public function indexAction() {
        $activity = new Zend_Session_NameSpace( 'activity' );
        $user     = new Zend_Session_NameSpace( 'user' );
        $id       = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
        $group_id = Zend_Filter::filterStatic( $this->_getParam( "group_id" ) , "int" );

        if( $id ) {
            $activity->id        = $id;
            $activity->group_id  = $group_id;
            $activity->person_id = $user->person_id;
        }

        if ( $activity->group_id )
            $value = $this->ActivityTextGroup->fetchRow( array( "activity_id =?" => $activity->id  , "activity_group_id =?" => $activity->group_id ) , "id DESC" );
        else
            $value = $this->ActivityTextPerson->fetchRow( array( "activity_id = ?" => $activity->id , "person_id =?" => $activity->person_id ) , "id DESC" );


        if ( $value->status == ActivityTextPerson::FINALITY_TEACHER )
            $this->_redirect( "/activity/text/view/" );

        $this->view->rs = $this->ActivityStage->fetchAll( array( "activity_id = ?" => $activity->id ) , "id" );

        $this->view->role = $user->role_id;

        $this->render();
    }

    public function inputAction() {
        $id = Zend_Filter::filterStatic( $this->_getParam("id") , "int" );

        if( $id ) {
            $this->view->data = $this->ActivityStage->fetchRow( array( "id = ?" => $id ) );
        }

        $this->view->jsonValidate = Zend_Json::encode( $this->ActivityStage->validators );

        $this->render();
    }

    public function saveAction() {
        $activity = new Zend_Session_NameSpace( 'activity' );

        $input = $this->preSave();
        if ( $input->isValid() ) {
            $data = $this->setNull( $input->toArray() );
            $data['activity_id'] = $activity->id;

            if ( $this->ActivityStage->save( $data ) ) {
                $this->_helper->_flashMessenger->addMessage( $this->view->translate( "registered successfully" ) );
                $this->_redirect( "/activity/stage/" );
            }
            else
                $this->postSave( false , $input );
        }
        $this->postSave( false , $input );
    }
}