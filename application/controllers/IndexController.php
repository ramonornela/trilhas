<?php
class IndexController extends Application_Controller_Abstract
{
    public $_model = false;

    public function indexAction()
    {
        $auth = Zend_Auth::getInstance();
        if( !$auth->hasIdentity() ) {
            $this->_redirect("user/login");
        }

        $user = new Zend_Session_Namespace("user");

        $course      = new Station_Model_Course();
        $discipline  = new Station_Model_Discipline();
        $classModel  = new Station_Model_ClassModel();
        $classPerson = new Station_Model_ClassPerson();
        $userModel   = new Preceptor_Share_User();

        $user->group      = new Zend_Session_Namespace('group');
        $user->discipline = new Zend_Session_Namespace('discipline');
        $user->course     = new Zend_Session_Namespace('course');

        $user->course_id 	 = null;
        $user->discipline_id = null;
        $user->group_id 	 = null;

        $user->group->all 	   = 0;
        $user->discipline->all = 0;
        $user->course->all 	   = 0;

        $user->folderHierarchyCreate = false;

        $role = $user->roles[SYSTEM_ID]['current'];

        if( $role == Share_Model_Role::INSTITUTION || !$auth->hasIdentity() ) {
            $groupWhere = $classModel->select()->where( 'using_elearning = ?' , Station_Model_ClassModel::YES );

            $user->group->all       = $classModel->forIn( $groupWhere );
            $user->discipline->all  = $discipline->forIn( array( 'status = ?' => Station_Model_Discipline::ACTIVE ) );
            $user->course->all      = $course->forIn( array( 'status = ?' => Station_Model_Course::ACTIVE ) );

            if( $role == Share_Model_Role::INSTITUTION ) {
                $shareResource = new Share_Model_Resource();
                $user->resources[SYSTEM_ID] = $shareResource->fetchAll()->toArray();
            }
        }else if( $role == Share_Model_Role::SPECIALIST ) {

                $groupWhere = $classModel->select()->setIntegrityCheck(false)
                    ->from( array( "c" => "class" ) , array( "c.*" ) , "station" )
                    ->join( array( "dd" => "discipline_disponibility" ) , "dd.discipline_id = c.discipline_id" , array() , "station" )
                    ->where( "dd.person_id =?" , $user->person_id );

                $user->group->all = $classPerson->forIn( $groupWhere );

                if ( $user->group->all ) {
                    $user->discipline->all = $classModel->forIn( array(
                        'id IN ( ' . $user->group->all . ' )') ,  "discipline_id" );
                }

                if ( $user->discipline->all ) {
                    $user->course->all = $discipline->fetchIn(
                        'd.id IN ( ' . $user->discipline->all . ' )' ,
                        "d.status = '".Station_Model_Discipline::ACTIVE."'"
                    );
                }

            }else if( $role == Share_Model_Role::TEACHER ) {
                    $user->discipline->all = $classModel->forIn( array(
                        'discipline_disponibility_id = '. $user->person_id ) ,  "discipline_id" );

                    if ( $user->discipline->all ) {
                        $user->course->all = $discipline->fetchIn(
                            'd.id IN ( ' . $user->discipline->all . ' )' ,
                            "d.status = '".Station_Model_Discipline::ACTIVE."'"
                        );
                    }
                }else {
                    $user->group->all = $classPerson->forInClass( $user->person_id );

                    if ( $user->group->all ) {
                        $user->discipline->all = $classModel->forIn( array(
                            'id IN ( ' . $user->group->all . ' )') ,  "discipline_id" );
                    }

                    if ( $user->discipline->all ) {
                        $user->course->all = $discipline->fetchIn(
                            'd.id IN ( ' . $user->discipline->all . ' )' ,
                            "d.status = '".Station_Model_Discipline::ACTIVE."'"
                        );
                    }
                }

        if( !isset($_COOKIE['theme']) ) {
            setcookie( 'theme' , DEFAULT_THEME , time()+60*60*24*365 ); //30 dias
        }

        $this->view->theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : DEFAULT_THEME;

        if( isset($user->id) ) {
            $this->view->profiles = Preceptor_Util::toSelect(
                $userModel->fetchRow( array ( "id = ?" => $user->id ) )
                ->findManyToManyRowset( 'Preceptor_Share_Role',
                'Preceptor_Share_UserRole' )
            );
        }

        $this->view->user = $user;

        $this->_helper->layout->setLayout('layout');
    }
}