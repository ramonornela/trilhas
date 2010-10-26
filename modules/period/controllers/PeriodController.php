<?php
class Period_PeriodController extends Controller
{
	//public $uses = array( "Period" , "FormCoursePeriod" );
	
	public function indexAction()
	{
		$form_id = new Zend_Session_NameSpace( 'user' );
		
		if ( Zend_Filter::filterStatic( $this->_getParam( "formId" ) , "int" ) )
			$form_id->id = Zend_Filter::filterStatic( $this->_getParam( "formId" ) , "int" );
		
		$this->view->form_id = $form_id->id;
		$this->view->rs = $this->Period->fetchAll( null , 'entered DESC' );
		$this->view->jsonValidate = Zend_Json::encode( $this->Period->validators );
		$this->render( 'index' , 'ajax' );
	}
	
	public function inputAction()
	{
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
		
		if( $id ){
            $this->view->data = $this->Period->find( $id )->current();
        }
			
		$this->view->jsonValidate = Zend_Json::encode( $this->Period->validators );
		
		$this->render( 'input' , 'ajax' );
	}
	
	public function saveAction()
	{
		$user = new Zend_Session_NameSpace( 'user' );

        $_POST['entered'] = date( 'd/m/Y' , strtotime($_POST['entered']) );
        $_POST['expired'] = date( 'd/m/Y' , strtotime($_POST['expired']) );
        
		$input = $this->preSave();
		if( $input->isValid() )
		{
			$data = $this->setNull( $input->toArray() );
			$data['person_id'] = $user->person_id;
			
			$id = $this->Period->save( $data );
			
			if ( $id ) 
				$this->postSave( true , $input );
			
			else
				$this->postSave( false , $input );
		}
		else
			$this->postSave( false , $input );
	}
}

