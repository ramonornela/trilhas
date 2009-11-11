<?php
class Notepad_NotepadController extends Application_Controller_Abstract
{
    protected $_model = "Notepad_Model_Notepad";

    public function indexAction()
	{
		$id        = Zend_Filter::filterStatic( $this->_getParam( "id" ) , 'int' );
		$person_id = Zend_Filter::filterStatic( $this->_getParam( "person_id" ) , 'int' );
		$user      = new Zend_Session_Namespace('user');

        $notepad = new $this->_model();


        if ( !$person_id ){
			$person_id = $user->person_id;
        }

		if( $id ){
			$this->view->data = $notepad->find( $id )->current();
        }

		$rs = $notepad->fetchAll( array( 'person_id =?' => $person_id , 'discipline_id =?' => $user->discipline_id ) , array( "created DESC" ) );

		$this->view->rs = $rs;
		$this->view->dt = $this->getDates( $rs );
		$this->view->person_id = $person_id;
	}

	public function findAction()
	{
		$user       = new Zend_Session_Namespace('user');
        $notepad    = new Notepad_Model_Notepad();
        
		$date = str_replace( "," , "-" , $this->_getParam( "date" ) );

		$this->view->rs = $notepad->fetchAll( array ( 'created = ?' => $date , 'person_id = ?' => $user->person_id , 'discipline_id =?' => $user->discipline_id ) , "id DESC" );
		$this->view->dt = $this->getDates( $this->Notepad->fetchAll( array( 'person_id =?' => $user->person_id , 'discipline_id =?' => $user->discipline_id ) , array( "created DESC" ) ) );

		$this->_redirect( "notepad/notepad/index" );
	}

	public function getDates( $result )
	{
		$dates = array();

        foreach( $result as $rs ){
			$dates[] = $this->view->date( $rs->created , "m/d/Y" );
		}

        if( count($dates) ){
            return join( "," , $dates );
        }

        return;
	}
}