<?php
class Evaluation_ReplyController extends Application_Controller_Abstract
{
	protected $_model= "Evaluation_Model_EvaluationReply";
	
	public function indexAction()
	{
		$evaluation = new Zend_Session_Namespace( "evaluation" );
		$user 	    = new Zend_Session_NameSpace( 'user' );

        $evaluationModel    = new Evaluation_Model_Evaluation();
        $evaluationReply    = new Evaluation_Model_EvaluationReply();
        $evaluationQuestion = new Evaluation_Model_EvaluationQuestion();

        $evaluation->id  = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "Int" );
		$evaluation_rand = $evaluationModel->fetchRow( array( "id =?" => $evaluation->id ) );

        if( $evaluation_rand->attempts == Station_Model_Status::INACTIVE ){
            if ( $evaluationReply->fetchEvaluationUser( $user->person_id , $evaluation->id ) ){
                $this->_helper->_flashMessenger->addMessage( $this->view->translate( "you can not redo the assessment" ) );
                $this->_redirect( "evaluation/evaluation/index/" );
            }
        }
        
        $this->view->letters = array( '' => '' , 'A' => 'A' , 'B' => 'B' , 'C' => 'C' , 'D' => 'D' , 'E' => 'E' , 'F' => 'F' , 'G' => 'G' , 'H' => 'H' , 'I' => 'I' , 'J' => 'J' , 'K' => 'K' , 'L' => 'L' , 'M' => 'M' , 'N' => 'N' , 'O' => 'O' , 'P' => 'P' , 'Q' => 'Q' , 'R' => 'R' , 'S' => 'S' , 'T' => 'T' , 'U' => 'U' , 'V' => 'V' , 'W' => 'W' , 'X' => 'X' , 'Y' => 'Y' , 'Z' => 'Z' );
		$this->view->rs  = $evaluationQuestion->fetchQuestion( $evaluation->id );
		
		$this->view->evaluation = $evaluation_rand;

	}
	
	public function saveAction()
	{
		$user               = new Zend_Session_Namespace( "user" );
		$evaluation         = new Zend_Session_Namespace( "evaluation" );
		$evaluationModel    = new Evaluation_Model_Evaluation();
        $evaluationReply    = new Evaluation_Model_EvaluationReply();
        
        $version = 1;

        $attempts = $evaluationModel->fetchRow( array( "id =?" => $evaluation->id ) )->attempts;
        if( $attempts == Station_Model_Status::ACTIVE ){
            $version = $evaluationReply->fetchReplyVersion( $user->person_id , $evaluation->id )->current()->version;
            $version++;
        }

		foreach ( $_POST as $key => $data ){
			$save['Evaluation_Model_EvaluationReply']['evaluation_question_id'] = $key;
			$save['Evaluation_Model_EvaluationReply']['person_id'] = $user->person_id;
			$save['Evaluation_Model_EvaluationReply']['evaluation_id'] = $evaluation->id;
			$save['Evaluation_Model_EvaluationReply']['version'] = $version;

            if( is_string($data) ){
                $save['Evaluation_Model_EvaluationReply']['value'] = $data;
                $evaluationReply->save( $save );
            }else{
                foreach ( $data as $evaluationValue => $val ){
                    $save['Evaluation_Model_EvaluationReply']['value'] = $val;
                    if ( $evaluationValue ){
                        $save['Evaluation_Model_EvaluationReply']['evaluation_value_id'] = $evaluationValue;
                    }else{
                        $save['Evaluation_Model_EvaluationReply']['evaluation_value_id'] = null;
                    }
                    
                    $evaluationReply->save( $save );
                }
            }

            unset( $save );
		}
		
		$this->_helper->_flashMessenger->addMessage( $this->view->translate( "registered successfully" ) );
		
		$this->_redirect( "/evaluation/evaluation/index/" );
	}


    public function saveNoteAction()
    {
        $note = Zend_Filter::filterStatic( $this->_getParam( "note" ) , "HtmlEntities" );
        
        $evaluation = new Zend_Session_Namespace( "evaluation" );
        $user 	    = new Zend_Session_NameSpace( 'user' );

        $evaluationNote = new Evaluation_Model_EvaluationNote();
        $evaluationNote->delete( array( "person_id" => $user->person_id , "evaluation_id" => $evaluation->id ) );

        $data['Evaluation_Model_EvaluationNote']['note'] = $note;
        $data['Evaluation_Model_EvaluationNote']['person_id'] = $user->person_id;
        $data['Evaluation_Model_EvaluationNote']['evaluation_id'] = $evaluation->id;

        $result = $evaluationNote->save( $data );
        exit;
    }

	public function viewAction()
	{
		$evaluation = new Zend_Session_Namespace( "evaluation" );
		$user = new Zend_Session_Namespace( "user" );
        
		$evaluationQuestion = new Evaluation_Model_EvaluationQuestion();
		$evaluationReply    = new Evaluation_Model_EvaluationReply();

        $version = $evaluationReply->fetchReplyVersion( $user->person_id , $evaluation->id )->current()->version;
        if( !$version ){
            $this->_helper->_flashMessenger->addMessage( $this->view->translate( "you have not answered this assessment" ) );
            $this->_redirect( "/evaluation/evaluation/index/" );
        }
        
		$textarea = $evaluationQuestion->fetchFieldTextArea( $evaluation->id , $user->person_id , $version );

        $response = true;
        foreach( $textarea as $val ){
            if( !$val->response_note ){
                $response = false;
            }
        }

		if ( $response && $textarea->count() ){
			$this->view->rs            = $evaluationQuestion->fetchQuestion( $evaluation->id );
            $this->view->version       = $evaluationReply->fetchReplyVersion( $user->person_id , $evaluation->id )->current()->version;
			$this->view->person_id     = $user->person_id;
		}
	}

    public function studentsEvaluatedAction()
    {
       $evaluation = new Zend_Session_Namespace( "evaluation" );
       $user = new Zend_Session_Namespace( "user" );

       $evaluation->id  = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "Int" );

       $evaluationReply = new Evaluation_Model_EvaluationReply();
       $this->view->students = $evaluationReply->fetchStudentsEvaluated( $evaluation->id );
    }

    public function deleteAction()
    {
        $person_id = Zend_Filter::filterStatic( $this->_getParam( "person_id" ) , "Int" );
        $evaluation = new Zend_Session_Namespace( "evaluation" );

        $evaluationReply     = new Evaluation_Model_EvaluationReply();
        $evaluationReplyNote = new Evaluation_Model_EvaluationReplyNote();

        $replys = $evaluationReply->fetchIdReply( $evaluation->id , $person_id );
        foreach( $replys as $val ){
            $evaluationReplyNote->delete( array( "evaluation_reply_id" => $val->id ) );
            $evaluationReply->delete( array( "id" => $val->id ) );
        }
        
        $this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted successfully" ) );
		$this->_redirect( "/evaluation/reply/students-evaluated/id/$evaluation->id" );
    }
}