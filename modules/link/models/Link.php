<?php
class Link_Model_Link extends Link_Model_Abstract
{
	const TYPE = "L";

    protected $_name 	= 'link';
    protected $_primary = 'id';

    public $filters = array(
		'*'		=> 'StringTrim',
	    'id'    => 'Int'
	);

	public $validators = array(
		'title'	   => array( 'NotEmpty' , array( "StringLength" , 0 , 255  ) ),
		'url' 	   => array( 'NotEmpty' , array( "StringLength" , 0 , 255 ) )
	);

	protected $_dependentTables = array( "Link_Model_LinkCategory" );

    public function _save()
    {
        $user = new Zend_Session_NameSpace( 'user' );
        
        $this->_data['Link_Model_Link']['person_id'] = $user->person_id;
    }

    public function _postSave()
    {
        $linkCategory = new Link_Model_LinkCategory();

        $id = $this->_data['Link_Model_Link']['id'];

        $linkCategory->save( $id );
    }
}