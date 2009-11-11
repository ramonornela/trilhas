<?php
class GroupController extends Application_Controller_Abstract
{	
	public function indexAction()
	{
		$this->view->rs = $this->Course->fetchAll( array( "status = ?" => Status::ACTIVE ) , "title" );
		
		$this->render( null , "clearbox" );
	}
	
	public function inputAction()
	{
		$id            = Zend_Filter::filterStatic( $this->_getParam( "id" ) , 'int' );
        $discipline_id = Zend_Filter::filterStatic( $this->_getParam( "discipline_id" ) , 'int' );
        $course_id     = Zend_Filter::filterStatic( $this->_getParam( "course_id" ) , 'int' );
        
		$checked = $this->toOptGroup();
		$notIn 	 = 0;

		$rs    = $this->PersonGroup->fetchAllInGroup( $id , $discipline_id );
		$notIn = $this->forIn( $rs );

        $this->view->data->discipline_id = $discipline_id;
        $this->view->course_id = $course_id;
        
		if( $id ){
			$this->view->data = $this->Group->find( $id )->current();
			$checked = $this->toOptGroup( $this->PersonGroup->fetchAll( array( "group_id = ?" => $id ) , "role_id" ) , "PersonGroup" );
		}

        $this->filter();

		$this->view->status  = $this->Group->getStatus();
		$this->view->checked = $checked;
		$this->view->all     = $this->toOptGroup();
		$this->view->id      = $id;
		$this->view->jsonValidate = Zend_Json::encode( $this->Group->validators );
		
		$this->render( null , "clearbox" );
	}
	
	public function findAction()
	{
		$period         = $this->_getParam( "period" );
		$id             = Zend_Filter::filterStatic( $this->_getParam( "id" ) , 'int' );
		$discipline_id  = Zend_Filter::filterStatic( $_POST["discipline_id"] , 'int' );
		$course_id      = Zend_Filter::filterStatic( $_POST["course_id"] , 'int' );

		$checked = $this->toOptGroup();
		$notIn 	 = 0;

		$rs 	 = $this->PersonGroup->fetchAllInGroup( $id , $discipline_id );
		$notIn 	 = $this->forIn( $rs );
		
		$this->view->data->discipline_id = $discipline_id;
		$this->view->course_id = $course_id;
		
		$this->view->status = $this->Group->getStatus();
		
		if( $id )
		{
			$this->view->data = $this->Group->find( $id )->current();
			$checked = $this->toOptGroup( $this->PersonGroup->fetchAll( array( "group_id = ?" => $id ) , "role_id" ) , "PersonGroup" );
		}
			 
		$select = $this->UserRole->select();
		
		if( $notIn )
		{
			foreach( $notIn as $key => $val )
			{
				$select->where( 'NOT( ( user_id =?' , $val['user_id'] );
				$select->where( 'role_id = ? ) )' , $val['role_id'] );
			}
		}
		
		if ( $period )
			$period_in = $this->findByPeriod( $notIn );
		else
			$period_in = $this->findByCourse( $notIn );

		if ( ! $period_in )
			$period_in = 0;

		$select->where( "user_id IN( $period_in )" );
		$select->where( "role_id <> 6" );
		$select->order( "role_id" );
		
		$this->view->checked = $checked;
        $this->view->id      = $id;
        
		$this->view->all 	 = $this->toOptGroup( $this->UserRole->fetchAll( $select ) , "UserRole" );
		
		$this->view->jsonValidate = Zend_Json::encode( $this->Group->validators );
		
		$this->render( null , "ajax" );
	}
	
	public function saveAction()
	{
		$input = $this->preSave();
		
		if( $input->isValid() ){
			$data = $this->setNull( $input->toArray() );
			
			if ( $id = $this->Group->save( $data ) ){
				$persons = $this->PersonGroup->save( $id );

                if( $persons ){
                    if ( $data['status'] == Status::ACTIVE ){
                        $this->sendMail( $persons , $id , $data['discipline_id'] , $data['course_id'] );
                    }
                }
                
				$this->_helper->_flashMessenger->addMessage( $this->view->translate( "registered successfully" ) );
				$this->_redirect( "/group/" );
			}else {
				$this->postSave( false , $input );
            }
		}
		
		$this->postSave( false , $input );
	}
	
	public function disciplineAction()
	{
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
		
		$this->view->disciplines = $this->toSelect( $this->Discipline->fetchByCourse( $id ) , "id"  , "title" , $this->view->translate( "select" ) );
		
		$this->render( null , "ajax" );
	}
	
	public function groupAction()
	{
		$id = Zend_Filter::filterStatic( $this->_getParam( "id" ) , "int" );
		
		$this->view->groups = $this->toSelect( $this->Group->fetchAll( array( "discipline_id = ?" => $id ) ) , "id" , "title" , $this->view->translate( "select" ) );
		
		$this->render( null , "ajax" );
	}
	
