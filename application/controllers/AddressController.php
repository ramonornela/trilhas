<?php
/**
 * 
 * @author abdalacerqueira
 * @version $Id$
 */
class AddressController extends Application_Controller_Abstract
{
    protected $_model = "Station_Model_Address";

    public function findAddressAction()
    {
        $cep = Zend_Filter::filterStatic( $this->_getParam( "cep" ) , "Digits" );
        
        $cepModel = new Cep_Model_Cep();

        $this->view->data = $cepModel->find( $cep )->current();
        
        $this->_helper->layout->setLayout( "clear" );
    }

}