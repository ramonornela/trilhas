<?php
class Glossary_Model_Glossary extends Glossary_Model_Abstract
{
	protected $_name 	= 'glossary';
    protected $_primary = 'id';

    protected $_referenceMap = array( 
		array(
			'refTableClass' => 'Relation',
			'refColumns'	=> array( 'relation' ),
			'columns'		=> array( 'relation' )
		)
	);
	
	public $filters = array(
		'*'	 => 'StringTrim',
	    'id' => 'Int'
	);
		
	public $validators = array(
		'word' => array( 'NotEmpty', array( 'StringLength', 0 , 255 ) ),
		'ds'   => array( 'NotEmpty' )
	);
	
	public function words()
	{
		$words = array();
		
		$result = $this->fetchRelation();
		
		foreach( $result as $rs ){
            if( !in_array( $rs->word , $words ) ){
                $words[] = $rs->word;
            }
		}
		
		return $words;
	}

    public function _save()
    {
        $user = new Zend_Session_Namespace('user');

        $this->_data['Glossary_Model_Glossary']['person_id'] = $user->person_id;
    }
	
}