<?php
// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application') );

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

$library_path = realpath(APPLICATION_PATH . '/../../library');

// Ensure library/ is on include_path
set_include_path($library_path);

function debug( $value , $exit = 0 ){
    echo "<fieldset><legend>Debug</legend>";
    ob_start();
    var_dump($value);
    highlight_string( "<?php\n" . ob_get_clean() . "?>\n" );

    $aTrace = debug_backtrace();
	echo $aTrace[0]['file'] . " - " . $aTrace[0]['line'];
    echo "</fieldset><br />";

    if( $exit ){
        exit();
    }
}

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap()
	        ->run();