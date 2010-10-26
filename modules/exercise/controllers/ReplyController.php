<?php
class Evaluation_ReplyController extends Controller {
    protected $_model= "EvaluationReply";

    public function indexAction() {
        $evaluation = new Zend_Session_Namespace( "evaluation" );
        $user 	    = new Zend_Session_NameSpace( 'user' );

        $evaluationModel    = new Evaluation();
        $evaluationReply    = new EvaluationReply();
        $evaluationQuestion = new EvaluationQuestion();
        $evaluationPerson   = new EvaluationPerson();
        $evaluation->id  = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "Int" );
        $evaluation_rand = $evaluationModel->fetchRow( array( "id =?" => $evaluation->id ) );

        $rs = $evaluationPerson->fetchRow( array( 'person_id =?' => $user->person_id , 'evaluation_id =?' => $evaluation->id ) );

        if( $evaluation_rand->attempts == Status::INACTIVE ) {
            if ( $rs ) {
                $this->_helper->_flashMessenger->addMessage( $this->view->translate( "you can not redo the assessment" ) );
                $this->_redirect( "evaluation/evaluation/index/" );
            }
        }

        if( $evaluation->id && $user->person_id ) {
            if( !$rs ) {
                $data['dt']            = date( 'Y-m-d');
                $data['person_id']     = $user->person_id;
                $data['evaluation_id'] = $evaluation->id;
                $evaluationPerson->save( $data );

                $data['group_id'] = $user->group_id;
                $data['note']     = 0;

                $evaluationNote = new EvaluationNote();
                $evaluationNote->delete( array( 'evaluation_id' => $evaluation->id, 'person_id' => $user->person_id ) );
                $evaluationNote->save($data);
                unset($data);
            }
        }

        $this->view->letters    = array( '' => '' , 'A' => 'A' , 'B' => 'B' , 'C' => 'C' , 'D' => 'D' , 'E' => 'E' , 'F' => 'F' , 'G' => 'G' , 'H' => 'H' , 'I' => 'I' , 'J' => 'J' , 'K' => 'K' , 'L' => 'L' , 'M' => 'M' , 'N' => 'N' , 'O' => 'O' , 'P' => 'P' , 'Q' => 'Q' , 'R' => 'R' , 'S' => 'S' , 'T' => 'T' , 'U' => 'U' , 'V' => 'V' , 'W' => 'W' , 'X' => 'X' , 'Y' => 'Y' , 'Z' => 'Z' );
        $this->view->rs         = $evaluationQuestion->fetchQuestion( $evaluation->id );
        $this->view->evaluation = $evaluation_rand;

