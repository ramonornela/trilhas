<?php
class Category_Model_Category extends Category_Model_Abstract
{
	protected $_name 	= 'category';
    protected $_primary = 'id';

    public $filters = array(
		'*'		=> 'StringTrim',
	    'id'    => 'Int'
	);

	public $validators = array(
		'name'	 => array( 'NotEmpty' , array( "StringLength" , 0 , 255  ) ),
		'type'	 => array( 'NotEmpty' , array( "StringLength" , 0 , 1  ) )
	);

	protected $_dependentTables = array( 'Faq_Model_FaqCategory' , 'Link_Model_LinkCategory' );

    public function _save()
    {
        $user = new Zend_Session_Namespace('user');
        $this->_data['Category_Model_Category']['person_id'] = $user->person_id;
    }

}