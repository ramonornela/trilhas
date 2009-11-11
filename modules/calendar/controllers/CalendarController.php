<?php            
class Calendar_CalendarController extends Application_Controller_Abstract
{
    protected $_model = "Calendar_Model_Calendar";

	public function indexAction()
	{
		$user = new Zend_Session_Namespace("user");
		$calendar = new $this->_model();
		
		$this->view->user    		= $user;
		$this->view->date    		= date( "Y,m,d" );
		$this->view->calendarDate   = $calendar->fetchAllDate();
        
		$this->view->renderCalendar  = true;
        
		$where = array( '( finished >= CURRENT_DATE 
						AND relation IS NULL
						AND person_id = ? )
						OR ( finished >= CURRENT_DATE' => $user->person_id );

		$this->view->rs = $calendar->fetchRelation( $where );
        
		$this->_helper->layout->setLayout('clear');
	}
	
	public function findAction()
	{
		$user       = new Zend_Session_Namespace("user");
        $calendar   = new $this->_model();
        
		$date = $this->view->date = $this->_getParam( "date" );
		
		$where = array( 
			'( ( started = ? OR finished = ? ) 
			AND relation IS NULL 
			AND person_id = '. $user->person_id .' ) 
			OR ( ( started = ? OR finished = ? ) ' => $date
		);
		
		$this->view->user = $user;
		$this->view->rs   = $calendar->fetchRelation( $where );
		
        $this->_helper->layout->setLayout('clear');

        $this->render("index");
	}
	
	public function inputAction()
	{
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );

        $calendar   = new $this->_model();

		$this->view->data->started = date('Y-m-d');
		$this->view->data->finished = date('Y-m-d');
		
		$this->view->suffix = "calendar";
		
		if( $id )
			$this->view->data = $calendar->find( $id )->current();
			
		$this->view->jsonValidate = Zend_Json::encode( $calendar->validators );
		
		$this->_helper->layout->setLayout('clearbox');
	}
}