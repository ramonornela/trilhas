<?php
class BulletinNote extends Table {
    protected $_name 	= 'trails_bulletin_note';
    protected $_primary = 'id';

    public $filters = array(
            '*'		=> 'StringTrim',
            'id'    => 'Int'
    );

    protected $_referenceMap = array(
            array(
                            'refTableClass' => 'Bulletin',
                            'refColumns' => array( 'id' ),
                            'columns' => array( 'bulletin_id' )
            )
    );

}

?>
