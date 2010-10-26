<?php
class Form_FormgroupController extends Controller
{
	//public $uses = array( "FormGroup" , "Form" , "FormGroupField" );
	
	public function indexAction()
	{
		$this->view->formId = Zend_Filter::get( $this->_getParam( "formId" ) , "int" );
		
		$this->view->rs = $this->FormGroup->fetchAll( "id <> -1" , "name" );
		$this->view->jsonValidate = Zend_Json::encode( $this->FormGroup->validators );
		
		$this->render( "index" , "ajax" );
	}
	
	public function inputAction()
	{
		$id = Zend_Filter::get( $this->_getParam( "id" ) , "int" );
		
		if( $id )
			$this->view->data = $this->FormGroup->find( $id )->current();
		
		$this->view->jsonValidate = Zend_Json::encode( $this->FormGroup->validators );
		
		$this->render( 'input' , 'ajax' );
	}
	
	public function saveAction()
	{
		$input = $this->preSave();
		if( $input->isValid() )
		{
			$data = $this->setNull( $input->toArray() );
			if ( $this->FormGroup->save( $data ) )
			{
				$this->_helper->_flashMessenger->addMessage( $this->view->translate( "registered successfully" ) );
				$this->_redirect( "form/formgroup/index" );
			}
		}
		else
		{
			$this->_helper->_flashMessenger->addMessage( $this->view->translate( "save error" ) );
			$this->view->validate = $input->getMessages();
			$this->view->data 	  = $input->toView();
			
			$this->view->jsonValidate = Zend_Json::encode( $this->FormGroup->validators );
			
			$this->render( "formgroup/input" , 'clearbox' );
		}
	}	
}
?>
