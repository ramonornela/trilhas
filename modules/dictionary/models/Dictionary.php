<?php
class Dictionary_Model_Dictionary extends Dictionary_Model_Abstract
{
	protected $_name 	= 'dictionary';
    protected $_primary = 'id';

    public $filters = array(
		'*'		=> 'StringTrim',
	    'id'    => 'Int'
	);

	public $validators = array(
	    'title'	=> array( 'NotEmpty' , array( "StringLength" , 0 , 255  ) ),
		'ds' 	=> array( 'NotEmpty' , array( "StringLength" , 0 , 3000 ) ),
		'url' 	=> array( 'NotEmpty' , array( "StringLength" , 0 , 255  ) )
	);

    public function _save()
    {
        $user = new Zend_Session_NameSpace( 'user' );

        $this->_data['Dictionary_Model_Dictionary']['person_id'] = $user->person_id;
    }

}