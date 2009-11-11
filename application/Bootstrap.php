<?php
class Bootstrap extends Xend_Bootstrap
{
	public function _initConstants()
	{
		date_default_timezone_set('America/Sao_Paulo');
        define('SYSTEM_ID',1);
		define('APP_CHARSET','UTF-8');
		define('URL_JAVASCRIPT_LIBRARY','/javascript/');
		define('URL_IMAGE', false );
        define('UPLOAD' , APPLICATION_PATH . '/../data/uploads/');
        define('DEFAULT_THEME' , 'cupertino');

		define( "SMTP_SERVER"   , "smtp.espacoead.com.br" );
		define( "SMTP_USERNAME" , "trails@espacoead.com.br" );
		define( "SMTP_PASSWORD" , "smtptr41ls" );
		define( "SMTP_PORT"     , 25 );
	}
}