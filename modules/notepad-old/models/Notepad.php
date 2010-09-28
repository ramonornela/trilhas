<?php
class Notepad extends Table 
{
	protected $_name 	= 'trails_notepad';
    protected $_primary = 'id';
    
    public $filters = array(
		'*'				=> 'StringTrim',
	    'id'    => 'Int'
	);
		
	public $validators = array(
	    'content_id'    => 'Int',
		'person_id'	 	=> array( 'NotEmpty' , 'Int' ),
		'discipline_id' => array( 'NotEmpty' , 'Int' ),
		'ds' 	 	    => array( 'NotEmpty' ),
	);
    
	protected $_referenceMap = array( 
		array(
			 'refTableClass' => 'Content',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'content_id' )
		),
        array(
			 'refTableClass' => 'Discipline',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'discipline_id' )
		)
	);
}