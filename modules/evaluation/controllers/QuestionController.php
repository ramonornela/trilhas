<?php
class Evaluation_QuestionController extends Controller {
    protected $_model = "EvaluationQuestion";

    public function indexAction() {
        $evaluation = new Zend_Session_Namespace( "evaluation" );

        $id	= Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );

        if ( $id ) {
            $evaluation->id  = $id;
        }

        $this->view->rs = $this->EvaluationQuestion->fetchQuestion( $evaluation->id );
        $this->view->evaluation = $evaluation;

        $this->render();
    }

    public function inputAction() {
        $id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );

        $evaluation = new Zend_Session_Namespace( "evaluation" );

        $this->view->evaluation_id = $evaluation->id;
        $this->view->data = $this->EvaluationQuestion->createRow();

        $this->view->types = array( ''       => "[" . $this->view->translate("select") . "]",
                'text'        => $this->view->translate("default text"),
                'textarea'    => $this->view->translate("discursive"),
                'radio'       => $this->view->translate("multiple choice(uniq)"),
                'checkbox'    => $this->view->translate("multiple choice(more)"),
                'truefalse'   => $this->view->translate("true or false"),
                'association' => $this->view->translate("association"),
                'keyword'     => $this->view->translate("key word"),
        );
        if( $id ) {

            $result = $this->EvaluationQuestion->fetchQuestion( $evaluation->id , null , $id );

            //$evaluationReply = new Evaluation_Model_EvaluationReply();

            if( $result->count() ) {
                $this->view->messages[] = $this->view->translate( "this question belongs to other assessments" );
            }
            //else if( $this->EvaluationReply->fetchAll( array( "evaluation_question_id =?" => $id ) )->count() ){
            //  $this->_helper->_flashMessenger->addMessage( $this->view->translate( "can not edit, student responded" ) );
            //$this->_redirect( "/evaluation/question/bank-question" );
            //}

            $this->view->data = $this->EvaluationQuestion->fetchRow( array( "id =?" => $id ) );
        }

        $this->render();
    }

    public function typesAction() {
        $id = Zend_Filter::filterStatic( $this->_getParam('id') , 'Int' );
        $type = Zend_Filter::filterStatic( $this->_getParam('type') , 'HtmlEntities' );

        $evaluation = new Zend_Session_Namespace( "evaluation" );
        $this->view->evaluation_id = $evaluation->id;

        if( $id ) {
            $this->view->rs = $this->EvaluationValue->fetchAlternativesQuestion( $id );
        }

        switch( $type ) {
            case "text":
                $this->render( "text" , "ajax" );
                break;
            case "radio":
                $this->render( "radio" , "ajax"  );
                break;
            case "checkbox":
                $this->render( "checkbox" , "ajax"  );
                break;
            case "truefalse":
                $this->render( "truefalse" , "ajax"  );
                break;
            case "keyword":
                $this->render( "keyword" , "ajax"  );
                break;
            case "textarea":
                $this->render( "textarea" , "ajax"  );
                break;
            case "association":
                $this->view->letters = array( 'A' => 'A' , 'B' => 'B' , 'C' => 'C' , 'D' => 'D' , 'E' => 'E' , 'F' => 'F' , 'G' => 'G' , 'H' => 'H' , 'I' => 'I' , 'J' => 'J' , 'K' => 'K' , 'L' => 'L' , 'M' => 'M' , 'N' => 'N' , 'O' => 'O' , 'P' => 'P' , 'Q' => 'Q' , 'R' => 'R' , 'S' => 'S' , 'T' => 'T' , 'U' => 'U' , 'V' => 'V' , 'W' => 'W' , 'X' => 'X' , 'Y' => 'Y' , 'Z' => 'Z' );
                $this->render( "association" , "ajax"  );
                break;
        }

    }

    public function bankQuestionAction() {
        $to = Zend_Filter::filterStatic( $this->_getParam('to') , 'int' );

        $query = new Zend_Session_Namespace('query-evaluation');
        if( $_POST['fixed'] ) {
            $query->q    = $_POST['q'];
            $query->type = $_POST['type'];
        }else if( isset( $_POST['q'] ) && $_POST['q'] == "" ) {
            $query->q    = '';
            $query->type = '';
        }

        $queryTemp = new Zend_Session_Namespace('query-evaluation-temp');

        $this->view->types = array( ''       => "Todos",
                'text'        => $this->view->translate("default text"),
                'textarea'    => $this->view->translate("discursive"),
                'radio'       => $this->view->translate("multiple choice(uniq)"),
                'checkbox'    => $this->view->translate("multiple choice(more)"),
                'truefalse'   => $this->view->translate("true or false"),
                'association' => $this->view->translate("association"),
                'keyword'     => $this->view->translate("key word"),
        );

        $limit = 20;
        if( $to ) {
            $this->view->rs     = $this->EvaluationQuestion->search( $queryTemp->q , $queryTemp->type , $to );
            $this->view->counts = $this->paginateSelect( ceil( count( $this->EvaluationQuestion->search( $queryTemp->q , $queryTemp->type , null , false ) ) / $limit ) );

            $this->view->q      = $queryTemp->q;
            $this->view->type   = $queryTemp->type;
        }else {
            if( $_POST['q'] || $_POST['type'] ) {
                $this->view->rs     = $this->EvaluationQuestion->search( $_POST['q'] , $_POST['type'] , $to );
                $this->view->counts = $this->paginateSelect( ceil( count( $this->EvaluationQuestion->search( $_POST['q'] , $_POST['type'], null , false ) ) / $limit ) );
                if( count( $this->view->counts ) > 1 ) {
                    $queryTemp->q    = $_POST['q'];
                    $queryTemp->type = $_POST['type'];
                }
            }else if( $query->q ) {
                $this->view->rs     = $this->EvaluationQuestion->search( $query->q , $query->type , $to );
                $this->view->counts = $this->paginateSelect( ceil( count( $this->EvaluationQuestion->search( $query->q , $query->type , null , false ) ) / $limit ) );
            }else {
                $user = new Zend_Session_Namespace( "user" );
                $this->view->rs = $this->EvaluationQuestion->fetchAll( null , "id DESC" , $limit , ( $to * $limit ) );
                $this->view->counts = $this->paginateSelect( ceil( count( $this->EvaluationQuestion->fetchAll( null , "id DESC" ) ) / $limit ) );
            }

            $this->view->q      = isset( $_POST['q'] ) && $_POST['q']? $_POST['q']:$query->q;
            $this->view->type   = isset( $_POST['type'] ) && $_POST['type']? $_POST['type']:$query->type;
        }


        $this->view->to     = $to;
        $this->view->question = Zend_Filter::filterStatic( $this->_getParam('question') , 'HtmlEntities' );

        $tpl = Zend_Filter::filterStatic( $this->_getParam('tpl') , 'HtmlEntities' );

        if( isset( $tpl ) && $tpl == 'ajax' ) {
            $this->view->tpl = $tpl;
            $this->render( null , $tpl );
        }else {
            $this->render();
        }

    }

    public function selectedQuestionAction() {
        $id = Zend_Filter::filterStatic( $this->_getParam('id') , 'Int' );

        $evaluation = new Zend_Session_Namespace( "evaluation" );

        if( isset( $id ) && $id ) {
            $this->view->rs = $this->EvaluationQuestion->fetchRow( array( "id =?" => $id ) );
        }

        $this->render( null , "ajax" );
    }

    public function saveRelationAction() {
        $evaluation = new Zend_Session_Namespace( "evaluation" );

        $_POST['note'] = (float)str_replace( "," , "." , $_POST['note'] );
        $_POST['evaluation_id'] = $evaluation->id;

        if( $_POST['note'] >= 0 ) {
            $_POST['note'] = (string)str_replace( "." , "," , $_POST['note'] );

            $data = array();
            $data = $_POST;

            $this->EvaluationQuestionRel->save( $data );
            $this->_helper->_flashMessenger->addMessage( $this->view->translate( "registered successfully" ) );
            $this->_redirect( "/evaluation/question/bank-question" );
        }else {
            $this->_helper->_flashMessenger->addMessage( $this->view->translate( "save error" ) );
            $this->_redirect( "/evaluation/question/bank-question" );
        }

    }

    public function deleteRelationAction() {
        $evaluationQuestion_id = Zend_Filter::filterStatic( $this->_getParam('id') , 'Int' );

        $evaluation = new Zend_Session_Namespace( "evaluation" );

        $this->EvaluationQuestionRel->deleteRel( $evaluation->id , $evaluationQuestion_id );

        $this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted successfully" ) );
        $this->_redirect( "/evaluation/evaluation/input/id/" . $evaluation->id );
    }

    public function saveAction() {
        $evaluation = new Zend_Session_Namespace( "evaluation" );

        if( empty( $_POST['labels'] ) ) {
            $this->_helper->_flashMessenger->addMessage( $this->view->translate( "tell the question comand" ) );
            $this->_redirect( "/evaluation/evaluation/input/id/" . $evaluation->id );
        }

        $input = $this->preSave();

        if( $input->isValid() ) {
            $data = $this->setNull( $input->toArray() );
            try {
                $id = $this->EvaluationQuestion->save( $data );

                if ( $id ) {
                    $this->EvaluationValue->save( $id );
                    $this->_helper->_flashMessenger->addMessage( $this->view->translate( "registered successfully" ) );
                }
            }catch( Exception $e ) {
                $this->_helper->_flashMessenger->addMessage( $this->view->translate( "save error" ) );
            }
        }
        else {
            $this->_helper->_flashMessenger->addMessage( $this->view->translate( "save error" ) );
            $this->view->validate = $input->getMessages();
            $this->view->data 	  = $input->toView();

            //$this->view->jsonValidate = Zend_Json::encode( $this->EvaluationQuestion );
        }

        if( $evaluation->id ) {
            $this->_redirect( "/evaluation/question/bank-question/question/$id" );
        }else {
            $this->_redirect( "/evaluation/evaluation/index" );
        }

    }


    public function deleteAction() {
        $id = Zend_Filter::filterStatic( $this->_getParam('id') , 'Int' );

        $evaluation = new Zend_Session_Namespace( "evaluation" );

        $evaluationQuestion = new EvaluationQuestion();
        $evaluationReply    = new EvaluationReply();
        $evaluationValue    = new EvaluationValue();

        $result = $evaluationQuestion->fetchQuestion( $evaluation->id , null , $id );
        if( !count( $result ) ) {
            $questions = $evaluationValue->fetchAll( array( "evaluation_question_id =?" => $id ) );
            if( count( $questions ) ) {
                foreach( $questions as $q ) {
                    $replys = $evaluationReply->fetchAll( array( "evaluation_value_id =?" => $q->id ) );
                    if( count( $questions ) ) {
                        foreach( $replys as $r ) {
                            $evaluationReply->delete( array( "id" => $r->id ) );
                        }
                    }
                    $evaluationValue->delete( array( "id" => $q->id ) );
                }
            }

            try {
                $evaluationQuestion->delete( array( "id" => $id ) );
                $this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted successfully" ) );
            }catch( Exception $e ) {
                $this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted error" ) );
            }
        }else {
            $this->_helper->_flashMessenger->addMessage( $this->view->translate( "the record can not be excluded because it belongs to other evaluation" ) );
        }

        $this->_redirect( "/evaluation/question/bank-question" );

    }

    public function alterNoteAction() {
        $evaluation = new Zend_Session_Namespace( "evaluation" );
        $evaluationQuestionRel = new EvaluationQuestionRel();

        $data = $_POST;
        $data['evaluation_id'] = $evaluation->id;

        $data['position'] = $evaluationQuestionRel->fetchRow( array( 'evaluation_question_id = ?' => $data['evaluation_question_id'] ,
                'evaluation_id = ?' => $data['evaluation_id'])
                )->position;

        $evaluationQuestionRel->save( $data );
        $this->_redirect( "evaluation/evaluation/input/id/$evaluation->id" );

    }
}