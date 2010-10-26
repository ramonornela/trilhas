<?php
class Email_EmailController extends Controller {
    public function indexAction() {
        $user         = new Zend_Session_Namespace("user");
        $personGroup  = new PersonGroup();
        $group        = new Group();

        $this->view->rs    = $personGroup->fetchAllStudents($user->group_id, true);
        $this->view->group = $group->fetchRow( array( 'id =?' => $user->group_id ) );


        if( Zend_Filter::filterStatic( $this->_getParam( "tpl" ) , "HtmlEntities" ) ) {
            $this->render( null , 'ajax' );
        }else {
            $this->render( null , $this->getLayout() );
        }
    }

    public function sendAction() {
        $user      = new Zend_Session_Namespace("user");
        $transport = new Zend_Mail_Transport_Smtp(SMTP_SERVER);
        $mail      = new Zend_Mail(APP_CHARSET);

        $person = $this->Person->fetchRow(array('id =?' => $user->person_id));

        $text = "<div style='width:560px;background: #fff;'>
                <img src='http://www17.senado.gov.br/trilhas/img/banner_email.gif' border='0'>
                <div style='width:490px;margin-left:36px;'>
                    {$_POST['ds']}
                </div>
                </div>";

        $mail->setBodyHtml( $text );
        $mail->setFrom( 'ilbeadmatriculas@senado.gov.br' , 'Senado - ILB - Instituto Legislativo Brasileiro' );
        $mail->setSubject( $_POST['subject'] );
        $mail->addHeader('Reply-To', $person->email );
        $mail->setReturnPath($person->email);

        foreach($_POST['persons'] as $name => $email){
                $mail->addCc(trim($email), $name);
        }

        try {
            $mail->send( $transport );
            $this->_helper->_flashMessenger->addMessage( $this->view->translate( "Enviado com sucesso" ) );
        }catch( Exception $e ) {
            $this->_helper->_flashMessenger->addMessage( $this->view->translate( "Error ao enviar" ) );
        }

        $this->_redirect('/bulletin/bulletin/index/tpl/ajax');
    }

    public function viewAction() {
        $this->view->ds =  $_POST['ds'];
        $this->render(null, 'ajax');
    }
}
?>