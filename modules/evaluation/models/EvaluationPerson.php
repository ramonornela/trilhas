<?php
class EvaluationPerson extends Table
{
	protected $_name    = "trails_evaluation_person";
	protected $_primary = array( 'evaluation_id' , 'person_id' );


	protected $_referenceMap = array(
		array(
			 'refTableClass' => 'Person',
			 'refColumns' => array( 'id' ),
			 'columns' => array( 'person_id' )
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
	);

    public function fetchEvaluated($evaluation_id)
    {
        $user = new Zend_Session_Namespace( "user" );

        $select = $this->select()->distinct();
        
        $select->from( array( 'ep' => $this->_name ) , new Zend_Db_Expr( 'ep.evaluation_id, ep.person_id, ep.dt' ) )
               ->join( array ( "pg" => "trails_person_group" ) , "pg.person_id = ep.person_id" , array() )
               ->join( array ( "p" => "trails_person" ) , "p.id = ep.person_id" , array() )
               ->where( "pg.role_id = ?" , Role::STUDENT )
               ->where( "pg.group_id = ?" , $user->group_id )
               ->where( 'ep.evaluation_id =?' , $evaluation_id )
               ->group(array('ep.evaluation_id', 'ep.person_id', 'ep.dt'))
               ->limit(100)
               ->order('dt DESC, p.name');

        if(!empty($_POST['search'])){
            $select->where( 'UPPER(p.name) LIKE UPPER(?)' , "%{$_POST['search']}%" );
        }

        return $this->fetchAll( $select );
    }
}