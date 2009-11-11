<?php
class Rss_Model_Rss extends Rss_Model_Abstract
{
	protected $_name = 'rss';
	protected $_primary = 'id';

	protected $_referenceMap = array(
		array(
			'refTableClass' => 'Rss',
			'refColumns'	=> array( 'relation' ),
			'columns'		=> array( 'relation' )
		),
	);

	public $filters = array(
		'*'		=> 'StringTrim',
	    'id'    => 'Int'
	);

	public $validators = array(
		'channel' => array( 'NotEmpty' , array( 'StringLength', 0 , 255 ) ),
		'url' 	=> array( 'NotEmpty' , array( 'StringLength', 0 , 255 ) )
	);

    public function _save()
    {
        $user = new Zend_Session_Namespace('user');

        $this->_data['Rss_Model_Rss']['person_id'] = $user->person_id;
    }
	

}