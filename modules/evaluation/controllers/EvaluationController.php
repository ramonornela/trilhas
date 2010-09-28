<?php
class Evaluation_EvaluationController extends Controller {
    public function indexAction() {
        $user           = new Zend_Session_Namespace( "user" );
        $evaluation     = new Zend_Session_Namespace( "evaluation" );
        $evaluation->id = null;

        if ( Role::STUDENT == $user->role_id ) {
            $where = array( "started  <= ?" => date('Y-m-d' , strtotime("now")), "finished >= ?" => date('Y-m-d', strtotime("now")) );
        }else {
            $where = null;
        }

        $this->view->rs   = $this->Evaluation->fetchRelation( $where , array("name" , "id DESC") );
        $this->view->role = $user->role_id;

        $this->render( null , $this->getLayout() );

    }

    public function inputAction() {
        $evaluation = new Zend_Session_Namespace( "evaluation" );
        $id         = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "Int" );

        if ( $id ) {
            $evaluation->id  = $id;
            $this->view->rs = $this->EvaluationQuestion->fetchQuestion( $id );
        }

        $this->view->data = $this->Evaluation->createRow();

        parent::inputAction();
    }

    public function saveAction() {
        $user 	 = new Zend_Session_NameSpace( 'user' );
        $relation  = new Zend_Session_NameSpace( 'relation' );

        $_POST['number_question'] = Zend_Filter::filterStatic( $_POST['number_question'] , "int" );

        $input = $this->preSave();

        if( $input->isValid() ) {
            $data = $this->setNull( $input->toArray() );
            $data['person_id'] = $user->person_id;

            $id = $this->Evaluation->save( $data );

            if ( $id ) {
                $relation->redirect = "/evaluation/evaluation/input/id/$id";
                $this->_redirect( "/relation/index/model/Evaluation/div/evaluation/id/" . $id );
            }else {
                $this->postSave( false , $input );
            }
        }
        $this->postSave( false , $input );
    }

    public function viewEvaluationAction() {
        $id         = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "Int" );
        $evaluation = new Zend_Session_Namespace( "evaluation" );

        if($id){
            $evaluation->id = $id;
        }

        $evaluationModel    = new Evaluation();
        $evaluationQuestion = new EvaluationQuestion();

        $this->view->letters = range( "a" , "z" );
        $this->view->rs  = $evaluationQuestion->fetchQuestion( $evaluation->id );

        $this->view->evaluation = $evaluationModel->fetchRow( array( "id =?" => $evaluation->id ) );

        $this->render();
    }

    public function organizerAction() {
        $evaluation = new Zend_Session_Namespace( "evaluation" );

        $evaluationModel    = new Evaluation();
        $evaluationQuestion = new EvaluationQuestion();

        $this->view->rs = array_map('array_change_key_case' , $evaluationQuestion->fetchQuestion( $evaluation->id )->toArray() );

        $this->render();
    }

    public function saveOrganizerAction() {
        $data = Zend_JSON::decode( $_POST['json'] );

        $evaluation = new Zend_Session_Namespace( "evaluation" );

        foreach( $data as $key => $val ) {
            $data = null;
            $data['position']               = Zend_Filter::filterStatic( $val['position'] , "int" );
            $data['evaluation_id']          = $evaluation->id;
            $data['evaluation_question_id'] = Zend_Filter::filterStatic( $val['id'] , "int" );
            $data['note']                   = $this->EvaluationQuestionRel->fetchRow( array( 'evaluation_id =?' => $data['evaluation_id'] , 'evaluation_question_id =?' => $data['evaluation_question_id']  ) )->note;
            $this->EvaluationQuestionRel->save( $data );
        }

        $this->_helper->_flashMessenger->addMessage( $this->view->translate( "registered successfully" ) );
        $this->_redirect( "/evaluation/evaluation/input/id/$evaluation->id" );
    }
}