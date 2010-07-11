<?php
class ErrorController extends Tri_Controller_Action
{
	public function errorAction()
	{
        $e = $this->_getParam('error_handler')->exception;

        try {
            Zend_Db_Table_Abstract::getDefaultAdapter()->rollBack();
        } catch (Exception $en) {
        }

		echo $e->getMessage();
        echo '<br /><br />';
        $traces = $e->getTrace();
        foreach ($traces  as $trace) {
            if (isset($trace['line'])) {
                echo $trace['line'] . " => " . $trace['file'] . '<br />';
            }
        }
        exit;
	}
}