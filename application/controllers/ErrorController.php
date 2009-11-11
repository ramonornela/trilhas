<?php
class ErrorController extends Zend_Controller_Action
{
	public function errorAction()
	{
		if( APPLICATION_ENV == 'production' ){
			$errors = "<h1>Erro na plataforma</h1><br />";
			$errors .= $_SERVER['HTTP_REFERER'] . "<br />";
			$errors .= $_SERVER['REQUEST_URI'] . "<br />";
			$errors .= "<pre>" . var_dump( $this->_getAllParams() ) . "</pre>";
			$errors .= "<pre>" . var_dump( $this->_getParam('error_handler') ) . "</pre>";

			$mail = new Zend_Mail();
			$mail->setBodyHtml( $errors );
			$mail->setFrom( "error@preceptoread.com.br" , "Error reporting" );
			$mail->addTo( "ramonmast3r@gmail.com" , "Ramon" );
			$mail->addTo( "sodrejava@gmail.com" , "Sodre" );
			$mail->addTo( "abdala.cerqueira@gmail.com" , "Abdala" );
			$mail->setSubject( "Erro na plataforma" );
			$mail->send();
		}else{
			include( 'backtrace.php' );

			$e = $this->_getParam('error_handler')->exception;
			$oTrace = new BackTrace( $e->getTrace() );
			echo $e->getMessage();
			echo $oTrace->explain();
			exit;
		}
	}
}