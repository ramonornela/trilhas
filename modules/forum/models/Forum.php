<?php
class Forum_Model_Forum extends Forum_Model_Abstract
{
    protected $_name = 'forum';
    protected $_primary = 'id';
    
	protected $_dependentTables = array("Forum_Model_Forum",
										"Forum_Model_ForumSubscribe",
										"Bulletin_Model_Bulletin" );
	
    protected $_referenceMap = array( 
		array(
			 'refTableClass' => 'Share_Model_Person',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'person_id' )
		),
		array(
			 'refTableClass' => 'Application_Model_Status',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'status' )
		),
		array(
			 'refTableClass' => 'Forum_Model_Forum',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'forum_id' )
		)
	);

    public function closeExpired()
    {
        $result = $this->fetchAll( array( 'finished < ? AND finished IS NOT NULL' => date('Y-m-d') ) );
        
        if( $result->count() ){
            foreach( $result as $rs ){
				$data = array(
					"Forum_Model_Forum"=>array(
						'id'=>$rs->id,
						'status'=>Application_Model_Status::CLOSED
					)
				);
                $this->save( $data );
            }
        }
    }

    public function _save()
    {
        $user = new Zend_Session_Namespace('user');
        $this->_data['Forum_Model_Forum']['person_id']  = $user->person_id;

        if ($this->_data['Forum_Model_Forum']['forum_id'] == 0) {
            unset($this->_data['Forum_Model_Forum']['forum_id']);
        }
    }
}