<?php
class Bulletin_Model_BulletinNote extends Bulletin_Model_Abstract
{
	protected $_name 	= 'bulletin_note';
    protected $_primary = 'id';
    
    public $filters = array(
		'*'		=> 'StringTrim',
	    'id'    => 'Int'
	); 
		
	protected $_referenceMap = array( 
		array(
			 'refTableClass' => 'Bulletin_Model_Bulletin',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'bulletin_id' )
		)
	);
}