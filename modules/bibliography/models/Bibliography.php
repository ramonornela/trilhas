<?php
class Bibliography_Model_Bibliography extends Bibliography_Model_Abstract
{
	protected $_name 	= 'bibliography';
    protected $_primary = 'id';

    const BASIC = 1;

    const ADDITIONAL = 2;

    public $filters = array(
		'*'		=> 'StringTrim',
	    'id'    => 'Int'
	);

	public $validators = array(
		'title'   => array( 'NotEmpty' , array( "StringLength" , 0 , 3000 ) ),
		'type' => array( 'NotEmpty' ),

	);

    public function _save()
    {
        $user = new Zend_Session_Namespace('user');
        
        $this->_data['Bibliography_Model_Bibliography']['person_id'] = $user->person_id;
    }
}