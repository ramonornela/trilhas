<?php
class Form_FormfieldController extends Controller
{
	//public $uses = array( "FormField" , "FormGroup" , "Form" , "FormFieldValue" , "FormGroupField" , "FormValidate" , "FormFieldValidate" , "FormFieldData" );

	public function inputAction()
	{
		$id = Zend_Filter::get( $this->_getParam( "id" ) , "int" );
		$field_id = Zend_Filter::get( $this->_getParam('fieldId') , 'int' );
		$group = $this->_getParam( "group" );
		
		$this->view->form     = $this->FormGroup->fetchFormGroup( $id );
		$this->view->form_id  = $id;
		$this->view->group    = $this->toSelect( $this->FormGroup->fetchAll( array( 'id > -1' ) , "name" ) );
		
		$this->view->all = $this->toSelect( $this->FormValidate->fetchAll( null ) , 'id' , 'label' , null );
		$this->view->validate = array();
			
		$this->view->jsonValidate = Zend_Json::encode( $this->FormField->validators );
		$this->view->jsonGroup    = Zend_Json::encode( $this->FormGroup->validators );
		
		if( $group )
		{
			$this->render( 'updategroup' , 'ajax' );
			return false;
		}
		
		if( $field_id )
		{
			$this->edit( $field_id );
			return false;
		}
		
		$this->render( "input" , "clearbox" );
	}
	
	public function edit( $field_id )
	{
		$this->view->data = $this->FormField->fetchRow( array( "id = ?" => $field_id ) );
		$this->render( 'edit' , 'ajax' );
	}
	
	public function saveAction()
	{
		$input = $this->preSave();
		
		if( $input->isValid() )
		{
			$data = $this->setNull( $input->toArray() );
			$id = $this->FormField->save( $data );
			if ( $id )
			{
				$this->FormFieldValue->save( $id );
				$this->FormGroupField->save( $id );
				$this->FormFieldValidate->save( $id );
				$this->_helper->_flashMessenger->addMessage( $this->view->translate( "registered successfully" ) );
			}
		}
		else
		{
			$this->_helper->_flashMessenger->addMessage( $this->view->translate( "save error" ) );
			$this->view->validate = $input->getMessages();
			$this->view->data 	  = $input->toView();
			
			$this->view->jsonValidate = Zend_Json::encode( $this->FormField->validators );
		}
		
		$this->_redirect( "/form/formfield/input/id/" . $_POST['form_id'] );
	}
	
	public function deleteAction()
	{
		$id = Zend_Filter::get( $this->_getParam('id') , 'int' );
		$form_id = Zend_Filter::get( $this->_getParam('formId') , 'int' );
		
		$this->FormFieldValue->delete( $id , 'form_field_id' );
		$this->FormGroupField->delete( $id , 'form_field_id' );
		$this->FormFieldValidate->delete( $id , 'form_field_id' );
		$this->FormFieldData->delete( $id , 'form_field_id' );
		$this->FormField->delete( $id );
		
		$this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted successfully" ) );
		$this->_redirect( "/form/formfield/input/id/" . $form_id . "/deleted/1" );
	}
					
	public function keeppositionAction()
	{
		$getValues = split( "&" , $_SERVER['REDIRECT_QUERY_STRING'] );
		foreach( $getValues as $key => $value )
		{
			$numeric = Filter::get( substr( $value , strpos( $value , "=" ) + 1 ) , 'int' );
			
			if( ($key+1) % 2 == 1 )
				$save['id'] = $numeric;
			else
			{
				$save['position'] = $numeric;
				$id = $this->FormField->save( $save );
				
			}
		}
		echo "ok";
		exit();
	}
		
}
?>
