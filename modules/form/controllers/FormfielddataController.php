<?php
class Form_FormfielddataController extends Controller {
    public function indexAction() {
        $user = new Zend_Session_NameSpace( 'user' );
        $person = new Zend_Session_Namespace("person");

        if ( $person->id )
            unset( $person->id );

        $this->view->rs  = $this->Person->fetchFormPerson( null , "name" );

        $this->view->courses = $this->toSelect( $this->Course->fetchAll( 'id IN ( ' . $user->course->all . ' )' )  , 'id' , 'title' );
        $periods = $this->Period->fetchAll( null , "entered" );

        /**
         * @todo implement method toSelect of the model
         */
        //$select[''] = $this->view->translate( "select" );
        //foreach ( $periods as $period ){
        //$select[$period->id] = $this->view->date( $period->entered ) . " " . $this->view->translate("a") . " " . $this->view->date( $period->expired );
        //}

        //$this->view->periods = $select;
        $status = $this->Person->getStatus();
        $status[Status::ALL] = $this->view->translate('all');

        $this->view->status    = $status;
        $this->view->count     = $this->ViewCountPerson->fetchAll()->current();
        $this->view->state     = $this->toSelect($this->FormFieldValue->fetchAll(array('form_field_id=?'=>13), 'value'), 'value' , 'value');
        $this->view->country   = $this->toSelect($this->FormFieldValue->fetchAll(array('form_field_id=?'=>14), 'value'), 'value' , 'value');
        $this->view->workplace = $this->toSelect($this->FormFieldValue->fetchAll(array('form_field_id=?'=>1), 'value'),  'value' , 'value');

        $this->render( "index" , "clearbox" );
    }

    public function findAction() {
        $letter  = Zend_Filter::get( $this->_getParam( "letter" ) , "HtmlEntities" );

        $person = new Person();
        $this->view->rs = $person->fetchAllPersons($letter);

        $this->render( "find" , "ajax" );
    }

    public function inputAction() {
        $form_id  = Zend_Filter::get( $this->_getParam( "formId" ) , "int" );
        $person_id = Zend_Filter::get( $this->_getParam( "personId" ) , "int" );

        $this->view->roles = $this->toSelect( $this->Role->fetchAll( array( "id > ?" => Role::GUEST )) , "id" , "name" , "" );
        $this->view->userRoles = array();

        if ( $person_id ) {
            $this->view->person = $this->Person->fetchRow( array( "id =?" => $person_id ) );

            $person = new Zend_Session_Namespace("person");
            $person->id = $person_id;

            $userRs = $this->User->fetchRow( array( "person_id = ?" => $person_id ) );
            $roles = $userRs->findDependentRowset( "UserRole" );

            /**
             * @todo implement method toSelect of the model
             */
            foreach ($roles as $role) {
                $checkeds[] = $role->role_id;
            }

            $this->view->userRoles = $checkeds;
        }
        $this->view->form    = $this->FormGroup->fetchFormGroup( $form_id );
        $this->view->status  = $this->Person->getStatus();

        $this->render( 'input' , 'clearbox' );
    }

    public function viewAction() {
        $formId = Zend_Filter::get( $this->_getParam('formId') , 'int' );
        $person_id = Zend_Filter::get( $this->_getParam('personId') , 'int' );
        $this->view->person = $this->Person->fetchRow( array('id =?' => $person_id ) );
        $this->view->form = $this->FormGroup->fetchFormGroup( $formId );

        $this->render( 'view' , 'ajax' );
    }

