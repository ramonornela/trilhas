<?php
class Evaluation_CorrectController extends Application_Controller_Abstract
{
	public function indexAction()
	{ 
		$evaluation = new Zend_Session_Namespace( "evaluation" );
		$evaluationReply = new Evaluation_Model_EvaluationReply();
		$evaluation->id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
		
		$this->view->rs = $evaluationReply->fetchPersonByReply( $evaluation->id );
	}
	
	public function viewAction()
	{ 
		$evaluation = new Zend_Session_Namespace( "evaluation" );
		$evaluationReply = new Evaluation_Model_EvaluationReply();
		$evaluationQuestion = new Evaluation_Model_EvaluationQuestion();

		$person_id     = Zend_Filter::filterStatic( $this->_getParam( "person_id" ) , "int" );
		
		if ( $person_id ){
			$evaluation->person_id = $person_id;
		}

        $reply_version = $evaluationReply->fetchReplyVersion( $person_id , $evaluation->id )->current()->version;
        $this->view->rs = $evaluationQuestion->fetchFieldTextArea( $evaluation->id , $person_id , $reply_version );
		$this->view->person_id = $evaluation->person_id;
	}
	
	public function saveTextareaNoteAction()
    {
        $user 	    = new Zend_Session_NameSpace( 'user' );
        $evaluation = new Zend_Session_Namespace( "evaluation" );

        $evaluationReplyNote = new Evaluation_Model_EvaluationReplyNote();

        $data['Evaluation_Model_EvaluationReplyNote']['note'] = str_replace( "," , ".", $_POST['note']);
        $data['Evaluation_Model_EvaluationReplyNote']['person_id'] = $user->person_id;
        $data['Evaluation_Model_EvaluationReplyNote']['evaluation_reply_id'] = $_POST['id'];

        $result = $evaluationReplyNote->save( $data );
        
        $this->_helper->_flashMessenger->addMessage( $this->view->translate( $result->message ) );
        $this->_redirect( "/evaluation/correct/index/id/$evaluation->id" );
    }
}