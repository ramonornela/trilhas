<?php
class Evaluation_EvaluationController extends Application_Controller_Abstract
{
	protected $_model = "Evaluation_Model_Evaluation";
	
	public function indexAction()
	{
		$evaluation = new Evaluation_Model_Evaluation();
        $user  = new Zend_Session_Namespace( "user" );
		
		if ( Share_Model_Role::STUDENT == $user->roles[SYSTEM_ID]['current'] ){
			$where = array( "started  <= ?" => date('Y-m-d'), "finished >= ?" => date('Y-m-d') );
		}else{
			$where = null;
        }
        
		$this->view->rs = $evaluation->fetchRelation( $where , "id DESC" );
		$this->view->role = $user->role_id;
	}

    public function inputAction()
    {
        $evaluation = new Zend_Session_Namespace( "evaluation" );
		$id         = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "Int" );
        
        if ( $id ){
			$evaluation->id  = $id;
            $evaluationQuestion = new Evaluation_Model_EvaluationQuestion();
            $this->view->rs = $evaluationQuestion->fetchQuestion( $id );
		}

        $evaluationModel = new Evaluation_Model_Evaluation();
        $this->view->data = $evaluationModel->createRow();

        parent::inputAction();
    }

    public function saveAction()
    {
        $this->_redirector = "/evaluation/evaluation/index/";
        
        parent::saveAction();
    }
}