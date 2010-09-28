<?php         
class Notepad_NotepadController extends Controller 
{
	//public $uses = array( "Notepad" , "Content" , "Discipline" );
	
	public function indexAction()
	{ 
		$id        = Zend_Filter::filterStatic( $this->_getParam( "id" ) , 'int' );
		$person_id = Zend_Filter::filterStatic( $this->_getParam( "person_id" ) , 'int' );
		$user      = new Zend_Session_Namespace('user');
		$limit     = 5;
		$to 	   = Zend_Filter::filterStatic( $this->_getParam('to') , 'int' );

        if ( !$person_id ){
			$person_id = $user->person_id;
        }

		if( $id ){
			$this->view->data = $this->Notepad->find( $id )->current();
        }
        	
		//$rs = $this->Notepad->fetchAll( array( 'person_id =?' => $person_id , 'discipline_id =?' => $user->discipline_id ) , array( "created DESC" ) );
		
		$this->view->rs     = $this->Notepad->fetchAll( array( 'person_id =?' => $person_id , 'discipline_id =?' => $user->discipline_id ) , array( "created DESC" ) , $limit , ( $to * $limit )  );
		$this->view->counts = $this->paginateSelect( ceil( $this->Notepad->count( array( 'person_id =?' => $person_id , 'discipline_id =?' => $user->discipline_id ) ) / $limit ) );

		//$this->view->rs = $rs;
		//$this->view->dt = $this->getDates( $rs );
        $this->view->limit  = $limit;
		$this->view->to 	= $to;

		$this->view->person_id = $person_id;
		
		$this->render( null , $this->getLayout() );
	}
	
	public function saveAction()
	{
		$user = new Zend_Session_Namespace('user');
		
		$input = $this->preSave();
		
		if( $input->isValid() )
		{
			$data = $input->toArray();
			$data['person_id']     = $user->person_id;
			$data['discipline_id'] = $user->discipline_id;
            
			if ( $this->Notepad->save( $data ) ) 
				$this->postSave( true , $input );
			else
				$this->postSave( false , $input );
		}
		
		$this->postSave( $saved , $input );
	}
	
	public function findAction()
	{
		$user = new Zend_Session_Namespace('user');
		$date = str_replace( "," , "-" , $this->_getParam( "date" ) );

		$this->view->rs = $this->Notepad->fetchAll( array ( 'created = ?' => $date , 'person_id = ?' => $user->person_id , 'discipline_id =?' => $user->discipline_id ) , "id DESC" );
		
		//$this->view->rs = $this->Notepad->fetchDate( $date );
		$this->view->dt = $this->getDates( $this->Notepad->fetchAll( array( 'person_id =?' => $user->person_id , 'discipline_id =?' => $user->discipline_id ) , array( "created DESC" ) ) );
		
		$this->render( "index" );
	}
	
	public function getDates( $result )
	{
		foreach( $result as $rs )
		{
			$dates[] = $this->view->date( $rs->created , "m/d/Y" );
		}

        if( count($dates) ){
            return join( "," , $dates );
        }

        return;
	}
}