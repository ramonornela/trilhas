<?php
class EvaluationNote extends Table
{
	protected $_name    = "trails_evaluation_note";
	protected $_primary = array( "evaluation_id" , "person_id" ); 
	
	protected $_referenceMap = array( 
		array(
			'refTableClass' => 'Bulletin',
			'refColumns'	=> array( 'item' ),
			'columns'		=> array( 'evaluation_id' )
		),
		array(
			'refTableClass' => 'Evaluation',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'evaluation_id' )
		),
		array(
			'refTableClass' => 'Group',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'group_id' )
		),
        array(
			'refTableClass' => 'Person',
			'refColumns'	=> array( 'id' ),
			'columns'		=> array( 'person_id' )
		)
	);
	
	public function deleteRel( $person_id , $evaluation_id )
    {
		$db = $this->getAdapter();
		
		$where = $db->quoteInto( "person_id = ?" , $person_id );
		$where .= " AND " . $db->quoteInto( "evaluation_id = ?" , $evaluation_id );
		
		$db->delete( "trails_evaluation_note" , $where );
    }

}