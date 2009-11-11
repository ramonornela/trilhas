<?php
class CourseController extends Application_Controller_Abstract
{
    protected $_model = "Station_Model_Course";

    public function indexAction()
    {
        $list = Zend_Filter::filterStatic( $this->_getParam( "list" ) , "HtmlEntities" );

        $auth = Zend_Auth::getInstance();
        $list = empty( $list ) ? false : true ;

        if( $auth->hasIdentity() && !$list ){
            $this->_redirect( "course/my-course" );
        }
        
        $course         = new $this->_model();
        $discipline     = new Station_Model_Discipline();
        
        $disciplineResult  = $discipline->forIn();
        $courseResult      = $course->forIn( array( 'status = ?' => Station_Model_Course::ACTIVE ) );

        if( $courseResult ){
            $where = $course->select()->where( 'id IN ( ' . $courseResult . ' )' )->order( 'name' );
    		$this->view->rs = $course->fetchAll( $where );
        }else{
            $this->view->rs = null;
        }
        
		$this->view->course_id      = $courseResult;
        $this->view->discipline_id  = $disciplineResult;
        
        $this->_helper->layout->setLayout('clearbox');
    }

    public function myCourseAction()
	{
        $user 		   = new Zend_Session_Namespace("user");
		$content 	   = new Content_Model_Content();
		$course 	   = new Station_Model_Course();
		$contentAccess = new Content_Model_ContentAccess();
        
        if ( !$user->course->all ){
            $user->course->all = 0;
        }

        if( !$user->discipline->all ){
            $user->discipline->all = 0;
        }

        if( !$user->group->all ){
            $user->group->all = 0;
        }
		
        $this->view->group_id	   = $user->group->all;
        $this->view->discipline_id = $user->discipline->all;
        $this->view->course_id	   = $user->course->all;
        $this->view->person_id	   = $user->person_id;
        $this->view->role_id	   = $user->role_id;
		$this->view->user		   = $user;
		
        $this->view->ContentAccess = $contentAccess;
        $this->view->Content 	   = $content;
        
        $where = $course->select()->where( 'id IN ( ' . $user->course->all . ' )' )->order( 'name' );

        $this->view->rs = $course->fetchAll( $where );
            
        $this->_helper->layout->setLayout('clearbox');
	}
}
