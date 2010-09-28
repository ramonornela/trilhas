<?php
class BulletinGroup extends Table
{
	protected $_name 	= 'trails_bulletin_group';
    protected $_primary = 'id';
    
    public $filters = array(
		'*'		=> 'StringTrim',
	    'id'    => 'Int'
	);
		
	public $validators = array(
	    'id'    => 'Int',
		'title'	=> array( 'NotEmpty' , array( "StringLength" , 0 , 255  ) ),
	);
	
	protected $_dependentTables = array( "Bulletin" , "RestrictionBulletin" );
		
}

?>
