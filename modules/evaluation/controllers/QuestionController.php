<?php
class Evaluation_QuestionController extends Application_Controller_Abstract
{
	protected $_model= "Evaluation_Model_EvaluationQuestion";
	
	public function indexAction()
	{ 
		$evaluation = new Zend_Session_Namespace( "evaluation" );
		$id			 = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );

		$evaluationQuestion = new Evaluation_Model_EvaluationQuestion();
		
		if ( $id ){
			$evaluation->id  = $id;
		}
		
		$this->view->rs = $evaluationQuestion->fetchQuestion( $evaluation->id );
		$this->view->evaluation = $evaluation;
	}

    public function inputAction()
    {
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );

        $evaluation = new Zend_Session_Namespace( "evaluation" );
        
		$evaluationQuestion = new Evaluation_Model_EvaluationQuestion();

		$this->view->evaluation_id = $evaluation->id;
		$this->view->data = $evaluationQuestion->createRow();

        $this->view->types = array( ''       => "[" . $this->view->translate("select") . "]",
                                    'text'        => $this->view->translate("default text"),
                                    'textarea'    => $this->view->translate("discursive"),
                                    'radio'       => $this->view->translate("multiple choice(uniq)"),
                                    'checkbox'    => $this->view->translate("multiple choice(more)"),
                                    'truefalse'   => $this->view->translate("true or false"),
                                    'association' => $this->view->translate("association"),
                                    'keyword'     => $this->view->translate("key word"),
                                );
      if( $id ){

        $result = $evaluationQuestion->fetchQuestion( $evaluation->id , null , $id );

        $evaluationReply = new Evaluation_Model_EvaluationReply();

        if( $result->count() ){
            $this->view->messages[] = $this->view->translate( "this question belongs to other assessments" );
        }else if( $evaluationReply->fetchAll( array( "evaluation_question_id =?" => $id ) )->count() ){
            $this->_helper->_flashMessenger->addMessage( $this->view->translate( "can not edit, student responded" ) );
            $this->_redirect( "/evaluation/question/bank-question" );
        }

        $this->view->data = $evaluationQuestion->fetchRow( array( "id =?" => $id ) );
      }

      
    }

    public function typesAction()
    {
        $id = Zend_Filter::filterStatic( $this->_getParam('id') , 'Int' );
        $type = Zend_Filter::filterStatic( $this->_getParam('type') , 'HtmlEntities' );
        
        $evaluation = new Zend_Session_Namespace( "evaluation" );
        $this->view->evaluation_id = $evaluation->id;

        if( $id ){
            $evaluationValue = new Evaluation_Model_EvaluationValue();
            $this->view->rs = $evaluationValue->fetchAlternativesQuestion( $id );
        }

        switch( $type ){
            case "text":
                $this->render( "text" );
            break;
            case "radio":
                $this->render( "radio" );
            break;
            case "checkbox":
                $this->render( "checkbox" );
            break;
            case "truefalse":
                $this->render( "truefalse" );
            break;
            case "keyword":
                $this->render( "keyword" );
            break;
            case "textarea":
                $this->render( "textarea" );
            break;
            case "association":
                $this->view->letters = array( 'A' => 'A' , 'B' => 'B' , 'C' => 'C' , 'D' => 'D' , 'E' => 'E' , 'F' => 'F' , 'G' => 'G' , 'H' => 'H' , 'I' => 'I' , 'J' => 'J' , 'K' => 'K' , 'L' => 'L' , 'M' => 'M' , 'N' => 'N' , 'O' => 'O' , 'P' => 'P' , 'Q' => 'Q' , 'R' => 'R' , 'S' => 'S' , 'T' => 'T' , 'U' => 'U' , 'V' => 'V' , 'W' => 'W' , 'X' => 'X' , 'Y' => 'Y' , 'Z' => 'Z' );
                $this->render( "association" );
            break;
        }

        $this->_helper->layout->setLayout( "clear" );
    }

    public function bankQuestionAction()
    {
        $q    = $this->_getParam('q-question');
        $type = Zend_Filter::filterStatic( $this->_getParam('type') , 'HtmlEntities' );
        
        $evaluationQuestion = new Evaluation_Model_EvaluationQuestion();
        
        $this->view->types = array( ''       => "Todos",
                                    'text'        => $this->view->translate("default text"),
                                    'textarea'    => $this->view->translate("discursive"),
                                    'radio'       => $this->view->translate("multiple choice(uniq)"),
                                    'checkbox'    => $this->view->translate("multiple choice(more)"),
                                    'truefalse'   => $this->view->translate("true or false"),
                                    'association' => $this->view->translate("association"),
                                    'keyword'     => $this->view->translate("key word"),
                                );

        if( $q || $type ){
            $this->view->rs = $evaluationQuestion->search( $q , $type );
        }else{
            $this->view->rs = $evaluationQuestion->fetchAll( null , "id DESC" );
        }
    }
    
    public function selectedQuestionAction()
    {
        $id = Zend_Filter::filterStatic( $this->_getParam('id') , 'Int' );

        $evaluationQuestion = new Evaluation_Model_EvaluationQuestion();
        if( isset( $id ) && $id ){
            $this->view->rs = $evaluationQuestion->fetchRow( array( "id =?" => $id ) );
        }
        
        $this->_helper->layout->setLayout( "clear" );
    }
    
    public function saveRelationAction()
    {
        $evaluation = new Zend_Session_Namespace( "evaluation" );

        $evaluationQuestionRel = new Evaluation_Model_EvaluationQuestionRel();
        
        $_POST['note'] = str_replace( "," , "." , $_POST['note'] );
        $_POST['evaluation_id'] = $evaluation->id;

        $data = array();
        $data['Evaluation_Model_EvaluationQuestionRel'] = $_POST;
        $evaluationQuestionRel->save( $data );

        $this->_helper->_flashMessenger->addMessage( $this->view->translate( "registered sucessfuly" ) );
        $this->_redirect( "/evaluation/question/bank-question" );
    }

    public function deleteRelationAction()
    {
        $evaluationQuestion_id = Zend_Filter::filterStatic( $this->_getParam('id') , 'Int' );

        $evaluation = new Zend_Session_Namespace( "evaluation" );
        
        $evaluationQuestionRel = new Evaluation_Model_EvaluationQuestionRel();
        $evaluationQuestionRel->delete( array( "evaluation_question_id" => $evaluationQuestion_id , "evaluation_id" => $evaluation->id ) );
        
        $this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted successfully" ) );
        $this->_redirect( "/evaluation/evaluation/input/id/" . $evaluation->id );
    }

	public function saveAction()
	{
        $evaluation = new Zend_Session_Namespace( "evaluation" );
        
        if( empty( $_POST['label'] ) ){
            $this->_helper->_flashMessenger->addMessage( $this->view->translate( "tell the question comand" ) );
    		$this->_redirect( "/evaluation/evaluation/input/id/" . $evaluation->id );
        }
        
        if( isset( $_POST['id'] ) && $_POST['id'] ){
            $_POST['data']['Evaluation_Model_EvaluationQuestion']['id'] = $_POST['id'];
        }
        
        $_POST['data']['Evaluation_Model_EvaluationQuestion']['type']  = $_POST['type'];
        $_POST['data']['Evaluation_Model_EvaluationQuestion']['label'] = $_POST['label'];


        $this->_redirector = "/evaluation/evaluation/input/id/" . $evaluation->id;
		parent::saveAction();
	}
	
	
	public function deleteAction()
	{
        $id = Zend_Filter::filterStatic( $this->_getParam('id') , 'Int' );

        $evaluation = new Zend_Session_Namespace( "evaluation" );

        $evaluationQuestion = new Evaluation_Model_EvaluationQuestion();
        $evaluationValue    = new Evaluation_Model_EvaluationValue();

        $result = $evaluationQuestion->fetchQuestion( $evaluation->id , null , $id );

        if( !$result->count() ){
            $evaluationValue->delete( array( "evaluation_question_id" => $id ) );
            $evaluationQuestion->delete( array( "id" => $id ) );
            $this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted successfully" ) );
        }else{
            $this->_helper->_flashMessenger->addMessage( $this->view->translate( "the record can not be excluded because it belongs to other evaluation" ) );
        }

        $this->_redirect( "/evaluation/question/bank-question" );

	}
}