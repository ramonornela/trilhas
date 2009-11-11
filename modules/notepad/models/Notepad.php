<?php
class Notepad_Model_Notepad extends Notepad_Model_Abstract
{
	protected $_name 	= 'notepad';
    protected $_primary = 'id';

    public $filters = array(
		'*'	 => 'StringTrim',
	    'id' => 'Int'
	);

	public $validators = array(
		'ds' => array( 'NotEmpty' )
	);

	protected $_referenceMap = array(
		array(
			 'refTableClass' => 'Content_Model_Content',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'content_id' )
		),
        array(
			 'refTableClass' => 'Station_Model_Discipline',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'discipline_id' )
		)
	);

    public function _save()
    {
        $user = new Zend_Session_NameSpace( 'user' );

        $this->_data['Notepad_Model_Notepad']['person_id'] = $user->person_id;
		$this->_data['Notepad_Model_Notepad']['discipline_id'] = $user->discipline_id;
    }
}