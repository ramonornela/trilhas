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
		)
	);
	
	public function delete( $evaluation_id , $person_id )
    {
		$db = $this->getAdapter();
		
		$where = $db->quoteInto( "evaluation_id = ? AND " , $evaluation_id  );
		$where .= $db->quoteInto( "person_id = ?" , $person_id );
		
		return $db->delete( $this->_name , $where );
    }
	 
}