        $this->render();
    }

    public function saveAction() {
        $user               = new Zend_Session_Namespace( "user" );
        $evaluation         = new Zend_Session_Namespace( "evaluation" );
        $evaluationModel    = new Evaluation();
        $evaluationReply    = new EvaluationReply();

        $version = 1;

        $attempts = $evaluationModel->fetchRow( array( "id =?" => $evaluation->id ) )->attempts;
        if( $attempts == Status::ACTIVE ) {
            $version = $evaluationReply->fetchReplyVersion( $user->person_id , $evaluation->id )->current()->version;
            $version++;
        }

        foreach ( $_POST as $key => $data ) {
            $save['evaluation_question_id'] = $key;
            $save['person_id'] = $user->person_id;
            $save['evaluation_id'] = $evaluation->id;
            $save['version'] = $version;

            if( is_string($data) ) {
                $save['value'] = $data;
                try {
                    $evaluationReply->save( $save );
                }catch ( Exception $e ) {}
                
            }else {
                foreach ( $data as $key => $val ) {
                    $save['value'] = $val;
                    if ( $key ) {
                        $save['evaluation_value_id'] = $key;
                    }else {
                        $save['evaluation_value_id'] = null;
                    }
                    try {
                        $evaluationReply->save( $save );
                    }catch ( Exception $e ) {

                    }
                }
            }
            unset( $save );
        }

        $this->_helper->_flashMessenger->addMessage( $this->view->translate( "registered successfully" ) );

        $this->_redirect( "/evaluation/reply/view/id/$evaluation->id" );
    }


    public function saveNoteAction() {
        $note        = Zend_Filter::filterStatic( $this->_getParam( "note" ) , "HtmlEntities" );
        $certificate = Zend_Filter::filterStatic( $this->_getParam( "certificate" ) , "HtmlEntities" );
        $person_id   = Zend_Filter::filterStatic( $this->_getParam( "person_id" ) , "int" );
        
        $user 	     = new Zend_Session_NameSpace( 'user' );
        $evaluation  = new Zend_Session_Namespace( "evaluation" );

        $evaluationNote = new EvaluationNote();

        //$result = $evaluationNote->fetchRow( array( 'person_id =?' => $user->person_id , 'evaluation_id =?' => $evaluation->id , 'group_id =?' => $user->group_id ) );

        if(empty($person_id)){
            $person_id = $user->person_id;
        }

        if( $note ) {
            $evaluationNote->delete( array( 'person_id' => $person_id , 'evaluation_id' => $evaluation->id ) );
            $data['note']          = str_replace( '.' , ',' , $note );
            $data['person_id']     = $person_id;
            $data['group_id']      = $user->group_id;
            $data['evaluation_id'] = $evaluation->id;

            $evaluationNote->save( $data );
        }

        if( $user->role_id == Role::INSTITUTION && $person_id ) {

            $personGroupModel = new PersonGroup();
            $personGroup = $personGroupModel->fetchRow( array( 'person_id =?' => $person_id , 'group_id =?' => $user->group_id ) )->toArray();
            if($certificate && $personGroup && (!$personGroup['certificate'] || is_null($personGroup['certificate'])) ) {
                $personGroup['certificate'] = md5( $personGroup['person_id']."-".$personGroup['group_id'] );
                $personGroup['status']      = Status::APPROVED;

                debug($personGroupModel->delete( array( 'person_id' => $personGroup['person_id'], 'group_id' => $personGroup['group_id'], 'role_id' => $personGroup['role_id']) ));
                debug($personGroupModel->save($personGroup),1);
            }
        }else {
            $personGroupModel = new PersonGroup();
            $personGroup = $personGroupModel->fetchRow( array( 'person_id =?' => $user->person_id , 'group_id =?' => $user->group_id ) )->toArray();
            if($certificate && $personGroup && (!$personGroup['certificate'] || is_null($personGroup['certificate'])) ) {

                $personGroup['certificate'] = md5( $personGroup['person_id']."-".$personGroup['group_id'] );
                $personGroup['status']      = Status::APPROVED;

                $personGroupModel->delete( array( 'person_id' => $personGroup['person_id'], 'group_id' => $personGroup['group_id'], 'role_id' => $personGroup['role_id']) );
                $personGroupModel->save($personGroup);
            }
        }

        $this->_helper->viewRenderer->setNoRender();
    }

    public function viewAction() {
        $evaluation_id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
        $person_id = Zend_Filter::filterStatic( $this->_getParam( "person_id" ) , "int" );

        $user = new Zend_Session_Namespace( "user" );
        
        if(empty($person_id)){
            $person_id = $user->person_id;
        }

        $evaluation = new Zend_Session_Namespace( "evaluation" );
        $evaluation->id = $evaluation_id;

        $evaluationQuestion = new EvaluationQuestion();
        $evaluationReply    = new EvaluationReply();
        $evaluationModel	= new Evaluation();

        $version = $evaluationReply->fetchReplyVersion( $person_id , $evaluation_id )->current()->version;

        if( !$version ) {
            $this->_helper->_flashMessenger->addMessage( $this->view->translate( "you have not answered this assessment" ) );
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
            $this->view->person_id  = $person_id;
            $this->view->rs         = $evaluationQuestion->fetchQuestion( $evaluation_id );
        }

        $this->view->evaluation = $evaluationModel->fetchRow(array('id =?' => $evaluation_id));
        $this->render();
    }

    public function studentsEvaluatedAction() {
        $evaluation = new Zend_Session_Namespace( "evaluation" );
        $user = new Zend_Session_Namespace( "user" );

        $evaluation->id   = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "Int" );
        $evaluationPerson = new EvaluationPerson();
        $evaluationModel  = new Evaluation();

        $this->view->students = $evaluationPerson->fetchEvaluated($evaluation->id);
        $this->view->user = $user;
        $this->view->evaluation_id   = $evaluation->id;
        $this->view->evaluation_name = $evaluationModel->fetchRow(array('id =?' => $evaluation->id))->name;

        $this->render();
    }

    public function deleteAction() {
        $person_id = Zend_Filter::filterStatic( $this->_getParam( "person_id" ) , "Int" );
        $evaluation = new Zend_Session_Namespace( "evaluation" );

        $user                = new Zend_Session_Namespace( "user" );
        $evaluationNote      = new EvaluationNote();
        $evaluationReply     = new EvaluationReply();
        $evaluationPerson    = new EvaluationPerson();
        $evaluationReplyNote = new EvaluationReplyNote();

        $replys = $evaluationReply->fetchIdReply( $evaluation->id , $person_id );
        foreach( $replys as $val ) {
            $evaluationReplyNote->delete( array( "evaluation_reply_id" => $val->id ) );
            $evaluationReply->delete( array( "id" => $val->id ) );
        }

        $evaluationNote->delete( array( 'person_id' => $person_id , 'evaluation_id' => $evaluation->id , 'group_id' => $user->group_id ) );
        $evaluationPerson->delete( array( 'evaluation_id' => $evaluation->id , 'person_id' => $person_id ) );

        $this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted successfully" ) );
        $this->_redirect( "/evaluation/reply/students-evaluated/id/$evaluation->id" );
    }

    public function certificateRefreshAction() {
        $this->view->user      = new Zend_Session_Namespace( "user" );
        $this->view->person_id = Zend_Filter::filterStatic( $this->_getParam( "person_id" ) , "int" );

        $this->render(null , 'ajax');
    }
}