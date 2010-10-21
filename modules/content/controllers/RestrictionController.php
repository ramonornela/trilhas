<?php
class Content_RestrictionController extends Tri_Controller_Action
{
	protected $_model = "Content_Model_Restriction";
	
	public function indexAction()
	{
		$type = $this->_getParam( "type" );
		
		$restrictionTime	 = new Content_Model_RestrictionTime();
		$restrictionBulletin = new Content_Model_RestrictionBulletin();

		if ( $type == Content_Model_Restriction::TIME ){
			$this->view->rs   = $restrictionTime->fetchTime();
			$this->view->type = Content_Model_Restriction::TIME;
			
			$this->render( 'indextime' );
			return false;
		}else{
			$this->view->rs = $restrictionBulletin->fetchBulletinGroup();
		}
	}

    public function inputAction()
    {
		$id	  = Zend_Filter::filterStatic( $this->_getParam( 'id' ) , 'int' );
		$type = $this->_getParam( "type" );
    	$user = new Zend_Session_Namespace("user");
		
    	if ( $type == Content_Model_Restriction::TIME ){
			$this->inputTime( $user , $id );
			return false;
    	}
		
    	if ( $type == Content_Model_Restriction::EVALUATION ){
			$this->inputBulletin( $user , $id );
			return false;
		}
    }

	public function inputTime( $user , $id = null )
	{
		$user = new Zend_Session_Namespace("user");
		
		$restriction = new Content_Model_Restriction();
		$classModel  = new Station_Model_ClassModel();

		$this->view->contents = $this->toSelectContent( $user->contents );
		$this->view->groups   = Preceptor_Util::toSelect( $classModel->fetchAll( array( "id IN( " . $user->group->all . ")" , 'discipline_id =?' => $user->discipline_id ) ) ,  array( "first" => $this->view->translate('all') ) );

		if ( $id ){
			$this->view->data = $restriction->fetchByTime( $id );
		}

		$this->render( 'inputtime' );

	}

	public function inputBulletin( $user , $id = null )
	{
		$user = new Zend_Session_Namespace("user");

		$restriction   = new Content_Model_Restriction();
		$classModel    = new Station_Model_ClassModel();
		$bulletinGroup = new Bulletin_Model_BulletinGroup();

		$this->view->contents    = $this->toSelectContent( $user->contents );
		$this->view->groups      = Preceptor_Util::toSelect( $classModel->fetchAll( array( "id IN( " . $user->group->all . ")" , 'discipline_id =?' => $user->discipline_id ) ) , array( "first" => $this->view->translate('all') ) );
		$this->view->evaluations = Preceptor_Util::toSelect( $bulletinGroup->fetchAll( array( 'discipline_id =?' => $user->discipline_id ) , 'id' ) , array( 'label' => 'title') );

		if ( $id ){
			$this->view->data = $restriction->fetchByBulletin( $id );
		}

		$this->render( 'inputbulletin' );
	}

    public function saveAction()
	{
		$type = $this->_getParam( "type" );
		
		$restriction		 = new Content_Model_Restriction();
		$restrictionTime	 = new Content_Model_RestrictionTime();
		$restrictionBulletin = new Content_Model_RestrictionBulletin();
		$result = $restriction->save( $_POST['data'] );
		
        if( $result->error ){
			$this->_helper->_flashMessenger->addMessage( $result->message );
            $this->_redirect( $this->_getRedirector( 'Error' ) , array( 'prependBase' => true ) );
        }else{
			if ( $type == Content_Model_Restriction::EVALUATION ){
				$_POST['data']['Content_Model_RestrictionBulletin']['id']             = $_POST['data']['Content_Model_Restriction']['bulletin_id'];
				$_POST['data']['Content_Model_RestrictionBulletin']['restriction_id'] = $result->detail['id'];
				
				$result = $restrictionBulletin->save( $_POST['data'] );
			}else{
				$_POST['data']['Content_Model_RestrictionTime']['id']             = $_POST['data']['Content_Model_Restriction']['bulletin_id'];
				$_POST['data']['Content_Model_RestrictionTime']['restriction_id'] = $result->detail['id'];
				
				$result = $restrictionTime->save( $_POST['data'] );
			}

			$this->_helper->_flashMessenger->addMessage( $result->message );
			$this->_redirect( "/content/restriction/index/type/$type" );
        }
	}
	
	public function deleteAction()
	{
		$id = Zend_Filter::filterStatic( $this->_getParam('id') , 'int' );
		$type = $this->_getParam( "type" );

		$restriction		 = new Content_Model_Restriction();
		$restrictionTime	 = new Content_Model_RestrictionTime();
		$restrictionBulletin = new Content_Model_RestrictionBulletin();
		
		if ( $type == Content_Model_Restriction::TIME ){
			$result = $restrictionTime->delete($id);
		}else{
			$result = $restrictionBulletin->delete(array('restriction_id'=>$id));
		}
		
		$result = $restriction->delete( $id );

		
		$this->_helper->_flashMessenger->addMessage( $result->message );
		
		$this->_redirect( "/content/restriction/" );
	}
	
	private function toSelectContent( $data )
	{
		$data = Zend_Json::decode($data);
		
		foreach( $data as $key => $val ){
			$select[$val['id']] = str_repeat('- ', $val['level']) . $val['title'];
		}

		return $select;
	}
}