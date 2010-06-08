<?php
class ErrorController extends Zend_Controller_Action
{
    public function errorAction()
    {
        $e = $this->_getParam('error_handler')->exception;

        try{
            Zend_Db_Table_Abstract::getDefaultAdapter()->rollBack();
        } catch (Exception $en) {
            ;
        }

		echo $e->getMessage();
        echo '<br /><br />';
        $traces = $e->getTrace();
        foreach ($traces  as $trace) {
            echo $trace['line'] . " => " . $trace['file'] . '<br />';
        }
        exit;
    }
}
?>
