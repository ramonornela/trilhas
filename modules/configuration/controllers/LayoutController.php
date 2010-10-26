<?php
class Configuration_LayoutController extends Controller
{
	//public $uses = array( "Configuration", "Menu", "RoleResourceAccess", "MenuResource", "File" );
	
	public function indexAction()
	{
		$user    = new Zend_Session_Namespace("user");
		
		$role_id = Zend_Filter::filterStatic( $this->_getParam("role_id") , "int" );

		$this->view->rs 	   = $this->Menu->fetchAll( "menu_id IS NULL" , "position" );
		$this->view->role_id   = $role_id;
		
	   	$this->view->type    = $user->config;
		$this->view->modules = array(
            "content" 	 => "default/content",
            "file" 		 => "file/file" ,
            "activity" 	 => "activity/activity" ,
            "evaluation" => "evaluation/evaluation",
            "notepad" 	 => "notepad/notepad" ,
            "dictionary" => "dictionary/dictionary" ,
            "faq" 		 => "faq/faq" ,
            "forum" 	 => "forum/forum" ,
            "glossary" 	 => "glossary/glossary",
            "link" 		 => "link/link" ,
            "map" 		 => "map/map" ,
            "multimedia" => "multimedia/multimedia" ,
            "narration"  => "narration/narration" ,
            "rss" 		 => "rss/rss" ,
            "weblibrary" => "weblibrary/weblibrary"
        );
        
        $this->render();
	}
	
	public function saveAction()
	{
		$user    = new Zend_Session_Namespace("user");
		
		$data['name'] = "layout_organization";
		$data['value']  = $_POST['relation'];
		
		$option  = Zend_Filter::filterStatic($_POST['option'], "HtmlEntities" );
		
		if ( $option == "course_id" )
		{
			$data['course_id'] = $user->course_id;
			$this->Configuration->deleteLayout( "course_id" , $user->course_id );
		}

		if ( $option == "discipline_id" )
		{
			$data['discipline_id'] = $user->discipline_id;
			$this->Configuration->deleteLayout( "discipline_id" , $user->discipline_id );
		}
		
		if ( $option == "group_id" )
		{
			$data['group_id'] = $user->group_id;
			$this->Configuration->deleteLayout( "group_id" , $user->group_id );
		}
		
		$id = $this->Configuration->save( $data );	
		if ( $id ) 
		{
			unset($user->config);
			$this->Configuration->loadConfig( $user );

			$this->_helper->_flashMessenger->addMessage( $this->view->translate( "registered successfully" ) );
			$this->_redirect( "/configuration/layout/index/" );
		}
		else
		{
			$this->_helper->_flashMessenger->addMessage( $this->view->translate( "save error" ) );
			$this->_redirect( "/configuration/layout/index/" );
		}
		
	}
}