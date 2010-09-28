<?php
class Evaluation_CorrectController extends Controller {

    public function indexAction() {
        $evaluation = new Zend_Session_Namespace( "evaluation" );
        $evaluation->id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
        $this->view->rs = $this->EvaluationReply->fetchPersonByReply( $evaluation->id );

        $this->render();
    }

    public function viewAction() {
        $evaluation    = new Zend_Session_Namespace( "evaluation" );
        $person_id     = Zend_Filter::filterStatic( $this->_getParam( "person_id" ) , "int" );
        $evaluation_id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );

        if ( $evaluation_id )
            $evaluation->id = $evaluation_id;
        
        $this->_redirect( "/evaluation/correct/view-answer/evaluation_id/$evaluation->id/person_id/$person_id" );

        if ( $person_id ) {
            $evaluation->person_id = $person_id;
        }

        $verify = $this->EvaluationReply->fetchRow( array( "evaluation_id =?" => $evaluation->id , "person_id =?" => $evaluation->person_id ) );

        if ( ! $verify ) {
            $this->render( 'notfinalizing' );
            return false;
        }

        $this->view->rs        = $this->EvaluationQuestion->fetchQuestion( $evaluation->id );
        $this->view->person_id = $evaluation->person_id;
        $this->view->submit    = Zend_Filter::filterStatic( $this->_getParam( "submit" ) , "int" );

        $this->render();
    }

    public function savenoteAction() {
        $evaluation = new Zend_Session_Namespace( "evaluation" );

        foreach ( $_POST['note'] as $key => $note ) {

            $this->EvaluationReplyNote->delete( $key , 'evaluation_reply_id' );

            $data['evaluation_reply_id'] = $key;
            $data['person_id']           = $evaluation->person_id;
            $data['note']                = str_replace( "." , "," , $note );
            $this->EvaluationReplyNote->save( $data );
        }
        $this->_helper->_flashMessenger->addMessage( $this->view->translate( "registered successfully" ) );
        $this->_redirect( "/evaluation/correct/view/submit/1" );
    }

    public function viewAnswerAction() {
        $evaluation_id = Zend_Filter::filterStatic( $this->_getParam( "evaluation_id" ) , "int" );
        $person_id     = Zend_Filter::filterStatic( $this->_getParam( "person_id" ) , "int" );

        $user = new Zend_Session_Namespace( "user" );

        $evaluationQuestion = new EvaluationQuestion();
        $evaluationReply    = new EvaluationReply();
        $evaluationModel	= new Evaluation();

        $version = $evaluationReply->fetchReplyVersion( $person_id , $evaluation_id )->current()->version;

        if( !$version ) {
            $this->_helper->_flashMessenger->addMessage( $this->view->translate( "O aluno ainda não respondeu essa avaliação" ) );
            $this->_redirect( "/evaluation/evaluation/index/" );
        }

        $textarea = $evaluationQuestion->fetchFieldTextArea( $evaluation_id , $person_id , $version );

        $response = true;

        if ( count( $textarea ) ) {
            foreach( $textarea as $val ) {
                if( !$val->response_note ) {
                    //$response = false;
                }
            }
        }
        
        if ( $response ) {
            $this->view->version    = $version;
            $this->view->person     = $this->Person->fetchRow(array('id = ?' => $person_id));
            $this->view->rs         = $evaluationQuestion->fetchQuestion( $evaluation_id );
            $this->view->evaluation = $evaluationModel->fetchRow(array('id =?' => $evaluation_id));
        }

        $this->view->evaluation = $evaluationModel->fetchRow( array( "id =?" => $evaluation_id ) );
        
        $this->render();
    }

    public function saveNoteTextareaAction() {
        $person_id = Zend_Filter::filterStatic( $this->_getParam( "person_id" ) , "int" );

        $evaluation = new Zend_Session_Namespace( "evaluation" );

        foreach( $_POST as $reply_id => $val ) {
            $data['evaluation_reply_id'] = $reply_id;
            $data['person_id']           = $person_id;
            $data['note']                = str_replace( "." , "," , $val['note'] );
            $data['ds_teacher']          = $val['comment'];

            try {
                $this->EvaluationReplyNote->delete( array( 'evaluation_reply_id' => $reply_id, 'person_id' => $person_id ) );
                $this->EvaluationReplyNote->save( $data );
                unset($data);
            }catch( Exception $e ) {}

        }

        $this->_redirect( "/evaluation/reply/view/person_id/$person_id/id/$evaluation->id" );
    }


}