	public function toOptGroup( $result = null , $type = null ) 
	{
		$selects = array();
		
		$roles = array( 
			2 => $this->view->translate("student"),
			3 => $this->view->translate("teacher"),
			4 => $this->view->translate("coordinator"),
			5 => $this->view->translate("specialist")
		);
		
		foreach ( $roles as $role )
			$selects[ $role ]= "";
			
		if( $type == "PersonGroup" ){
			foreach( $result as $rs ){
				$selects[ $roles[$rs->role_id] ]["[{$rs->person_id} , {$rs->role_id} ]"] = $rs->findParentRow( "Person" )->name;
            }
		}
		
		if( $type == "UserRole" ){
			foreach( $result as $rs ){
				$selects[ $roles[$rs->role_id] ]["[{$rs->findParentRow( "User" )->person_id} , $rs->role_id]"] = $rs->findParentRow( 'User' )->findParentRow( "Person" )->name;
            }
		}
		
		return $selects;
	}
	
	public function forIn( $result ) 
	{
		$nots = array();
		
		if ( $result ){
			foreach( $result as $rs ){
				$nots[] = array( "user_id" => $rs->findParentRow( 'Person' )->findDependentRowSet( 'User' )->current()->id , "role_id" => $rs->role_id );
            }
		}	
		return $nots;
	}
	
	public function filter()
	{
		$this->view->courses = $this->toSelect( $this->Course->fetchAll( null , "title" )  , 'id' , 'title' );
		$periods = $this->Period->fetchAll( null , "entered" );
		
		$select[''] = $this->view->translate("select"). " ...";
		foreach ( $periods as $period ){
			$select[$period->id] = $this->view->date( $period->entered ) . " " . $this->view->translate("a") . " " . $this->view->date( $period->expired );
		}
		
		$this->view->periods = $select;
	}
	
	public function findByPeriod( $notIn )
	{
		
		if ( $_POST['course'] ){
			$where['course_id = ?'] = $_POST['course'];
        }

		if( $_POST['period'] ){
			$where['period_id = ?'] = $_POST['period'];
        }

		$persons = $this->Person->findPersonByPeriod( $where );

		foreach ( $persons as $person ){
			$in[] = $person->id;
        }
		
		if ( $in ){
			return join( "," , $in );
        }

        return null;
	}
	
	public function findByCourse( $notIn )
	{
		if ( $_POST['course'] )
			$where['cd.course_id = ?'] = $_POST['course'];
		
		if( $_POST['discipline'] )
			$where['d.id = ?'] = $_POST['discipline'];
			
		if( $_POST['group'] )
			$where['g.id = ?'] = $_POST['group'];
			
		$persons = $this->Person->findPersonByCourse( $where , $notIn );	
		
		foreach ( $persons as $person )
			$in[] = $person->id;
		
		if ( $in )
			return join( "," , $in );
	}
	
	public function sendMail( $persons_id , $group_id , $discipline_id , $course_id )
	{
		$user = new Zend_Session_Namespace("user");
        $where = array(
            'id IN (' . join( "," , $persons_id ) . ')',
            'mail IS NULL'
        );
		
        $discipline = $this->Discipline->find( $discipline_id )->current()->title; 
		$course     = $this->Course->find( $course_id )->current()->title;
		
		$msg  = $this->view->translate('congratulations')."!<br /><br />";
		$msg .= $this->view->translate('his registration was carried out for') . " ";
		$msg .= $course." >> ".$discipline. ", "; 
		$msg .= $this->view->translate('in the virtual environment of learning trails').".<br /><br />";
		
		$persons = $this->Person->fetchAll( $where );
		
		$options = array(
			'auth' => 'login',
            'username' => SMTP_USERNAME,
          	'password' => SMTP_PASSWORD,
          	'port'     => SMTP_PORT
		); 
			
		$transport = new Zend_Mail_Transport_Smtp( SMTP_SERVER , $options );
		$subject   = $this->view->translate( 'confirmation of registration (environment trails)' );
		
        if( $persons->count() ){
            foreach ( $persons as $person ) {
                $mail = new Zend_Mail( APP_CHARSET );

                $mail->setBodyHtml( $msg . "<br /><a href='" . URL . "/form/formfielddata/confirmationregistered/id/{$person->id}'>".$this->view->translate('click here to confirm your registration'). "</a>" );
                $mail->setFrom( 'trails@espacoead.com.br' , $this->view->translate( 'trails educational environment' ) );
                $mail->addTo( $person->email , $person->name );
                $mail->addHeader('Reply-To', $user->email );
                $mail->setSubject( $subject );
				
                try {
                	$mail->send( $transport );
                }catch ( Exception $e ){}
                $data['id'] = $person->id;
                $data['mail'] = Person::MAIL;

                $this->Person->save( $data );
            }
        }
	}
	
}
