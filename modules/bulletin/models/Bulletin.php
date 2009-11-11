<?php
class Bulletin_Model_Bulletin extends Bulletin_Model_Abstract
{
	const EVALUATION = 1;
	const FORUM 	 = 2;
	const ACTIVITY 	 = 3;
	const NOTEPAD 	 = 4;
	const GLOSSARY   = 5;
	
	protected $_name 	= 'bulletin';
    protected $_primary = 'id';
    
    public $filters = array(
		'*'		=> 'StringTrim',
	    'id'    => 'Int'
	);
	
	protected $_dependentTables = array( "Bulletin_Model_BulletinNote" ,
											"Evaluation_Model_EvaluationNote" );
	
	protected $_referenceMap = array( 
		array(
			 'refTableClass' => 'Bulletin_Model_BulletinGroup',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'bulletin_group_id' )
		),
		array(
			 'refTableClass' => 'Activity_Model_Activity',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'item' )
		),
		array(
			 'refTableClass' => 'Evaluation_Model_Evaluation',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'item' )
		),
		array(
			 'refTableClass' => 'Forum_Model_Forum',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'item' )
		)
	);
	
	public function save( $id )
	{
		$result = Zend_Json::decode( $_POST["json_relation_bulletin"] );
		
		if ( $result ){
			foreach ( $result as $val ){
				if ( $val['id'] == "0" ){
					$val['id'] = 0;
				}
				
				$bulletin = $this->fetchRow( array( 'bulletin_group_id =?' => $id , 'module =?' => $val['type'] , 'item =?' => $val['id'] ) );
				
				$data['id'] = $bulletin ? $bulletin->id : 0;
				
				$data['bulletin_group_id'] = $id;
				$data['module']            = $val['type'];
				$data['item']              = $val['id'];
				
				$bulletins_id[] = parent::save( array('Bulletin_Model_Bulletin'=>$data ));
			}
		}
		
		$note = new Bulletin_Model_BulletinNote();
		
		$ids = array(0);
		if ( $bulletins_id ){
			foreach ( $bulletins_id as $value ){
				$ids[] = $value->detail['id'];
			}
		}
		
		$bulletins = $this->fetchAll( array( 'id NOT IN('. join( "," , $ids ) . ')'  , 'bulletin_group_id =?' => $id ) );
		
		foreach ( $bulletins as $bulletin ){
			$note->delete( array( 'bulletin_id' => $bulletin->id ) );
			$this->delete( $bulletin->id );
		}
	}
	
}
