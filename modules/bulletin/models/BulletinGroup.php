<?php
class Bulletin_Model_BulletinGroup extends Bulletin_Model_Abstract
{
	protected $_name 	= 'bulletin_group';
    protected $_primary = 'id';
    
    public $filters = array(
		'*'		=> 'StringTrim',
	    'id'    => 'Int'
	);
		
	public $validators = array(
		'title'	=> array( 'NotEmpty' , array( "StringLength" , 0 , 255  ) ),
	);
	
	protected $_dependentTables = array( "Bulletin_Model_Bulletin" , "Content_Model_RestrictionBulletin" );
	
	public function verify( $person_id , $certificate_id )
	{
		$user = new Zend_Session_Namespace( "user" );
		$certificate_person = new Certificate_Model_CertificatePerson();
		$course = new Station_Model_Course();
		
		$certificate_person->delete( $person_id , $certificate_id );
		
		$course_average = $course->find( $user->course_id )->current();
		
		$groups = $this->fetchAll( array( 'discipline_id =?' => $user->discipline_id ) );
		
		foreach ( $groups as $group ) {
			$note = array();
        	$evaluation_note = array();
        
	    	foreach ( $group->findDependentRowset('Bulletin_Model_Bulletin') as $key => $bulletin ){
				$count = $key + 1;
				$note[$bulletin->id]            = $bulletin->findDependentRowset( 'Bulletin_Model_BulletinNote' , null , $bulletin->select()->where( 'person_id =?' , $person_id ) )->current()->note;
				$evaluation_note[$bulletin->id] = $bulletin->findDependentRowset( 'Evaluation_Model_EvaluationNote' , null , $bulletin->select()->where( 'person_id =?' , $person_id ) )->current()->note;
	    	}
			$merge = array_merge( $note , $evaluation_note );
			
			$sum = array_sum( $merge );
			$average = round( $sum / $count , 2 );
			
			if ( $average < $course_average->average ){
				return false;			
			}
		}
	
		$save['Certificate_Model_CertificatePerson']['certificate_id'] = $certificate_id;
		$save['Certificate_Model_CertificatePerson']['person_id']      = $person_id;
		
		$certificate_person->save( $save );
	}
	
	public function _save()
	{
		$user = new Zend_Session_NameSpace( 'user' );
		
		$this->_data['Bulletin_Model_BulletinGroup']['person_id']     = $user->person_id;
		$this->_data['Bulletin_Model_BulletinGroup']['discipline_id'] = $user->discipline_id;
	}
	
	
}

?>
