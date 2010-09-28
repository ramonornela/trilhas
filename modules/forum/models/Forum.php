<?php
class Forum extends Table
{
    protected $_name = 'trails_forum';
    protected $_primary = 'id';
	
    public $filters = array(
		'*'	 => 'StringTrim',
	    'id' => 'Int'
	);
		
	public $validators = array(
		'person_id'	=> array( 'NotEmpty' , 'Int' ),
		'ds' 	 	=> array( 'NotEmpty' ),
		'title'	 	=> array( array( 'StringLength', 0 , 255 ) ),
        'started'   => array( array('Date', "Y-m-d" ) , array( "dateDependency" , "finished_forum" ) ),
		'finished'  => array( array('Date', "Y-m-d")),
	);
    
	protected $_dependentTables = array( "Forum" , "ForumSubscribe" , "Bulletin" );
	
    protected $_referenceMap = array( 
		array(
			 'refTableClass' => 'Person',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'person_id' )
		),
		array(
			 'refTableClass' => 'Status',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'status' )
		),
		array(
			 'refTableClass' => 'Forum',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'forum_id' )
		)
	);
	
	public function countComment( $forum_id )
    {
    	$db = $this->getAdapter();
    	
		$select = $db->select();

		$select->from( array ( "f" => $this->_name ) , "COUNT(0) as count" )
				  ->where( 'f.forum_id = ?' , $forum_id )
				  ->orWhere( 'f.forum_resposta_id = ?' , $forum_id );
		
		$result = $db->fetchAll($select);
		
		return  $result[0]['count'];
    }
    
    public function showByWord( $sWord ) 
    {
		$db = $this->getAdapter();

		$select = $db->select();

		$select->from($this->_name, '*')
			   ->where('UPPER(forum_titulo) LIKE UPPER(?)', "%$sWord%")
			   ->orWhere('UPPER(forum_ds) LIKE UPPER(?)', "%$sWord%")
			   ->where('curso_id = ?', $_SESSION['course']['id'])
			   ->order("forum_id");

		return $db->fetchAll($select);
	}

	public function showByReportComunication( $turma , $usuario ) 
	{
		$db = $this->getAdapter();
		$select = $db->select();

		$select->from( $this->_name  , array( 'forum_id' , 'forum_resposta_id' ) )
				  ->where( 'turma_id = ?' , $turma )
				  ->where( 'usuario_id = ?' , $usuario );

		return $db->fetchAll( $select );
	}

    public function closeExpired(){
        $result = $this->fetchAll( array( 'finished < ? AND finished IS NOT NULL' => date('Y-m-d')));

        if( $result->count() ){
            foreach( $result as $rs ){
                $this->save( array( 'id' => $rs->id ,  'status' => 7 ) );
            }
        }
    }
}