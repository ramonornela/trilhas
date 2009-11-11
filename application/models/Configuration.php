<?
class Application_Model_Configuration extends Application_Model_Abstract
{
	protected $_name    = "configuration";
	protected $_primary = "id";	
	
	protected $_dependentTables = array();
	
	protected $_referenceMap = array(
		array(
			 'refTableClass' => 'Station_Model_Course',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'course_id' )
		),
		array(
			 'refTableClass' => 'Station_Model_Discipline',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'discipline_id' )
		),
		array(
			 'refTableClass' => 'Station_Model_ClassModel',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'class_id' )
		)
	);
	
	public function deleteLayout( $optionLayout , $optionValue  )
    {
		$db = $this->getAdapter();
		
		$where = $db->quoteInto( "name = ? " , 'layout_organization' );
		$where .= " AND " . $db->quoteInto( $optionLayout.' = ?' , $optionValue );
		
		$db->delete( "trails.configuration" , $where );
    }
	
    /**
     * loadConfig: loads pre-set configuration 
     * of modules stipulated in the modules and 
     * update or insert in Session.
     * 
     * @param object $user
     * @return void
     */
    public function loadConfig( $user )
	{
        $user->config = null;

		$where = $this->select()->where( "course_id = ?" , $user->course_id )
									  		   ->orWhere( "( discipline_id = ?" , $user->discipline_id )
									  		   ->orWhere( "class_id = ? )" , $user->group_id )
									  		   ->where( " name = ? ", "layout_organization" )
									  		   ->order( array( "course_id" , "discipline_id" , "class_id" ) );
		
		$result = $this->fetchAll( $where );
		foreach( $result as $rs ){
			$user->config->{$rs->name} = Zend_JSON::decode( $rs->value );
		}
	}
}