<?php
class Bulletin_BulletinController extends Application_Controller_Abstract
{
	protected $_model = 'Bulletin_Model_Bulletin';

	public function indexAction()
	{ 
		$user = new Zend_Session_Namespace('user');
		$person = new Share_Model_Person();
		$bulletinGroup = new Bulletin_Model_BulletinGroup();

		$this->view->persons = $person->fetchAllPersonByGroup();
		$this->view->rs      = $bulletinGroup->fetchAll( array( 'discipline_id =?' => $user->discipline_id ) , 'id' );

		$this->view->role    = $user->roles[SYSTEM_ID]['current'];
	}
		
	public function inputAction()
	{
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
		$bulletinGroup = new Bulletin_Model_BulletinGroup();
		$bulletin = new Bulletin_Model_Bulletin();
		$result = null;
		
		$this->view->modules = array(
			"" => $this->view->translate("select"),
			Bulletin_Model_Bulletin::NOTEPAD => $this->view->translate("notepad"),
		);
		
		$this->view->modules_itens = array( 
			"" => $this->view->translate("select"),
			Bulletin_Model_Bulletin::EVALUATION => $this->view->translate("evaluation"),
			Bulletin_Model_Bulletin::FORUM      => $this->view->translate("forum"),
			Bulletin_Model_Bulletin::ACTIVITY   => $this->view->translate("activity")
		);

		$this->view->data = $bulletinGroup->createRow();

		if( $id ){
			$this->view->data = $bulletinGroup->find( $id )->current();
			$result = $bulletin->fetchAll( array( "bulletin_group_id = ?" => $id ) );
		}
		
		$add = array();

        if($result){
			$add = $this->addModules($result);
		}
		
		$this->view->jsonRelation = Zend_Json::encode( $add );
		$this->view->jsonValidate = Zend_Json::encode( $bulletinGroup->validators );
	}
	
	public function listAction()
	{
		$modules = Zend_Filter::filterStatic( $this->_getParam( 'modules' ) , 'int' );
		$module  = new Zend_Session_Namespace('module');

		$activity = new Activity_Model_Activity();
		$evaluation = new Evaluation_Model_Evaluation();
		$forum = new Forum_Model_Forum();
		
		switch ( $modules ){
			case Bulletin_Model_Bulletin::ACTIVITY :
				$this->view->rs = Preceptor_Util::toSelect( $activity->fetchRelation( null , array( 'composition_type DESC' , 'finished' ) ) , array('label'=>'title'));
			break;
			case Bulletin_Model_Bulletin::EVALUATION :
				$this->view->rs = Preceptor_Util::toSelect( $evaluation->fetchRelation( null , "id DESC" ) );
			break;
			case Bulletin_Model_Bulletin::FORUM :
				$this->view->rs = Preceptor_Util::toSelect( $forum->fetchRelation( array( "forum_id IS NULL" ) , array( "status" , "created DESC" ) ) , array('label'=>'title'));
			break;
		}
		
		$this->view->module   = $modules;
		$this->view->group_id = Zend_Filter::filterStatic( $this->_getParam( 'group_id' ) , 'int' );
		$this->view->jsonRelation = Zend_Json::encode( array() );
		
		$this->_helper->layout->setLayout('clear');
	}
	
	public function saveAction()
	{
		$bulletinGroup = new Bulletin_Model_BulletinGroup();
		$bulletin = new Bulletin_Model_Bulletin();
		
		$result = $bulletinGroup->save( $_POST['data'] );

		$this->_helper->_flashMessenger->addMessage( $result->message );
		
		if( $result->error ){
            $this->_redirect( $this->_getRedirector( 'Error' ) , array( 'prependBase' => true ) );
        }else{
        	$bulletin->save( $result->detail['id'] );
            $this->_redirect( "/bulletin/bulletin/index/" );
        }
	}
	
	public function savenoteAction()
	{
		$user = new Zend_Session_Namespace( "user" );
		
		$person_id   = Zend_Filter::filterStatic( $this->_getParam( "person_id" )  , "int" );
		$bulletin_id = Zend_Filter::filterStatic( $this->_getParam( "bulletin_id" ), "int" );
		$id		     = Zend_Filter::filterStatic( $this->_getParam( 'id' ) , 'int' );

		$bulletinGroup = new Bulletin_Model_BulletinGroup();
		$bulletinNote = new Bulletin_Model_BulletinNote();
		$certificate = new Certificate_Model_Certificate();

		$data['id']          = $id;
		$data['person_id']   = $person_id;
		$data['bulletin_id'] = $bulletin_id;
		$data['note']        = str_replace( "," , "." , $_POST['note'] );
		
		$id = $bulletinNote->save( array( 'Bulletin_Model_BulletinNote' => $data ));

		$rs = $certificate->fetchRow( array('class_id = ?'=>$user->group_id) );
		if( $rs ){
			$bulletinGroup->verify( $person_id , $rs->id );
		}
		
       	echo "ok";
		exit;
	}
	
	public function deleteAction()
	{
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );

		$bulletinGroup = new Bulletin_Model_BulletinGroup();
		$bulletinNote = new Bulletin_Model_BulletinNote();
		$bulletin = new Bulletin_Model_Bulletin();
		
		$result = $bulletin->fetchAll( array( "bulletin_group_id = ?" => $id ) );
		
		foreach ( $result as $rs ){
			$bulletinNote->delete( array( 'bulletin_id'=>$rs->id ) );
		}
		
		$bulletin->delete( array('bulletin_group_id'=>$id) );
		$bulletinGroup->delete( $id );
		
		$this->_helper->_flashMessenger->addMessage( $this->view->translate( "deleted successfully" ) );	
		
		$this->_redirect( "/bulletin/bulletin/" );
		
	}
	
	public function addModules( $result )
	{
		foreach( $result as $value )
		{
			switch ( $value->module )
			{
				case Bulletin_Model_Bulletin::EVALUATION:
                    $text = $value->findParentRow( 'Evaluation_Model_Evaluation' )->name;
                break;
				case Bulletin_Model_Bulletin::FORUM:
                    $text = $value->findParentRow( 'Forum_Model_Forum' )->title;
                break;
				case Bulletin_Model_Bulletin::ACTIVITY:
                    $text = $value->findParentRow( 'Activity_Model_Activity' )->title;
                break;
				case Bulletin_Model_Bulletin::NOTEPAD:
                    $text = $this->view->translate( 'notepad' );
                break;
				case Bulletin_Model_Bulletin::GLOSSARY:
                    $text = $this->view->translate( 'glossary' );
                break;
			} 
			
			if ( $value->item ){
				$add[] = array( "type" 	=> $value->module, 
								"id" 	=> $value->item,
								"text" 	=> $text,
								"model" => "Bulletin_Model_Bulletin" );
			}else{
				$add[] = array( "type" 	=> $value->module, 
								"id" 	=> "0",
								"text" 	=> $text,
								"model" => "Bulletin_Model_Bulletin" );
			}
		}
		
		return $add;
	}
}