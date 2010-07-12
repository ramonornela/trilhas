<?php
/**
 * @package    Auth
 * @subpackage Adapter
 */
class Tri_Auth_Adapter_DbTable extends Zend_Auth_Adapter_DbTable
{
    /**
     * _authenticateCreateAuthResult() - Creates a Zend_Auth_Result object from
     * the information that has been collected during the authenticate() attempt.
     *
     * @return Zend_Auth_Result
     */
    protected function _authenticateCreateAuthResult()
    {
        return new Zend_Auth_Result(
            $this->_authenticateResultInfo['code'],
            $this->getResultRowObject(),
            $this->_authenticateResultInfo['messages']
            );
    }
}