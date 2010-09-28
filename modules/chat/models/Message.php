<?php
class Message extends Table
{
	protected  $_name = 'trails_message';
    protected  $_primary = 'id';
    
    public $filters = array(
		'*'  => 'StringTrim',
		'id' => 'Int'
	);
	
    public $validators = array(
	    'id'       => array('Int'),
		'ds'	   => array( 'NotEmpty' , array( "StringLength" , 0 , 1600  ) ),
    	'person_receiver_id' => array('Int')
	);
	
    protected $_referenceMap = array(
    	"Person_Receiver" => 
		array(
			 'refTableClass' => 'Person',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'person_receiver_id' )
		),
		
		"Person_Sender" => 
		array(
			 'refTableClass' => 'Person',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'person_sender_id' )
		)
	);
	
	public function count( $id )
	{
		$db = $this->getAdapter();

		$select = $db->select();
		
		$select->from( array ( 'r' => "trails_message" ) , "COUNT(0) as count" )
			   ->where( "r.person_receiver_id = ?" , $id );
		
		$row = $db->fetchAll( $select );
	
		return $row[0]['count'];
	}
	
    public function showByReportComunication( $turma , $usuario )
	{
		$db = $this->getAdapter();
		$objSelect = $db->select();

		$objSelect->from( $this->_name , array( 'usuario_id_remetente' , 'usuario_id_destinatario' ) )
				  ->where( 'turma_id = ?' , $turma )
				  ->where( '( usuario_id_remetente = ?' , $usuario )
				  ->orWhere( "usuario_id_destinatario = ? )" , $usuario );

		return $db->fetchAll( $objSelect );
	}
}