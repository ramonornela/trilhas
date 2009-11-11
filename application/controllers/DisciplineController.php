<?php
class DisciplineController extends Application_Controller_Abstract {
    protected $_model = "Station_Model_Discipline";

    public function indexAction() {
        $user = new Zend_Session_Namespace("user");
        $configuration = new Application_Model_Configuration();

        $user->course_id 	 = Zend_Filter::filterStatic( $this->_getParam("id") , "int" );
        $user->discipline_id = Zend_Filter::filterStatic( $this->_getParam("discipline_id") , "int" );
        $user->group_id 	 = Zend_Filter::filterStatic( $this->_getParam("group_id") , "int" );
        $user->theme_id      = 1;

        $this->view->user 	= $user;

        if( !$user->config ) {
            $configuration->loadConfig( $user );
        }

        $cookieName = "theme-".$user->group_id;

        $this->view->theme 	    = isset($_COOKIE[$cookieName]) ? $_COOKIE[$cookieName] : DEFAULT_THEME;
        $this->view->typeLayout = $user->config;

        $this->_helper->layout->setLayout('layout');
    }

    public function menuAction()
    {
        $menu = new Preceptor_Share_Menu();
        $this->view->menuRs = $menu->fetchAll( array("menu_id IS NULL" , "position > 0" , "system_id =?" => Preceptor_Share_System::TRAILS , "status =?" => Preceptor_Share_Menu::ACTIVE ) , "position" );
    }

    public function chatAction()
    {
        $person = new Share_Model_Person();
        $user   = new Zend_Session_Namespace("user");

        $this->view->chatRs = $person->fetchByClass($user->group_id, $user->person_id);
    }

    public function registerAction()
    {
        $discipline_id  = Zend_Filter::filterStatic( $this->_getParam( "discipline_id" ) , "int" );

        $person = new Share_Model_Person();
        $user   = new Zend_Session_Namespace("user");

        $this->view->discipline_id = $discipline_id;
        $this->view->redirect	   = "/discipline/register-confirm/discipline_id/$discipline_id";

        $this->_helper->layout->setLayout( "clearbox" );
    }

    public function registerConfirmAction() {
        $class_id       = Zend_Filter::filterStatic( $this->_getParam( "class_id" ) , "Int" );
        $discipline_id  = Zend_Filter::filterStatic( $this->_getParam( "discipline_id" ) , "Int" );

        $person     = new Share_Model_Person();
        $class      = new Station_Model_ClassModel();
        $discipline = new Station_Model_Discipline();
        $user       = new Zend_Session_Namespace("user");

        $this->view->verifyRegister = false;

        if( isset( $class_id ) && $class_id ) {
            $this->view->groups     = $class->fetchClassAvailable( $discipline_id , $class_id );
            $this->view->discipline = $discipline->fetchForRegister( $discipline_id );
            $this->view->verifyRegister = true;
        }else {
            if( strstr( $user->discipline->all , (string)$discipline_id ) == false ) {
                $this->view->groups         = $class->fetchClassAvailable( $discipline_id );
                $this->view->discipline     = $discipline->fetchForRegister( $discipline_id );
                $this->view->verifyRegister = true;
            }
        }
        $this->_helper->layout->setLayout( "clearbox" );
    }

    public function paymentAction() {
        $class_id  = Zend_Filter::filterStatic( $this->_getParam( "class_id" ) , "Int" );

        $person      = new Share_Model_Person();
        $class       = new Station_Model_ClassModel();
        $classPerson = new Station_Model_ClassPerson();
        $discipline  = new Station_Model_Discipline();
        $user        = new Zend_Session_Namespace("user");
        $user->group = new Zend_Session_Namespace('group');

        if( $class_id ) {
            $user->group->all = $classPerson->forInClass( $user->person_id );

            if ( $user->group->all ) {
                $user->discipline->all = $class->forIn( array(
                    'id IN ( ' . $user->group->all . ' )') ,  "discipline_id" );
            }

            if ( $user->discipline->all ) {
                $user->course->all = $discipline->fetchIn(
                    'd.id IN ( ' . $user->discipline->all . ' )' ,
                    "d.status = '".Station_Model_Discipline::ACTIVE."'"
                );
            }
            $this->view->data   = $person->fetchPersonAddress( $user->person_id );
            $this->view->class  = $class->fetchRow( array( "id =?" => $class_id ) );

            $discount = new Station_Model_Discount();
            $this->view->discount = $discount->fetchDiscountByPerson( $user->person_id , $class_id );
        }

        $this->_helper->layout->setLayout( "clearbox" );
    }
}