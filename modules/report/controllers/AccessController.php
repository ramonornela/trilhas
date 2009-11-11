<?php
class Report_AccessController extends Application_Controller_Abstract
{
	protected $_model = false;
	
	public function indexAction()
	{
		$classModel = new Station_Model_ClassModel();
		$resource = new Preceptor_Share_Resource();
		
        $user = new Zend_Session_Namespace('user');
        
		$group = $classModel->findByCourse( $user->course_id , $user->group->all );
		
		$this->view->groups  = Preceptor_Util::toSelect( $group , array( 'first' => 'Todos' ) );
        $this->view->users   = Preceptor_Util::toSelect( array() , array( 'first' => 'Todos' ) );
		$this->view->modules = Preceptor_Util::toSelect( $resource->fetchUniqueController() , array( 'first' => 'Todos' ) );
		$this->view->actions = Preceptor_Util::toSelect( array() , array( 'first' => 'Todos' ) );
	}
	
	public function findAction()
	{
		$log = new Application_Model_Log();
        $user = new Zend_Session_Namespace('user');
        
        $group_id   = Zend_Filter::filterStatic( $_POST['group_id'] , "int" );
        $person_id  = Zend_Filter::filterStatic( $_POST['person_id'] , "int" );
        $controller = $_POST['controller'];
        $action     = $_POST['action'];
        $started    = $_POST['started'];
        $finish     = $_POST['finish'];
        
		$this->view->access = $log->fetchAccess( $group_id , $person_id , $controller , $action , $started , $finish );
		
		$this->_helper->layout->setLayout('clear');
	}

    public function selectAction()
    {
		$person = new Share_Model_Person();
		$resource = new Preceptor_Share_Resource();

        $this->view->rs = Preceptor_Util::toSelect( array() , array( 'first' => 'Todos' ) );
        
        if ( $this->_getParam('type') == "person" && $this->_getParam('q') ){
			$this->view->rs = Preceptor_Util::toSelect(
				$person->fetchAllPersonByGroup( $this->_getParam('q') , false ),
				array( 'first' => 'Todos' )
			);
            $this->view->selectName = "person_id";
        }

        if ( $this->_getParam('type') == "action" && $this->_getParam('q') ){
			$this->view->rs = Preceptor_Util::toSelect(
				$resource->fetchAll( array( "controller = ?" => $this->_getParam('q') ) ) ,
				array('first'=>'Todos','value'=>'action','label'=>'action')
			);
			
            $this->view->selectName = "action";
        }
        
        $this->_helper->layout->setLayout('clear');
    }

}