    public function saveAction() {
        $formId = Zend_Filter::get( $this->_getParam('id') , 'int' );
        $this->view->form = $this->Form->fetchRow( array( "id = ?" => $formId ) );

        $person = new Zend_Session_Namespace("person");

        $values['id']     = Zend_Filter::get( $person->id , 'int' );
        $values['name']   = $_POST['name'];
        $values['email']  = $_POST['email'];
        $values['status'] = $_POST['status'];

        $input = $this->preSave();

        if( $input->isValid() ) {
            /**
             * @todo implement const in model
             */
            $id = $this->Person->save( $values );

            if( $_POST['role_id'] ) {
                $user_id = $this->User->fetchRow( array( "person_id = ?" => $id ) )->id;

                if( $user_id ) {
                    $this->UserRole->delete( $user_id , "user_id" );
                    $dataUser['id'] = $user_id;
                }else {
                    $dataUser['password']  = md5('=trilhas');
                }

                $dataUser['username']  = $values['email'];

                if( $_POST['password'] ) {
                    $dataUser['password'] = md5('='.$_POST['password']);
                }

                $dataUser['person_id'] = $id;

                $user_id = $this->User->save( $dataUser );

                foreach ($_POST['role_id'] as $role_id) {
                    $dataRole['role_id'] = $role_id;
                    $dataRole['user_id'] = $user_id;
                    $this->UserRole->insert( $dataRole );
                }
            }

            $this->view->personId = $id;
            unset( $_POST['name'] , $_POST['email'] , $_POST['role_id'] , $_POST['password'] , $_POST['cpassword'] , $_POST['status'] );

            $this->FormFieldData->delete( $values['id'] , "person_id" );

            $this->FormFieldData->save( $_POST , $id );

            $this->_helper->_flashMessenger->addMessage( $this->view->translate( "registered successfully" ) );

            $this->_redirect( "/form/formfielddata/" );
        }

    }

    public function deleteAction() {
        $id     = Zend_Filter::get( $this->_getParam('created') , 'int' );
        $formId = Zend_Filter::get( $this->_getParam('formId') , 'int' );
        $value  = $this->_getParam('value');

        $this->FormFieldData->delete( $id , 'form_data_created_id' );
        $this->FormDataCreated->delete( $id );

        $this->_redirect( "/form/formfielddata/returnlist/formId/" . $formId . "/value/" . $value );
    }

    public function indexguestAction() {
        $this->view->rs = $this->Form->fetchFormCoursePeriodDateCurrent();

        if ( $this->_getParam( "ajax" ) ) {
            $this->render( "indexguest" , "ajax" );
            return false;
        }

        if( $this->_getParam( "id" ) ) {
            $data['form_id']   = $this->_getParam( "id" );
            $data['course_id'] = $this->_getParam( "courseId" );
            $data['period_id'] = $this->_getParam( "periodId" );
            $this->view->ajaxUp   = $data;
        }

        $this->render( "indexguest" , "guest" );
    }

    public function inputguestAction() {
        $form_id    = Zend_Filter::get( $this->_getParam( "formId" ) , "int" );
        $period_id  = Zend_Filter::get( $this->_getParam( "periodId" ) , "int" );
        $course_id  = Zend_Filter::get( $this->_getParam( "courseId" ) , "int" );

        $period = $this->Period->fetchPeriodDateCurrent( $course_id , $period_id , $form_id );

        if ( !$period )
            $this->_redirect( "/form/formfielddata/indexguest" );

        $form = new Zend_Session_Namespace("form");

        $form->form_id    = $form_id;
        $form->period_id  = $period_id;
        $form->course_id  = $course_id;

        $this->view->form = $this->FormGroup->fetchFormGroup( $form->form_id );

        $this->render( 'inputguest' , 'ajax' );
    }

    public function saveguestAction() {
        $form = new Zend_Session_Namespace("form");

        $this->view->form = $this->Form->fetchRow( array( "id = ?" => $form->form_id ) );

        $values['name']  = $_POST['name'];
        $values['email'] = strtolower($_POST['email']);

        $input = $this->preSave();

        if( $input->isValid() ) {
            $data['name'] = $values['name'];
            $data['email'] = $values['email'];
            /**
             * @todo create const in model
             */
            $data['status'] = Status::ACTIVE;

            $id = $this->Person->save( $data );

            $this->Signup->save( array( "person_id" => $id , "period_id" => $form->period_id , "course_id" => $form->course_id ) );

            unset( $_POST['name'] , $_POST['email'] );

            $this->FormFieldData->save( $_POST , $id );

            $values['password'] = rand(111111, 999999);

            $user_id = $this->User->save( array( 'person_id' => $id , 'username' => $values['email'] , 'password' => md5( User::LOGIN_TRASH . $values['password'] ) ) );
            $this->UserRole->saveStudent( array( 'role_id' => Role::STUDENT , 'user_id' => $user_id ) );

            if ( $this->view->form->email )
                $this->sendData( $this->view->form );

            if ( $this->view->form->text_email )
                $this->sendEmail( $this->view->form ,$values );

            $this->view->personId = $id;
            $this->_helper->_flashMessenger->addMessage( $this->view->translate( "registered successfully waiting" ) );

            $this->_redirect( "/form/formfielddata/indexguest/ajax/true" );
        }

    }

