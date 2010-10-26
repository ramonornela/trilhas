<?php
class Bulletin extends Table
{
	const EVALUATION = 1;

	const FORUM 	 = 2;
	
	const ACTIVITY 	 = 3;
	
	const NOTEPAD 	 = 4;
	
	const GLOSSARY   = 5;
	
	protected $_name 	= 'trails_bulletin';
    protected $_primary = 'id';
    
    public $filters = array(
		'*'		=> 'StringTrim',
	    'id'    => 'Int'
	);
	
	protected $_dependentTables = array( "BulletinNote" , "EvaluationNote" );
	
	protected $_referenceMap = array( 
		array(
			 'refTableClass' => 'BulletinGroup',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'bulletin_group_id' )
		),
		array(
			 'refTableClass' => 'Activity',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'item' )
		),
		array(
			 'refTableClass' => 'Evaluation',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'item' )
		),
		array(
			 'refTableClass' => 'Forum',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'item' )
		)
	);
	
	public function save( $id )
	{
		$result = Zend_Json::decode( $_POST['json_relation'] );
		if ( ! $result )
			return false;
			
		foreach ( $result as $val )
		{
			if ( $val['id'] == "0" )
				$val['id'] = 0;

			$bulletin = $this->fetchRow( array( 'bulletin_group_id =?' => $id , 'module =?' => $val['type'] , 'item =?' => $val['id'] ) );
			
			if ( $bulletin )
				$saves['id'] = $bulletin->id;
			else
				$saves['id'] = 0;
				
			$saves['bulletin_group_id'] = $id;
			$saves['module']            = $val['type'];
			$saves['item']              = $val['id'];
			
			$bulletin_id[] = parent::save( $saves );
			
		}

		$bulletins = $this->fetchAll( array( 'id NOT IN('. join( "," , $bulletin_id ) . ')'  , 'bulletin_group_id =?' => $id ) );
		$note = new BulletinNote();
		foreach ( $bulletins as $value )
		{
			$note->delete( $value->id , "bulletin_id" );
			$this->delete( $value->id );
		}
		
	}
	
}
