<?php
set_time_limit(0);
define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application') );
$library_path = realpath(APPLICATION_PATH . '/../../library');
set_include_path(realpath(APPLICATION_PATH . '/../../library'));

require_once 'Zend/Loader/Autoloader.php';
require_once 'Zend/Application/Module/Autoloader.php';
$resourceLoader = new Zend_Application_Module_Autoloader(array(
    'basePath'  => APPLICATION_PATH,
    'namespace' => 'Application'
));
$resourceLoader = new Zend_Application_Module_Autoloader(array(
    'basePath'  => APPLICATION_PATH . '/../modules/chat',
    'namespace' => 'Chat'
));

$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Xend_');

$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
$db = Zend_Db::factory($config->resources->db);

Zend_Db_Table_Abstract::setDefaultAdapter($db);

$chat = new Chat_Model_Chat();
$time = mktime(date('H'), date('i'), date('s')+60);

while ($time > time()) {
    $result = $chat->fetchToUser((int) ((string) $_GET['id']), $_GET['ids']);
    if (count($result)) {
        echo Zend_Json::encode($result);
        exit;
    }
    sleep(1);
}
echo "[]";