    public function confirmationregisteredAction() {
        $id = Zend_Filter::get( $this->_getParam( "id" ) , "int" );
        $this->view->person = $this->Person->fetchRow( array('id =?' => $id ) );
        $user = $this->User->fetchRow( array('person_id =?' => $id ) );

//		if ( $user->password )
//		{
//			$this->_helper->_flashMessenger->addMessage( $this->view->translate( "this registration confirmed" ) );
//			$this->_redirect( "/user/login/" );
//		}

        $user_confirmation = new Zend_Session_Namespace("confirmation");
        $user_confirmation->id = $user->id;
        $user_confirmation->email = $this->view->person->email;

        $this->view->jsonValidate = Zend_Json::encode( $this->User->validators );

        $this->render( 'confirmationregistered' , 'guest' );
    }

    public function sendData( $form ) {
        $options = array(
                'auth' => 'login',
                'username' => SMTP_USERNAME,
                'password' => SMTP_PASSWORD,
                'port'     => SMTP_PORT
        );

        $transport = new Zend_Mail_Transport_Smtp( 'hw12.webservidor.net' , $options );

        $mail = new Zend_Mail( APP_CHARSET );

        $mail_from->setBodyHtml( $this->view->render( 'frommail.tpl' ) );
        $mail_from->setFrom( $form->email , 'EAD' );
        $mail_from->addTo( $form->email , 'EAD' );
        $mail_from->setSubject( $form->subject );

        try {
            $mail_from->send( $transport );
        }catch( Exception $e ) {

        }
    }

    public function sendEmail( $form, $values ) {
        $options = array(
                'auth' => 'login',
                'username' => SMTP_USERNAME,
                'password' => SMTP_PASSWORD,
                'port'     => SMTP_PORT
        );

        $transport = new Zend_Mail_Transport_Smtp(SMTP_SERVER);
        $mail = new Zend_Mail(APP_CHARSET);

        $text = "<div style='width:560px'><img src='http://www17.senado.gov.br/trilhas/img/banner_email.gif' border='0'>"
                ."<div style='width:490px;margin-left:36px;'><b>Cadastro CONFIRMADO.</b><br/><br/>Esperamos que fique satisfeito com o novo ambiente virtual de aprendizagem e com o curso oferecido, mas contamos com suas sugestões para aperfeiçoar o nosso trabalho.<br/><br/>Acesse<br/>"
                ."URL: <b>http://www.senado.gov.br/trilhas</b><br/>"
                ."Login: <b>{$values['email']}</b><br/>"
                . "Senha: <b>{$values['password']}</b><br/></div></div>";

        $mail->setBodyHtml( $text );
        //$mail->setBodyHtml( $form->text_email.$text );
        $mail->setFrom( 'ilbeadmatriculas@senado.gov.br' , 'Senado - ILB - Instituto Legislativo Brasileiro' );
        $mail->addTo( $values['email'] , $values['name'] );
        $mail->setSubject( $form->subject );

        try {
            $mail->send( $transport );
        }catch( Exception $e ) {

        }
    }

    public function selectedFindAction() {
        $course_id     = Zend_Filter::get( $this->_getParam( "course_id" ) , "int" );
        $discipline_id = Zend_Filter::get( $this->_getParam( "discipline_id" ) , "int" );

        if(!empty($course_id)) {
            $this->view->disciplines = $this->toSelect($this->Discipline->fetchByCourse($course_id) , "id" , "title");
            $this->render('find-discipline', 'ajax');
        }

        if(!empty($discipline_id)) {
            $this->view->groups = $this->toSelect($this->Group->findByDiscipline($discipline_id) , "id" , "title");
            $this->render('find-group', 'ajax');
        }
    }

    public function additionalDataAction() {
        $person_id = Zend_Filter::get( $this->_getParam( "person_id" ) , "int" );
        $this->view->person = $this->Person->fetchRow(array('id=?'=>$person_id));

        $this->render(null, 'ajax');
    }
}
