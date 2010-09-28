<?php
class Glossary extends Table 
{
	protected $_name 	= 'trails_glossary';
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
	    'id'   => 'Int',
		'word' => array( 'NotEmpty', array( 'StringLength', 0 , 255 ) ),
		'ds'   => array( 'NotEmpty' )
	);
	
	public function words()
	{
		$words = array();
		
		$result = $this->fetchRelation();
		
		
		foreach( $result as $rs ){	
			$words['word']   = $rs->word;
			$words['encode'] = base64_encode($rs->word);
			$data[] = $words;
		}
		
		return $data;
	}
	
}