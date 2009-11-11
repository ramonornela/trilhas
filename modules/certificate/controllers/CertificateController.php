<?php
class Certificate_CertificateController extends Controller
{
	public function indexAction()
	{
		$user = new Zend_Session_Namespace( "user" );
		
		$this->view->rs = $this->Group->fetchAll( array( "discipline_id =?" => $user->discipline_id ) , "id DESC" );
		$this->render();
	}
	
	public function viewAction()
	{
		$id = Zend_Filter::get( $this->_getParam( "id" ) , "int" );
		
		$this->view->rs = $this->Certificate->fetchRow( array( "group_id =?" => $id ) );
		$this->view->group_id = $id;
		
		$this->render();
	}
	
	public function inputAction()
	{
		$id = Zend_Filter::get( $this->_getParam( "id" ) , "int" );
		$this->view->data->group_id = Zend_Filter::get( $this->_getParam( "group_id" ) , "int" );
		
		if ( $id ){
			$this->view->data  = $this->Certificate->fetchRow( array( 'id =?' => $id ) );
		}
		
		$this->view->jsonValidate   = Zend_Json::encode( $this->Certificate->validators );
		
		$this->render();
	}
	
	public function saveAction()
	{
		$model = $this->getModel();
		
		$input = $this->preSave();
		if( $input->isValid() )
		{
			$data = $model->setNull( $input->toArray() );
			
			if ( $model->save( $data ) ){
				$this->postSave( true , $input );
			}else{
				$this->postSave( false , $input );
			}
		}
		$this->postSave( false , $input );
	}
	
	public function printAction()
	{
		$group_id = Zend_Filter::get( $this->_getParam( 'group_id' ) , "int" );
		
		$this->view->certificate = $this->Certificate->findByPerson( $group_id );
		
		$this->render( null , 'guest' );
	}
}